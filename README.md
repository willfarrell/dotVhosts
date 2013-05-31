# .vhosts
Lightweight VirtualHost manager for Mac. A MAMP alternative.

## Features
- Add and modify localhost VirtualHosts
- Autoload `.vhosts` files from project folders and instert into global `vhosts.conf`

![Alt text][screenshot]

## Install
1. `curl -s https://raw.github.com/willfarrell/.vhosts/master/setup.sh -o vhosts.sh && bash vhosts.sh && rm vhosts.sh`

### MySQL
Download from [dev.mysql.com](https://dev.mysql.com/downloads/mysql/)

## Terminal
### Apache
sudo apachectl -k start
sudo apachectl -k restart
sudo apachectl -k stop

### nginx
sudo nginx
sudo nginx -s stop

## Roadmap
- nginx support
- debug on clean machine

## .vhosts file
1. make sure dir is readable by `everyone`


***

- [Hosts.prefpane](https://github.com/specialunderwear/Hosts.prefpane/downloads)


[screenshot]: ./screenshots/screenshot.png "Screenshot of .vhosts Dashboard"