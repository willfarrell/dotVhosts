<?php

class vhosts {
	private $config;
	private $db;
	private $errors;
	
	private $hosts;
	private $httpd;
	private $nginx;
	
	function __construct() {
		$this->config = json_decode(file_get_contents('json/config.json'), true);
		$this->db = json_decode(file_get_contents('json/db.json'), true);
		$this->password = file_get_contents('.password');
		
		//$this->httpd = file_get_contents($this->config['httpd']);
		//$this->nginx = file_get_contents($this->config['nginx']);
		
		$this->sync();
	}
	
	function __destruct() {
		//save('db.json', json_encode($this->db));
		file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'json/db.json', json_encode($this->db));
		$this->writeVhosts();
		$this->writeHosts();
		$this->writeApacheUser();
	}
	
	public function db() {
		echo json_encode($this->db);
	}
	
	public function add($request_data) {
		// delete old
		if (isset($request_data['id'])) {
			$this->delete($request_data['id']);
		}
		
		$request_data['DocumentRoot'] = str_replace("~", substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), '/', 7)), $request_data['DocumentRoot']);
		
		// save new
		$ID = $this->makeID($request_data['ServerName']);
		$request_data['id'] = $ID;
		$this->db[$ID] = $request_data;
		$this->db[$ID]['enabled'] = 1;
		echo $ID;
	}
	
	public function delete($id) {
		unset($this->db[$id]);
	}
	/*
		if (isset($_REQUEST['get'])) {
			$return =  $this->get();
		}
		if (isset($_REQUEST['rawdata'])) {
			//$return =  $this->rawdata();
		}
		$return =  $this->rawdata();
		
	}*/
	
	/**
	 *	Sync hosts and httpd with db.json
	 *
	 *
	 */
	private function sync() {
		// parse Globals
		$this->parseVhosts($this->config['vhosts']);
		$this->parseHosts($this->config['hosts']);
		//$this->parseNginx();
		
		// parse .vhost files
		$this->parseVhostFiles();
	}
	
	private function parseHosts($file) {
		if (!file_exists($file)) { return; }
		$data = file_get_contents($file);
		
		$lines = explode("\n", $data);
		
		foreach($lines as $line) {
			if ($line && substr($line, 0,1) != "#") { // not blank and not commented
				preg_match("/([\d]+.[\d]+.[\d]+.[\d]+|[^\s]*)[\s]+(.*)/i", $line, $matches);
				$ip = $matches[1];
				$domain = trim($matches[2]);
				$ID = $this->makeID($domain);
				if ($ip == "127.0.0.1" && !isset($this->db[$ID])) {
					$this->db[$ID]["port"] = 80;
					$this->db[$ID]["ServerName"] = $domain;
					$this->db[$ID]["DocumentRoot"] = "";
					
					$this->db[$ID]["id"] = $ID;
					$this->db[$ID]["enabled"] = "0";
				}
			}
		}
	}
	
	private function parseVhosts($file, $import = "0") {
		if (!file_exists($file)) { return; }
		$data = file_get_contents($file);
		// replace ~ with /Users/username
		$data = str_replace("~", substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), '/', 7)), $data);
		// replace __DIR__ with folder
		$data = str_replace("__DIR__", dirname($file), $data);
		
		preg_match_all("/<VirtualHost \*:([\d]+)>([\s\S]*?)<\/VirtualHost>/i", $data, $matches); // |<VirtualHost \*>([\s\S]*?)<\/VirtualHost>
		$ports = $matches[1];
		$VirtualHosts = $matches[2];
		for($i = 0, $l = count($VirtualHosts); $i < $l; $i++) {
			// pull out keys
			preg_match_all("/(ServerName|ServerAdmin|ServerAlias|ServerPath|DocumentRoot) (.*)\n/i", $VirtualHosts[$i], $matches);
			$vhosts = array_combine($matches[1], $matches[2]);
			$ID = $this->makeID($vhosts['ServerName']);
			$this->db[$ID]['id'] = $ID;
			
			foreach($vhosts as $key => $value) {
				$this->db[$ID][$key] = str_replace("\"", "", $value);
			}
			
			// remove <Directory>
			/*preg_match_all("/<([\w]+).*?>([\s\S]*?)<\/[\w]+>/i", $VirtualHosts[$i], $matches);
			$sub_ID = $matches[1];
			$sub_OBJ = $matches[2];
			for($j = 0; $j < count($sub_ID); $j++) {
				$this->db[$ID][$sub_ID[$j]] = array();
				// pull out keys
				preg_match_all("/([\w]+) (.*)\n/i", $sub_OBJ[0], $matches);
				$sub_ARR = array_combine($matches[1], $matches[2]);
				foreach($sub_ARR as $key => $value) {
					$this->db[$ID][$sub_ID[$j]][$key] = str_replace("\"", "", $value);
				}
			}*/
			
			$this->db[$ID]['port'] = (int)$ports[$i];
			$this->db[$ID]['enabled'] = "1";
			$this->db[$ID]['import'] = $import; // bool for imported from .vhost file
			if (!isset($this->db[$ID]['timestamp'])) {
				$this->db[$ID]['timestamp'] = $_SERVER['REQUEST_TIME'];
			}
			
			/*if (!isset($this->db[$vhosts['ServerName']]['ip'])) {
				$this->db[$vhosts['ServerName']]['ip'] = "127.0.0.1";
			}*/
		}
	}
	
	private function parseVhostFiles() {
		foreach($this->config['dirs'] as $dir) {
			//$this->php_grep(str_replace("~", dirname(__FILE__)."/..", $dir), 2);
			$dir = str_replace("~", substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), '/', 7)), $dir);
			$this->php_grep($dir, 2);
		}
	}
	
	function php_grep($path, $level){
		if (substr($path, -1) != "/") { $path .= "/"; }
		
		//$perms = fileperms($this->config['hosts']);
		//$this->chmod($this->config['hosts'], 0744);
		
		$fp = opendir($path);
		while($f = readdir($fp)){
			if( preg_match("#^\.+$#", $f) ) { continue; }		// ignore symbolic links
			if (substr($f, 0,1) == ".") { continue; }
			//if( in_array($f.'/', $this->config['black_folders']) ) { continue; }	// open source projects or system files
			
			$file_full_path = $path.$f;
			if(is_dir($file_full_path)) {
				if ($level - 1) {
					$this->php_grep($file_full_path, ($level - 1));
				}
				$file = $file_full_path."/.vhosts";
				if (!file_exists($file)) { continue; }
				$this->parseVhosts($file, "1");
			} else if ($f == ".vhosts") {
				$this->parseVhosts($file_full_path, "1");
			}
		}
		//$this->chmod($this->config['hosts'], $perms);
	}
	
	function writeVhosts() {
		$ignore = array("id", "enabled", "import");
		//$directives = array("ServerName", "ServerAdmin", "ServerAlias", "ServerPath", "DocumentRoot");
		$data = "# Virtual Hosts\n\n"
				."# Please see the documentation at\n"
				."# <URL:http://httpd.apache.org/docs/2.2/vhosts/>\n\n"
				."NameVirtualHost *:80\n\n";
		
		foreach($this->db as $vhost) {
			if ($vhost['enabled'] == "0") { continue; }
			$port = (strpos($vhost['ServerName'], "localhost") !== false) ? $vhost['port'] : $this->config['ports']['apache'];
			$data .= "<VirtualHost *:".$port.">\n";
			
			foreach($vhost as $key => $value) {
				if (in_array($key, $ignore)) { continue; }
				if (is_string($value)) {
					$data .= "    $key $value\n";
				} else if (is_array($value)) {
					$data .= "    <$key ".$vhost['DocumentRoot'].">\n";
					//$data .= "        Options +Indexes +FollowSymLinks\n"; // https://httpd.apache.org/docs/current/mod/core.html#options
					//$data .= "        DirectoryIndex index.html index.php\n";
					foreach($value as $k => $v) {
						$data .= "        $k $v\n";
					}
					$data .= "    </$key>\n";
				}
			}
			$data .= "</VirtualHost>\n\n";
			
		}
		
		$perms = fileperms($this->config['vhosts']);
		$this->chmod($this->config['vhosts'], 0766);
		file_put_contents($this->config['vhosts'], $data);
		$this->chmod($this->config['vhosts'], $perms);
	}
	
	function writeHosts() {
		$data = "##\n"
				."# Host Database\n"
				."#\n"
				."# localhost is used to configure the loopback interface\n"
				."# when the system is booting.  Do not change this entry.\n"
				."##\n"
				."127.0.0.1	localhost\n"
				."255.255.255.255	broadcasthost\n"
				."::1             localhost\n"
				."fe80::1%lo0	localhost\n\n";
		
		foreach($this->db as $vhost) {
			if ($vhost['ServerName'] == "localhost") { continue; }
			
			$data .= "127.0.0.1 ".$vhost['ServerName']."\n";
			
		}
		
		$perms = fileperms($this->config['hosts']);
		$this->chmod($this->config['hosts'], 0766);
		file_put_contents($this->config['hosts'], $data);
		$this->chmod($this->config['hosts'], $perms);
	}
	
	function writeApacheUser() {
		$data = "";
		
		foreach($this->config['dirs'] as $dir) {
			//$this->php_grep(str_replace("~", dirname(__FILE__)."/..", $dir), 2);
			$dir = str_replace("~", substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), '/', 7)), $dir);
			$data .= "<Directory \"$dir\">\n"
					."    Options Indexes MultiViews\n"
					."    AllowOverride None\n"
					."    Order allow,deny\n"
					."    Allow from all\n"
					."</Directory>\n\n";
		}
		
		$name = substr(dirname(__FILE__), 7, strpos(dirname(__FILE__), '/', 8) - 7);
		$file = "/private/etc/apache2/users/".$name.".conf";
		
		$perms = fileperms($file);
		$this->chmod($file, 0766);
		file_put_contents($file, $data);
		$this->chmod($file, $perms);
	}
	
	private function makeID($str) {
		return substr(md5($str), 0, 6);
	}
	
	private function chmod($file, $value) {
		exec("echo {$this->password} | sudo -S chmod $value $file", $output, $return);
		//echo $output; echo $return;
	}
}

?>