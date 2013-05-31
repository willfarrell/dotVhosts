<?php

class Engine {
	
	function __construct() {
		// load in password
		$this->password = file_get_contents(".password");
	}
	
	function apache($action) {
		switch ($action) {
			case "get":
				//exec("echo $password | sudo -S apachectl start", $output, $return);
				break;
			case "start":
				exec("echo {$this->password} | sudo -S apachectl start", $output, $return);
				break;
			case "restart":
				exec("echo {$this->password} | sudo -S apachectl restart", $output, $return);
				break;
			case "stop":
				exec("echo {$this->password} | sudo -S apachectl stop", $output, $return);
				break;
			default:
				break;
		}
	}
	
	function nginx() {
		switch ($action) {
			case "get":
				//exec("echo $password | sudo -S apachectl start", $output, $return);
				break;
			case "start":
				exec("echo $password | sudo -S nginx", $output, $return);
				break;
			case "restart":
				exec("echo $password | sudo -S nginx -s stop && echo $password | sudo -S nginx", $output, $return);
				break;
			case "stop":
				exec("echo $password | sudo -S nginx -s stop", $output, $return);
				break;
			default:
				break;
		}
	}
	
	function mysql() {
		
	}
}

?>