#!/bin/bash

APACHE_CONFIG=${APACHE_CONFIG:-/etc/apache2/apache2.conf}
APACHE_SITE_CONFIG=${APACHE_SITE_CONFIG:-/etc/apache2/sites-available/rentstuff.conf}

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
						exit 1
					fi
				fi
			fi
			;;
		#"npm")
			#if [ ! -d "node_modules/$PACKAGE" ]; then
				#echo "$PACKAGE isn't installed."
				#if ! sudo npm install "$PACKAGE"; then
					#echo "Could not install $PACKAGE."
					#exit 1
				#fi
			#fi
			#;;
		"composer")
			if [ ! -d "vendor/$(sed 's/^\([^:]*\).*/\1/' <<< $PACKAGE)" ]; then
				echo "$PACKAGE isn't installed."
				if ! php composer.phar require "$PACKAGE"; then
					echo "Could not install $PACKAGE"
					exit 1
				fi
			fi
			;;
		*)
			echo "Unknown package manager $MANAGER"
			exit 1
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
			sudo mv "${NAME}" "${NAME}.bak" || exit 1
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
#install_pkg npm || exit

# ## Install other dependencies

# install vim for vimdiff (used in this setup script).
install_pkg vim || exit

# core web services
install_pkg apache2 || exit
install_pkg php5 || exit

# ZFTool
install_pkg -m composer zendframework/zftool:dev-master || exit

#-------------------------------------------------------------------------------
# # APACHE SET-UP
# enable required modules
sudo a2enmod php5
sudo a2enmod rewrite

check_config "$APACHE_CONFIG" || exit
check_config "$APACHE_SITE_CONFIG" || exit

create_symbolic_link "${PWD}/public" "/var/www"

# disable all sites
for site in $(ls /etc/apache2/sites-enabled); do
	sudo a2dissite $site
done

# enable rentstuff
sudo a2ensite rentstuff.conf

# reload apache
sudo service apache2 reload
