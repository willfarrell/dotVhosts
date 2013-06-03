<?php
//error_reporting(0);

require_once 'class.vhosts.php';
require_once 'class.engine.php';


//echo "<pre>";
$engine = new Engine;
$vhosts = new vhosts;

// Simple rest API
if (!isset($_SERVER['REQUEST_METHOD'])) {
	// calls from terminal
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	//$_GET
	if (isset($_GET['db'])) {
		$vhosts->db();
	} else if (isset($_GET['apache'])) {
		$engine->apache($_GET['apache']);
	} else if (isset($_GET['nginx'])) {
		$engine->nginx($_GET['nginx']);
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
	$_PUT = json_decode(file_get_contents("php://input"), true);
	if (isset($_GET['VirtualHost'])) {
		$vhosts->add($_PUT);
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//$_POST
	
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
	//$_DELETE = json_decode(file_get_contents("php://input"),);
	if (isset($_GET['VirtualHost'])) {
		$vhosts->delete($_GET['VirtualHost']);
	}
}

/*function preg_replace_all($pattern,$replace,$text) {
	while(preg_match($pattern,$text))
		$text = preg_replace($pattern,$replace,$text);
	return $text;
}*/

?>