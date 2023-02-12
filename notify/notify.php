<?php

define('APP_RAN', '');

// Include config file
require_once('../config.php');
  
if (isset($_GET['url']) && isset($_GET['challenge'])) {
  	//http_response_code(200);
  	echo $_GET['challenge'];
}

if (($_SERVER["REQUEST_METHOD"] === "POST" || isset($_POST)) && isset($_POST["url"])) {
	print_r($_POST);
	$url = urldecode($_POST["url"]);
	
	$target_dir = dirname(__DIR__).'/admin/';
	$feeds = $target_dir.'feeds.csv';
	$match = false;
	
	if (file_exists($feeds)) {
		$f = fopen($feeds, 'r');
		while (($row = fgetcsv($f)) !== false) {
			$rows[] = $row;
		}
		
		foreach ($rows as $row) {
			$link = $row[0];
			if ($link == $url) {
				$match = true;
			}	
		}
    }
	
	if ($match) {
		file_exists('count.txt') ? $count = (int)file_get_contents('count.txt') : $count = 0;
		$count++;
		file_put_contents('count.txt', $count);
	}
}

?>