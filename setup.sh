#!/usr/bin/env bash

log()  { printf "$*\n" ; return $? ;  }

create_home() {
	def_vhosts_home="${HOME}/.vhosts"
	vhosts_home=${INSTALL_PATH:-$def_vhosts_home}

	mkdir -p "${vhosts_home}" && cd $_
}

download_files() {
	log "Downloading repo files"
	# https://help.github.com/articles/downloading-files-from-the-command-line
	#curl -o master.zip https://raw.github.com/willfarrell/.vhosts/archive/Archive.zip
	#unzip Archive.zip
	#rm -rf __MACOSX
	#rm -rf Archive.zip
	
	curl -L https://github.com/willfarrell/.vhosts/tarball/master | tar xz
	#tar -xf master.zip
	#mv ./master/* ./
}

# get password for sudos
echo -n Password:
read -s password 

create_home()
download_files()

log "Updating Apache settings"
sed -i.bak "s@^#LoadModule php5_module@LoadModule php5_module@g" /private/etc/apache2/httpd.conf
sed -i.bak "s@^#Include /private/etc/apache2/extra/httpd-vhosts.conf@Include /private/etc/apache2/extra/httpd-vhosts.conf@" /private/etc/apache2/httpd.conf

log "Building .vhosts DB"
php index.php

log "Startings up Apache"
echo $password | sudo -S apachectl start # -k
echo $password | sudo -S apachectl restart # incase already started

# Add Apache to LaunchDaemon
echo $password | sudo -S mv com.apache.apachectl.plist /Library/LaunchDaemons/

# Save password for future sudo
echo $password > .password

log "Done!"