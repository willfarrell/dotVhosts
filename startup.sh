#!/usr/bin/env bash

# Setup
cd ${HOME}/.vhosts

set -- $(<.password) # $1 == pasword

echo $1 | sudo -v
# Keep-alive: update existing `sudo` time stamp until bootstrap has finished
while true; do sudo -n true; sleep 60; kill -0 "$$" || exit; done 2>/dev/null &

# Permissions
sudo chmod u+w www/json/db.json

sudo chmod u+w /private/etc/hosts
sudo chmod u+w /private/etc/apache2/extra/httpd-vhosts.conf
sudo chmod -R u+w /private/etc/apache2/users
sudo chmod u+w /usr/local/etc/nginx/nginx.conf

# Start Services
sudo apachectl start
#sudo apachectl restart
#mysql start