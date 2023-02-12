<?php

// Initialize the session
session_start();

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
		
		if (file_exists($xml)) {
			unlink($xml);
		}
			$xmlfile = fopen($xml, 'w');

			fwrite($xmlfile, '<?xml version="1.0" standalone="yes" ?>'.PHP_EOL);
			fwrite($xmlfile, '<?xml-stylesheet href="'.BASE_URL.'feed.xsl" type="text/xsl" ?>'.PHP_EOL);
			fwrite($xmlfile, '<rss version="2.0">'.PHP_EOL);
			fwrite($xmlfile, '<channel>'.PHP_EOL);
			fwrite($xmlfile, '<title>'.NAME.' – PHP-MST</title>'.PHP_EOL);
			fwrite($xmlfile, '<description>Statuses from ' . NAME . ' - PHP-MST</description>'.PHP_EOL);
			fwrite($xmlfile, '<link>'.BASE_URL.'</link>'.PHP_EOL);
			if (AVATAR !== '') {
			fwrite($xmlfile, '<image>'.PHP_EOL);
			fwrite($xmlfile, '<url>'.AVATAR.'</url>'.PHP_EOL);
			fwrite($xmlfile, '<title>'.NAME.' – PHP-MST</title>'.PHP_EOL);
			fwrite($xmlfile, '<link>'.BASE_URL.'</link>'.PHP_EOL);
			fwrite($xmlfile, '</image>'.PHP_EOL);
			}
			fwrite($xmlfile, '<lastBuildDate>' . gmdate('D, d M Y H:i:s') . ' GMT</lastBuildDate>'.PHP_EOL);
			fwrite($xmlfile, '<cloud domain="rpc.rsscloud.io" port="5337" path="/pleaseNotify" registerProcedure="" protocol="http-post"/>'.PHP_EOL);
			fwrite($xmlfile, '<generator>PHP-MST</generator>'.PHP_EOL);
			fwrite($xmlfile, '<language>en-GB</language>'.PHP_EOL);
			
			function cmp($a, $b) {
			    return strcmp($b[0], $a[0]);
			}
			uasort($posts, "cmp");

			foreach($posts as $post) {
				$Parsedown = new Parsedown();
				$content = $Parsedown->text($post[1]);
				fwrite($xmlfile, '<item>'.PHP_EOL);
				fwrite($xmlfile, '<link>'.BASE_URL.'page.php?t='.$post[0].'</link>'.PHP_EOL);
				fwrite($xmlfile, '<pubDate>' . gmdate("D, d M Y H:i:s", (int)$post[0]) . ' GMT</pubDate>'.PHP_EOL);
				fwrite($xmlfile, '<description><![CDATA[' . $content . ']]></description>'.PHP_EOL);
				fwrite($xmlfile, '</item>'.PHP_EOL);
			}
			
			fwrite($xmlfile, '</channel>'.PHP_EOL);
			fwrite($xmlfile, '</rss>'.PHP_EOL);
			fclose($xmlfile);
			
			$feedurl = BASE_URL.'/feed.xml';
			doPing($feedurl);
	}
}


header("Location: ".BASE_URL);

?>