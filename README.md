# .vhosts
Lightweight VirtualHost manager for Mac. A MAMP alternative.

## Features
- Add and modify localhost VirtualHosts
- Autoload `.vhosts` files from project folders and instert into global `vhosts.conf`

![Alt text][screenshot]

## Install
Run in Terminal: `curl -s https://raw.github.com/willfarrell/.vhosts/master/setup.sh -o vhosts.sh && bash vhosts.sh && rm vhosts.sh`

**Requirements:**
1. Apache 2 (Built into Mac OS X)

**Optional:**
1. MySQL [dev.mysql.com](https://dev.mysql.com/downloads/mysql/)
2. redis [redis.io](http://redis.io/download)
3. nginx `brew install nginx`

##.vhosts File
You can add a .vhosts file into the root of your projects file and it will automatically be loaded into your VirtualHosts the next time you visit `http://vhosts.localhost`. Project folders in `~/Sites` are scaned by default, you can add in a custom on by adding to the `dirs` array in `json/config.json`. `__DIR__` will automatically be replaced with the project directory.

### Sample .vhosts File
```bash
# VirtualHosts for Yeoman projects
<VirtualHost *:8888>
    ServerName sample
    DocumentRoot __DIR__
</VirtualHost>

<VirtualHost *:8888>
    ServerName app.sample
    DocumentRoot __DIR__/app
</VirtualHost>

<VirtualHost *:8888>
    ServerName dist.sample
    DocumentRoot __DIR__/dist
</VirtualHost>
```

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