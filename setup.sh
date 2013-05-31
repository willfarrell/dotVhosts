#!/usr/bin/env bash

log()  { printf "$*\n" ; return $? ;  }

create_home() {
	def_vhosts_home="${HOME}/.vhosts"
	vhosts_home=${INSTALL_PATH:-$def_vhosts_home}

	mkdir -p "${vhosts_home}" && cd $_
}

download_files() {
	log "Downloading repo files"
	
	curl -L https://github.com/willfarrell/.vhosts/tarball/0.0.4 | tar xz
	# copy contents into .vhosts folder
	for FILE in `ls`
	do
		if test -d $FILE
		then
			shopt -s dotglob nullglob # grab dot files for move
			mv ./$FILE/* ./
			rm -rf $FILE
		fi
	done
}

create_home

# get password for sudos
read -p "Password:" -s password

download_files

log "Updating Apache settings"
sed -i.bak "s@^#LoadModule php5_module@LoadModule php5_module@g" /private/etc/apache2/httpd.conf
sed -i.bak "s@^#Include /private/etc/apache2/extra/httpd-vhosts.conf@Include /private/etc/apache2/extra/httpd-vhosts.conf@" /private/etc/apache2/httpd.conf

log "Startings up Apache"
echo -n $password | sudo -S apachectl start # -k

log "Building .vhosts DB"
echo -n $password > .password # Save password for future sudo
php index.php

log "Restartings up Apache"
echo -n $password | sudo -S apachectl restart # incase already started

# Add Apache to LaunchDaemon
echo -n $password | sudo -S mv com.apache.apachectl.plist /Library/LaunchDaemons/

chmod 0766 json/db.json

log "Done!"