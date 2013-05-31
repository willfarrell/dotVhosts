# .vhosts
Lightweight VirtualHost manager for Mac. A MAMP alternative.

## Features
- Add and modify localhost VirtualHosts
- Autoload `.vhosts` files from project folders and instert them into global `httpd-vhosts.conf`

![Alt text][screenshot]

## Install
Run in Terminal: `curl -s https://raw.github.com/willfarrell/.vhosts/master/setup.sh -o vhosts.sh && bash vhosts.sh && rm vhosts.sh`

**Requirements:**
1. Apache 2 (Built into Mac OS X)

**Optional:**
1. [Hosts.prefpane](https://github.com/specialunderwear/Hosts.prefpane/downloads)
2. MySQL [dev.mysql.com](https://dev.mysql.com/downloads/mysql/)
3. nginx `brew install nginx`
4. redis [redis.io](http://redis.io/download)


##.vhosts File
You can add a .vhosts file into the root of your projects file and it will automatically be loaded into your VirtualHosts the next time you visit `http://vhosts.localhost`. Project folders in `~/Sites` are scaned by default, you can add in a custom on by adding to the `dirs` array in `json/config.json`. `__DIR__` will automatically be replaced with the project directory.

### Sample .vhosts File
```bash
# VirtualHosts for Yeoman projects
<VirtualHost *:8888>
    ServerName yeoman
    DocumentRoot __DIR__
</VirtualHost>

<VirtualHost *:8888>
    ServerName app.yeoman
    DocumentRoot __DIR__/app
</VirtualHost>

<VirtualHost *:8888>
    ServerName dist.yeoman
    DocumentRoot __DIR__/dist
</VirtualHost>

<VirtualHost *:8888>
    ServerName test.yeoman
    DocumentRoot __DIR__
    <Directory __DIR__>
        DirectoryIndex test/e2e/runner.html
    </Directory>
</VirtualHost>
```
### Flow of Information
![Alt text][process]

## Terminal
### Apache
```bash
sudo apachectl -k start
sudo apachectl -k restart
sudo apachectl -k stop
```

### nginx
```bash
sudo nginx
sudo nginx -s stop
```

## Roadmap
- nginx support
- debug on clean machine of clean install
- yum packege to monitor for new .vhosts files

[process]: ./screenshots/process.png "Flow of VirtualHost settings"
[screenshot]: ./screenshots/screenshot.png "Screenshot of .vhosts Dashboard"