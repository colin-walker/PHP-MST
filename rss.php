<?php

// Initialize the session
session_start();

$posts = array();
if (file_exists($file)) {
	$f = fopen($file, 'r');
	while (($row = fgetcsv($f)) !== false) {
	    $posts[] = $row;
	}
	fclose($f);
}

file_exists($session) ? $auth = file_get_contents($session) : $auth = 0;

if (isset($_SESSION['mstauth']) && $_SESSION['mstauth'] == $auth) {
	if (file_exists($xml)) {
		unlink($xml);
	}
	$xmlfile = fopen($xml, 'w');
			
	fwrite($xmlfile, '<?xml version="1.0" standalone="yes" ?>'.PHP_EOL);
	fwrite($xmlfile, '<?xml-stylesheet href="'.BASE_URL.'feed.xsl" type="text/xsl" ?>'.PHP_EOL);
	fwrite($xmlfile, '<rss xmlns:source="http://source.scripting.com/" xmlns:now="https://php-mst.colinwalker.blog/" version="2.0">'.PHP_EOL);
	fwrite($xmlfile, '<channel>'.PHP_EOL);
	fwrite($xmlfile, '<title>'.NAME.'</title>'.PHP_EOL);
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
		fwrite($xmlfile, '<item>'.PHP_EOL);
		fwrite($xmlfile, '<link>'.BASE_URL.'page.php?t='.$post[0].'</link>'.PHP_EOL);
		fwrite($xmlfile, '<pubDate>' . gmdate("D, d M Y H:i:s", (int)$post[0]) . ' GMT</pubDate>'.PHP_EOL);
		
		$post_array = explode("\n", $post[1]);
	    $size = sizeof($post_array);
		if (substr($post_array[0], 0, 2) === "# ") {
			$length = strlen($post_array[0]);
			$required = $length - 3;
			$post_title = substr($post_array[0], 2, $required);
			$post[1] = substr($post[1],$length);
			fwrite($xmlfile, '<title><![CDATA[' . $post_title . ']]></title>'.PHP_EOL);
		}
		$md_content = $post[1];
  		$md_content = str_replace('&', '&amp;', $md_content);
  		$md_content = str_replace('<', '&lt;', $md_content);
  		$md_content = str_replace('>', '&gt;', $md_content);
  		$md_content = str_replace("\r\n", '&#10;', $md_content);
  		
		$Parsedown = new Parsedown();
		$content = $Parsedown->text($post[1]);
		fwrite($xmlfile, '<description><![CDATA[' . $content . ']]></description>'.PHP_EOL);
		fwrite($xmlfile, '<source:markdown>' . $md_content . '</source:markdown>' . PHP_EOL);
		
		if (isset($post[2]) && !empty($post[2])) {
			fwrite($xmlfile, '<mst:reply>'.$post[2].'</mst:reply>'.PHP_EOL);
		}
		
		fwrite($xmlfile, '</item>'.PHP_EOL);
	}
	
	fwrite($xmlfile, '</channel>'.PHP_EOL);
	fwrite($xmlfile, '</rss>'.PHP_EOL);
	fclose($xmlfile);
	
	$feedurl = BASE_URL.'feed.xml';
	doPing($feedurl);
}
?>