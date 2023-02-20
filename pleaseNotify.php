<?php

// Initialize the session
session_start();

define('APP_RAN', '');

// Include files

require_once('config.php');
require_once('Feeds.php');

$rows = array();

$target_dir = dirname(__FILE__).'/';
$feeds = $target_dir.'admin/feeds.csv';

if (file_exists($feeds)) {
	$f = fopen($feeds, 'r');
	while (($row = fgetcsv($f)) !== false) {
		$rows[] = $row;
	}

	foreach ($rows as $row) {
		$feed = $row[0];
		$domain = $row[1];
		$port = $row[2];
		$path = $row[3];
		$protocol = $row[4];
	
		$url = 'http://'.$domain.$path;
		$host = parse_url(BASE_URL, PHP_URL_HOST);
		//$homepath = '/php-mst/notify/notify.php';
		
		$test = strpos($_SERVER['PHP_SELF'], 'pleaseNotify.php');
		$cut = substr($_SERVER['PHP_SELF'],0,$test);
		$homepath = $cut.'notify/notify.php';
	
		$fields = array(
		  'domain' => $host,
		  'port' => '443',
		  'path' => $homepath,
		  'registerProcedure' => '',
		  'protocol' => 'https-post',
		  'url1' => $feed
		);
		
		$postdata = http_build_query($fields);
    
	    try {
	        $ch = curl_init($url);
	        curl_setopt($ch,CURLOPT_POST, true);
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	        $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        $result = curl_exec($ch);
	        if ($result === false) {
	            throw new Exception(curl_error($ch), curl_errno($ch));
	        }
	        
	    } catch(Exception $e) {
	        trigger_error(sprintf(
	            'Curl failed with error #%d: %s',
	            $e->getCode(), $e->getMessage()),
	            E_USER_ERROR);
	    } finally {
	        curl_close($ch);
	    }
	}
}
?>