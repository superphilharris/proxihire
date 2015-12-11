#!/bin/bash

APACHE_CONFIG=${APACHE_CONFIG:-/etc/apache2/apache2.conf}
APACHE_ENVVARS=${APACHE_ENVVARS:-/etc/apache2/envvars}
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
			-f|--flags)
				FLAGS="$2"
				shift
				;;
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
		"npm")
			if [ `npm list -g $PACKAGE | grep $PACKAGE | wc -l` == "0" ]; then
				echo "$PACKAGE isn't installed."
				if ! sudo npm install -g "$PACKAGE"; then
					echo "Could not install $PACKAGE."
					return 1
				fi
			fi
			;;
		"composer")
			if [ ! -d "vendor/$(sed 's/^\([^:]*\).*/\1/' <<< $PACKAGE)" ]; then
				echo "$PACKAGE isn't installed."
				if ! php composer.phar require $FLAGS "$PACKAGE"; then
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

# ## Install other dependencies

# install vim for vimdiff (used in this setup script).
install_pkg vim || exit 1

# core web services
install_pkg apache2 || exit 1
install_pkg php5 || exit 1
install_pkg php5-mysql || exit 1

# node js for server side scripting
install_pkg nodejs || exit 1
install_pkg npm || exit 1

# ZFTool
install_pkg -m composer zendframework/zftool:dev-master || exit 1

# mysql
install_pkg mysql-client-core-5.6 || exit 1
install_pkg mysql-client-5.6 || exit 1
install_pkg mysql-server-5.6 || exit 1

# Install phpunit
install_pkg phpunit || exit 1
install_pkg -f "--dev" -m "composer" "phpunit/phpunit" || exit 1

# Install imagemagick for converting scraped images into squares
install_pkg imagemagick || exit 1

# Install jsonlint for printing out a nice json message when a json file is not valid
install_pkg -m npm jsonlint || exit 1

#-------------------------------------------------------------------------------
# # APACHE SET-UP
# enable required modules
sudo a2enmod php5
sudo a2enmod rewrite

check_config "$APACHE_CONFIG" || exit 1
check_config "$APACHE_ENVVARS" || exit 1
check_config "$APACHE_SITE_CONFIG" || exit 1
check_config "$APACHE_PHP_CONFIG" || exit 1

create_symbolic_link "${PWD}/public" "/var/www"

# disable all sites
for site in $(ls /etc/apache2/sites-enabled); do
	sudo a2dissite $site || exit 1
done

# enable rentstuff
sudo a2ensite rentstuff.conf || exit 1

# reload apache
sudo service apache2 reload || exit 1

#-------------------------------------------------------------------------------
# # MYSQL SET-UP

function get_mysql_passopt_from_file
{
	file="$1"
	user="$2"
	grep_arg_for_line="$3"
	sed_arg_for_line="$4"

	if [ -e "$file" ]; then
		password="$( grep -F "$grep_arg_for_line" "$file" | sed "$sed_arg_for_line" )"

		# If we found a password, then try to access the database using it
		if [ -n "$password" ]; then
			password_option=" -p$password"
		fi

		if mysql -u $user $password_option <<< "quit"; then
			echo "$password_option"
		else
			# if we failed to log in, then prompt for password
			echo " -p"
		fi
	else
		# if we failed to log in, then prompt for password
		echo " -p"
	fi
}

