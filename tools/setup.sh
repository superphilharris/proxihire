#!/bin/bash

APACHE_CONFIG=${APACHE_CONFIG:-/etc/apache2/apache2.conf}
APACHE_SITE_CONFIG=${APACHE_SITE_CONFIG:-/etc/apache2/sites-available/rentstuff.conf}
APACHE_PHP_CONFIG=${APACHE_PHP_CONFIG:-/etc/php5/apache2/php.ini}

# If it is installed, use meld as the default diff viewer
if which meld > /dev/null; then
	DIFF_VIEWER=${DIFF_VIEWER:-meld}
fi
DIFF_VIEWER=${DIFF_VIEWER:-vimdiff}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

cd ${SCRIPT_DIR}/..

function confirm {
	prompt=$1
	default=${2:-N}

	case "${default}" in
		y|Y)
			prompt="${prompt} [Y/n]:"
			;;
		n|N)
			prompt="${prompt} [y/N]:"
			;;
		*)
	esac

	while :; do
		read -p "${prompt} " response

		if [ -z "${response}" ]; then
			response=${default}
		fi
		
		case "${response}" in
			y|Y)
				return 0
				;;
			n|N)
				return 1
				;;
			*)
				echo "Unknown option '${response}'."
				;;
		esac
	done
				
}

function install_pkg {
	MANAGER="apt"
	while [[ $# > 0 ]]; do
		case "$1" in
			-m|--manager)
				MANAGER="$2"
				shift
				;;
			*)
				PACKAGE="$1"
				;;
		esac
		shift
	done
	case "${MANAGER}" in
		"apt")
			if ! dpkg --get-selections "$PACKAGE" | grep -v "deinstall" > /dev/null; then
				echo "$PACKAGE isn't installed."
				if confirm "Install it?" y; then
					if ! sudo apt-get install $PACKAGE; then
						echo "Could not install $PACKAGE."
						return 1
					fi
				fi
			fi
			;;
		#"npm")
			#if [ ! -d "node_modules/$PACKAGE" ]; then
				#echo "$PACKAGE isn't installed."
				#if ! sudo npm install "$PACKAGE"; then
					#echo "Could not install $PACKAGE."
					#return 1
				#fi
			#fi
			#;;
		"composer")
			if [ ! -d "vendor/$(sed 's/^\([^:]*\).*/\1/' <<< $PACKAGE)" ]; then
				echo "$PACKAGE isn't installed."
				if ! php composer.phar require "$PACKAGE"; then
					echo "Could not install $PACKAGE"
					return 1
				fi
			fi
			;;
		*)
			echo "Unknown package manager $MANAGER"
			return 1
			;;
	esac
}

function create_symbolic_link {
	TARGET="$1"
	NAME="$2"
	if [ -L "$NAME" ]; then
		if ls -l "$NAME" | grep -F "$NAME -> $TARGET" > /dev/null; then
			# $NAME exists and correctly points to $TARGET
			return 0
		else
			echo "$NAME is a symbolic link, but not pointing to $TARGET."
			if confirm "Fix this?" n; then
				sudo rm "$NAME" || return 1
			else
				return 1
			fi
		fi
	else
		if [ -e "$NAME" ]; then
			echo "$NAME already exists, but is *not* a symbolic link."
			if [ ! -d "$NAME" ]; then
				read -s -p "Press enter to display a diff"
				echo
				$DIFF_VIEWER "$NAME" "$TARGET"
			fi
			if ! confirm "Do you wish to replace $NAME with a symbolic link to $TARGET? (a backup will be taken)"; then
				return 1
			fi
			echo "Backing up ${NAME} to ${NAME}.bak"
			sudo mv "${NAME}" "${NAME}.bak" || return 1
		else
			echo "Configuration file does not exist at $NAME."
			if ! confirm "Do you wish to create it as a link to $TARGET?" y; then
				return 1
			fi
		fi
	fi
	sudo ln -s "$TARGET" "$NAME" || return 1
	echo "$NAME successfully linked to $TARGET" 
}

