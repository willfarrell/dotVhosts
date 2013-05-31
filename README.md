# .vhosts
Lightweight VirtualHost manager for Mac. 

## Features
- Add and modify localhost VirtualHosts
- Autoload `.vhosts` files from project folders and instert into global `vhosts.conf`

## Install
Just run `curl -s https://raw.github.com/willfarrell/.vhosts/master/setup.sh | bash` in your terminal.

or

1. Copy repo files into `~/.vhosts` or `/Users/username/.vhosts`
2. Un-comment `LoadModule php5_module libexec/apache2/libphp5.so` and `Include /private/etc/apache2/extra/httpd-vhosts.conf` in `/private/etc/apache2/httpd.conf`
3. Copy contensts of `~/.vhosts/.vhosts` into `/private/etc/apache2/extra/httpd-vhosts.conf`
4. Start Apache `sudo apachectl -k start`
5. Visit `http://vhosts.localhost`

## Advanced Install
### Start Apache on login
- Add `com.apache.apachectl.plist` to `/Library/LaunchDaemons/`

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
- apache & mysql toggle
- one line bash install `curl -s https://raw.github.com/willfarrell/.vhosts/master/setup.sh | bash`
- username.conf - http://www.coolestguyplanettech.com/forbidden-403-you-dont-have-permission-to-access-username-on-this-server/

## .vhosts file
1. make sure dir is readable by `everyone`


***

- [Hosts.prefpane](https://github.com/specialunderwear/Hosts.prefpane/downloads)