# ### ROOT PASSWORD
# First, try to get the root password from dbv
MYSQL_ROOT_PASSOPT="$( get_mysql_passopt_from_file "public/dbv/config.php" root "define('DB_PASSWORD'," "s/define('DB_PASSWORD', *['\\\"]\\([^'\\\"]*\\)['\\\"].*/\\1/" )"

# if we need to prompt the user for password
if [ "$MYSQL_ROOT_PASSOPT" == " -p" ]; then
	source ${SCRIPT_DIR}/get_mysql_password_option root MYSQL_ROOT_PASSOPT
	# check the root password
	mysql -u root $MYSQL_ROOT_PASSOPT <<< "quit" || exit 1
fi

MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSOPT// -p/}

# ### APACHE PASSWORD
# First, try to get the apache password from config/autoload/db.local.php
MYSQL_APACHE_PASSOPT="$( get_mysql_passopt_from_file "config/autoload/db.local.php" apache "'password'" "s/\\s*[\\\"']password[\\\"']\\s*=> [\\\"']\\([^\\\"']*\)[\\\"'].*/\\1/" )"

# if we need to prompt the user for password
if [ "$MYSQL_APACHE_PASSOPT" == " -p" ]; then
	source ${SCRIPT_DIR}/get_mysql_password_option apache MYSQL_APACHE_PASSOPT
	# check the apache password
	mysql -u apache $MYSQL_APACHE_PASSOPT <<< "quit" || exit 1
fi
[ -n "$MYSQL_APACHE_PASSOPT" ] && MYSQL_APACHE_PASSWORD="${MYSQL_APACHE_PASSOPT// -p/}"


# ### CHECK THE DATABASE

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

while ! grep -F "define('DB_PASSWORD', '$MYSQL_ROOT_PASSWORD');" "public/dbv/config.php" > /dev/null 2>&1;
do
	if [ ! -e "public/dbv/config.php" ]
	then
		echo "Setting up database versioning. This can be accessed at 'http://<servername>/dbv'."
		echo "You will need to enter the MYSQL root password into the dbv config file."
		cp public/dbv/config.php.sample public/dbv/config.php
	else
		echo "It appears as though the root password in the dbv config file is incorrect."
	fi
	read -p "Press [enter] to config it." -s
	vim -c ":set hlsearch" -c "/'DB_PASSWORD', '[^']*'" public/dbv/config.php
	echo
	echo
done

# Give apache write permissions to the data folder
find public/dbv/data -type d | xargs sudo chown -R $USER:www-data || exit 1
find public/dbv/data -type d | xargs sudo chmod -R g+s || exit 1
find public/dbv/data -type f | xargs sudo chmod 664 || exit 1

#-------------------------------------------------------------------------------
# # Set up DB for Zend

cat <<EOF > /tmp/db.local.php.$$
<?php
return array(
	'db' => array(
		'driver'         => 'Pdo',
		'username'       => 'apache',
		'password'       => '$MYSQL_APACHE_PASSWORD',
		'dsn'            => 'mysql:dbname=RentStuff;host=localhost',
		'driver_options' => array(
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \\'UTF8\\''
		)
	),
);
?>
EOF

if [ ! -e "config/autoload/db.local.php" ]; then
	cp "/tmp/db.local.php.$$" "config/autoload/db.local.php"
elif [ -n "$( diff "/tmp/db.local.php.$$" "config/autoload/db.local.php" )" ]; then
	echo "The file 'config/autoload/db.local.php' differs from the standard template."
	read -s -p "Press [enter] to compare them: "
	$DIFF_VIEWER "config/autoload/db.local.php" "/tmp/db.local.php.$$"
fi

rm "/tmp/db.local.php.$$"

#-------------------------------------------------------------------------------
# # Set up git post-merge hook

cat <<EOF > /tmp/post-merge
#!/bin/bash
SCRIPT_DIR="\$(cd "\$(dirname "\${BASH_SOURCE[0]}")" && pwd)"
source \$SCRIPT_DIR/../../.githooks/post-merge
EOF
chmod 444 /tmp/post-merge

if [ ! -f ${SCRIPT_DIR}/../.git/hooks/post-merge ] || ! diff ${SCRIPT_DIR}/../.git/hooks/post-merge /tmp/post-merge >/dev/null; then
	echo "$(dirname ${SCRIPT_DIR})/.git/hooks/post-merge is not up to date."
	echo "Press [enter] to merge the changes."
	read
	$DIFF_VIEWER "${SCRIPT_DIR}/../.git/hooks/post-merge" /tmp/post-merge
	chmod +x "${SCRIPT_DIR}/../.git/hooks/post-merge" 
fi

chmod 644 /tmp/post-merge
rm /tmp/post-merge