function check_config {
	while [[ $# > 0 ]]; do
		case "$1" in
			*)
				NAME="$1"
				;;
		esac
		shift
	done

	TARGET="${SCRIPT_DIR}${NAME}"
	if [ ! -e "$TARGET" ]; then
		echo "TARGET configuration file does not exist at ${TARGET}."
		return 1
	fi

	create_symbolic_link "$TARGET" "$NAME"
}

#-------------------------------------------------------------------------------
# # INSTALLS
# ## Install package managers first

# Install composer (php package manager required for zend)
if ! php composer.phar > /dev/null; then
	if ! php ${SCRIPT_DIR}/composer_installer.php; then
		echo "Could not install composer." 
		exit 1
	fi
fi

## Install npm
#install_pkg npm || exit 1

# ## Install other dependencies

# install vim for vimdiff (used in this setup script).
install_pkg vim || exit 1

# core web services
install_pkg apache2 || exit 1
install_pkg php5 || exit 1
install_pkg php5-mysql || exit 1

# ZFTool
install_pkg -m composer zendframework/zftool:dev-master || exit 1

# mysql
install_pkg mysql-server || exit 1

#-------------------------------------------------------------------------------
# # APACHE SET-UP
# enable required modules
sudo a2enmod php5
sudo a2enmod rewrite

check_config "$APACHE_CONFIG" || exit 1
check_config "$APACHE_SITE_CONFIG" || exit 1
check_config "$APACHE_PHP_CONFIG" || exit 1

create_symbolic_link "${PWD}/public" "/var/www"

# disable all sites
for site in $(ls /etc/apache2/sites-enabled); do
	sudo a2dissite $site
done

# enable rentstuff
sudo a2ensite rentstuff.conf

# reload apache
sudo service apache2 reload

#-------------------------------------------------------------------------------
# # MYSQL SET-UP

source ${SCRIPT_DIR}/get_mysql_password_option root MYSQL_ROOT_PASSOPT
# check the root password
mysql -u root $MYSQL_ROOT_PASSOPT <<< "quit" || exit 1

source ${SCRIPT_DIR}/get_mysql_password_option apache MYSQL_APACHE_PASSOPT
[ -n "$MYSQL_APACHE_PASSOPT" ] && \
    mysql_apache_password="${MYSQL_APACHE_PASSOPT// -p/}"

# see if the database `RentStuff` is created
if ! mysql -u root $MYSQL_ROOT_PASSOPT RentStuff <<< "quit" > /dev/null 2>&1; then
	[ -n "$MYSQL_APACHE_PASSOPT" ] && \
		mysql_apache_identified_by=" IDENTIFIED BY '${MYSQL_APACHE_PASSOPT// -p/}'"

	# Create the apache user
	mysql -u root $MYSQL_ROOT_PASSOPT <<< \
"CREATE USER 'apache'@'localhost'${mysql_apache_identified_by};"

	# Create the rentstuff database
	mysql -u root $MYSQL_ROOT_PASSOPT <<< \
"CREATE DATABASE RentStuff;
GRANT SELECT, DELETE, UPDATE, INSERT ON RentStuff.* TO 'apache'@'localhost';" || exit 1

fi

# check the apache password
mysql -u apache $MYSQL_APACHE_PASSOPT <<< "quit" || exit 1

#-------------------------------------------------------------------------------
# # DBV SET-UP

MYSQL_APACHE_PASSWORD=${MYSQL_APACHE_PASSOPT// -p/}
while ! grep -F "define('DB_PASSWORD', '$MYSQL_APACHE_PASSWORD');" "public/dbv/config.php" > /dev/null 2>&1;
do
	if [ ! -e "public/dbv/config.php" ]
	then
		echo "Setting up database versioning. This can be accessed at 'http://<servername>/dbv'."
		echo "You will need to enter the MYSQL root password into the dbv config file."
		cp public/dbv/config.php.sample public/dbv/config.php
	else
		echo "It appears as though the apache password in the dbv config file is incorrect."
	fi
	read -p "Press [enter] to config it." -s
	vim -c ":set hlsearch" -c "/'DB_PASSWORD', '[^']*'" public/dbv/config.php
	echo
	echo
done
