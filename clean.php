<?php

// Initialize the session
session_start();

define('APP_RAN', '');

// Include config file
require_once('config.php');

$rows = array();

$f = fopen('items.csv', 'r');
while (($row = fgetcsv($f)) !== false) {
	$rows[] = $row;
}

$i = 0;

do {
	unset($rows[$i]);
	$i++;
	echo count($rows).'<br>';
} while (count($rows) > 100);

$f = fopen('items.csv', 'w');
foreach($rows as $row) {
	if(!isset($row[7])) {
		$row[7] = '';
	}
	fputcsv($f, array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7]));
}
fclose($f);

header("Location: ".BASE_URL.'admin/');

?>