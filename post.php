<?php

// Initialize the session
session_start();

define('APP_RAN', '');

// Include files

require_once('config.php');
require_once('Feeds.php');
require_once('Parsedown.php');
require_once('ping.php');

$target_dir = dirname(__FILE__).'/';
$session = $target_dir.'session.php';
$file = $target_dir.'posts.csv';
$xml = $target_dir.'feed.xml';

if (file_exists('admin/items.txt')) {
    $items = file_get_contents('admin/items.txt');
}

file_exists($session) ? $auth = file_get_contents($session) : $auth = 0;

if ( isset($_POST['dopost']) && hash_equals($_POST['randcheck'], ''.$_SESSION['rand']) ) {
	if (isset($_SESSION['mstauth']) && $_SESSION['mstauth'] == $auth) {
		$content = $_POST['content'];
		$posts = array();
		if (file_exists($file)) {
			$f = fopen($file, 'r');
			while (($row = fgetcsv($f)) !== false) {
			    $posts[] = $row;
			}
			fclose($f);
			unlink($file);
		}

		if (count($posts) >= $items) {
			$i = 0;
			do {
			unset($posts[$i]);
			$i++;
			} while (count($posts) >= $items);
		}
		
		$time = time();
		$new = [$time, $content];
		array_push($posts, $new);

		$f = fopen($file, 'a');
		foreach ($posts as $row) {
			fputcsv($f, $row);
		}
		
		include('rss.php');
	}
}

header("Location: ".BASE_URL);

?>