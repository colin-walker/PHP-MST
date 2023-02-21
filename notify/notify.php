<?php

define('APP_RAN', '');

// Include files

require_once('../config.php');
require_once('../Feeds.php');
  
if (isset($_GET['url']) && isset($_GET['challenge'])) {
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

	$rows = array();
	$addrows = array();
	$f = fopen('../items.csv', 'r');
	while (($row = fgetcsv($f)) !== false) {
		$rows[] = $row;
		if (count($rows) == 100) {
			unset($rows[0]);
		}
	}
	fclose($f);
	
	$options = array(
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0) Gecko/20100101 Firefox/9.0',
		CURLOPT_ENCODING => '',
		CURLOPT_HEADER => FALSE,
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_TIMEOUT => 20,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_FOLLOWLOCATION => TRUE,
	);
	
	$port = parse_url($url, PHP_URL_PORT);
	
	if (!empty($port)) {
		array_push($options,'CURLOPT_PORT => ' .$port);
	}
		
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt_array($curl, $options);
	$result = curl_exec($curl);
	curl_close($curl);
	
	$xml = new SimpleXMLElement(trim($result), LIBXML_NOWARNING | LIBXML_NOERROR);
			
	try {
		$rss = Feed::fromRss($xml);
		$title = $rss->title;
		$link = $rss->link;
	    if (isset($rss->image->url)) {
	    	$image = $rss->image->url[0];
	    } else {
	    	$image = '';	
	    }
		
		foreach ($rss->item as $item) {
			$match = false;		
			$itemLink = $item->link;
			$itemTime = $item->timestamp;
			
			if (isset($item->title)) {
				$itemTitle = $item->title;
			} else {
				$itemTitle = '';
			}
	
			if (isset($item->{'content:encoded'})) {
				$itemContent = $item->{'content:encoded'};
			} else {
				$itemContent = html_entity_decode($item->description);
			}
			
			if (isset($item->{'mst:reply'})) {
				$irt = $item->{'mst:reply'};
			} else {
				$irt = '';
			}
			
			foreach ($rows as $row) {
				if ($itemLink == $row[1]) {
					$match = true;
				}
			}
	
			if (!$match) {
				$addrows[] = array($itemTime, $itemLink, $itemTitle, $itemContent, $url, $title, $link, $image, $irt);
			}
		}
	}
	catch (Exception $e) {
	}
	
	$f = fopen('../items.csv', 'a');
	foreach($addrows as $row) {
		fputcsv($f, array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6], $row[7], $row[8]));
	}
	fclose($f);
}

?>