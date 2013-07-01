#!/usr/bin/env bash

log()  { printf "$*\n" ; return $? ;  }

### Setup ###
clear
log "Setting up .vhosts"
cd ${HOME}
rm -rf .vhosts
mkdir -p ${HOME}/.vhosts && cd $_

# get password for sudos
read -p "Password:" -s password

echo $password | sudo -v
# Keep-alive: update existing `sudo` time stamp until bootstrap has finished
while true; do sudo -n true; sleep 60; kill -0 "$$" || exit; done 2>/dev/null &

### Functions ###

download_files() {
	log "Downloading repo files"
	
	curl -#L https://github.com/willfarrell/.vhosts/tarball/master | tar xzv --strip-components 1 --exclude={README.md,bootstrap.sh,screenshots}
	# cd ${HOME}/Dev/ && tar -czv dotVhosts/* | (cd ${HOME}/.vhosts; tar xz -) && cd ${HOME}/.vhosts ### uncomment - local testing only
	
	# copy contents into .vhosts folder
	#for FILE in `ls`
	#do
	#	if test -d $FILE
	#	then
	#		shopt -s dotglob nullglob # grab dot files for move
	#		mv ./$FILE/* ./
	#		rm -rf $FILE
	#	fi
	#done
}

### Process ###
log ""
download_files
echo -n $password > www/.password # Save password for future sudo

# Permissions
sudo chmod u+w www/json/db.json

sudo chmod u+w /private/etc/hosts
sudo chmod u+w /private/etc/apache2/extra/httpd-vhosts.conf
sudo chmod -R u+w /private/etc/apache2/users
sudo chmod u+w /usr/local/etc/nginx/nginx.conf

log "Updating Apache settings"
sed -i.bak "s@^#LoadModule php5_module@LoadModule php5_module@g" /private/etc/apache2/httpd.conf
sed -i.bak "s@^#Include /private/etc/apache2/extra/httpd-vhosts.conf@Include /private/etc/apache2/extra/httpd-vhosts.conf@" /private/etc/apache2/httpd.conf

log "Startings up Apache"
sudo apachectl start # -k

log "Building .vhosts DB"
cd www
php index.php

log "Restartings up Apache"
sudo apachectl restart # incase already started

# Add Apache to LaunchDaemon
#sudo mv com.apache.apachectl.plist /Library/LaunchDaemons/
sudo mv com.farrell.vhosts.plist /Library/LaunchDaemons/

# Add mysql alias
echo alias mysql=/usr/local/mysql/bin/mysql >> ~/.bashrc
echo alias mysqladmin=/usr/local/mysql/bin/mysqladmin >> ~/.bashrc

# Setup MySQL socket linking for PHP
cd /var 
sudo mkdir mysql && cd $_
sudo ln -s /tmp/mysql.sock mysql.sock

#mysqladmin -u root -p localhost

log "Done!"