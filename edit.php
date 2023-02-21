<?php

define('APP_RAN', '');

// Include config file
require_once('config.php');
require_once('Feeds.php');
require_once('Parsedown.php');
require_once('ping.php');

$target_dir = dirname(__FILE__).'/';
$session = $target_dir.'session.php';
$file = $target_dir.'posts.csv';
$xml = $target_dir.'feed.xml';
$match = false;

if (isset($_POST['link']) && isset($_POST['content'])) {
	$link = $_POST['link'];
	$newcontent = $_POST['content'];	
	$rows = array();
	$h = fopen('posts.csv', 'r');
	while (($row = fgetcsv($h)) !== false) {
		$id = explode('?t=', $_POST['link'])[1];
		if ($row[0] == $id && $row[1] != $newcontent) {
			$rows[] = array($row[0], $newcontent);
			$match = true;
			
		} else {
			$rows[] = $row;
		}
	}
	fclose($h);
	
	if ($match) {
		$f = fopen('posts.csv', 'w');
		foreach ($rows as $row) {
			if (!isset($row[2])) {
				$row[2] = '';
			}
			fputcsv($f, array($row[0],$row[1], $row[2]));
		}
		fclose($f);
		
		include('rss.php');
	}
	
	header("Location: ".BASE_URL);
}

if (isset($_GET['c'])) {
	$content = $_GET['c'];
	$link = $_GET['l'];
	
	echo '<br>';
	echo '<form method="post" action="edit.php">';
	echo '<input type="hidden" name="link" value="'.$link.'">';
	echo '<textarea rows="5" name="content" id="editcontent" class="text mstedit">'.$content.'</textarea>';
	echo '<input type="submit" name="submit" id="submit" value="update">';
	echo '</form>';
}

?>

<script>
var textArea = document.getElementById("editcontent");
var areaLen = textArea.value.length;
textArea.focus();
textArea.setSelectionRange(areaLen, areaLen);
textArea.scrollTop = textArea.scrollHeight;
</script>