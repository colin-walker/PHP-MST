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

while (count($rows) > 100) {
	unset($rows[$i]);
	$i++;
	//echo count($rows).'<br>';
}

$f = fopen('items.csv', 'w');
foreach($rows as $row) {
	if(!isset($row[7])) {
		$row[7] = '';
	}
	if(!isset($row[8])) {
		$row[8] = '';
	}
	fputcsv($f, $row);
}
fclose($f);

?>

<script>
window.location.href = "<?php echo BASE_URL; ?>admin/";
</script>