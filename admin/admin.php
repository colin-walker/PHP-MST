<?php

if (!file_exists('../config.php')) {
	header("Location: ../setup.php");
	exit();
}

if (file_exists('../config.php') && file_exists('../setup.php')) {
	unlink('../setup.php');
}

// Initialize the session
session_start();

define('APP_RAN', '');

// Include config file
require_once('../config.php');
require_once('../Feeds.php');
require_once('pleaseNotify.php');

$target_dir = dirname(__FILE__).'/';
$session = dirname(__DIR__).'/session.php';
$feeds = $target_dir.'feeds.csv';

if (isset($_POST['logout'])) {
	unlink($session);
}

file_exists($session) ? $auth = file_get_contents($session) : $auth = 0;

if (!isset($_SESSION['mstauth']) || $_SESSION['mstauth'] != $auth) {
    header("Location: ../");
}
        
// add feed

if (isset($_POST['add'])) {
    $URL = $_POST['newfeed'];
    $match = false;
    $rows = array();
    
    if (file_exists($feeds)) {
		$f = fopen($feeds, 'r');
		while (($row = fgetcsv($f)) !== false) {
			$rows[] = $row;
		}
		
		foreach ($rows as $row) {
			$link = $row[0];
			if ($link == $URL) {
				$match = true;
			}	
		}
    }
    
    if ($match == false) {
    	
    	$options = array(
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0) Gecko/20100101 Firefox/9.0',
			CURLOPT_ENCODING => '',
			CURLOPT_HEADER => FALSE,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 20,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_FOLLOWLOCATION => TRUE,
		);
		
		$port = parse_url($URL, PHP_URL_PORT);

		$orig = $URL;
		
		if (!empty($port)) {
			array_push($options,'CURLOPT_PORT => ' .$port);
			$scheme = parse_url($orig, PHP_URL_SCHEME);
			$host = parse_url($orig, PHP_URL_HOST);
			$path = parse_url($orig, PHP_URL_PATH);
			$URL = $scheme.'://'.$host.$path;
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $orig);
		curl_setopt_array($curl, $options);
		$result = curl_exec($curl);
		curl_close($curl);
		
		$xml = new SimpleXMLElement(trim($result), LIBXML_NOWARNING | LIBXML_NOERROR);
    	
    	$rss = Feed::fromRss($xml);
    	
    	if (isset($rss->{'cloud'}['domain'])) {
			$domain = (string)$rss->{'cloud'}['domain'];
			$port = (string)$rss->{'cloud'}['port'];
			$path = (string)$rss->{'cloud'}['path'];
			$protocol = (string)$rss->{'cloud'}['protocol'];
    		$result = pleaseNotify($orig, $domain, $port, $path);			
		} else {
			$domain = $port = $path = $protocol = '';
		}
    	
    	$f = fopen($feeds, 'a');
    	fputcsv($f, array($orig, $domain, $port, $path, $protocol));
    	fclose($f);
    }
}

// delete feed

if (isset($_POST['deletefeed'])) {
	$feed = $_POST['f'];
	$f = fopen($feeds, 'r');
    while (($row = fgetcsv($f)) !== false) {
		$rows[] = $row;
	}
	unset($rows[$feed]);
	unlink($feeds);
	
	$f = fopen($feeds, 'a');
	foreach ($rows as $row) {
		fputcsv($f, $row);
	}
	fclose($f);
}

// set refresh

if (isset($_POST['change'])) {
    $interval = $_POST['refresh'];
    file_put_contents('refresh.txt', $interval);
}

// set items

if (isset($_POST['items'])) {
    $items = $_POST['items'];
    file_put_contents('items.txt', $items);
}

$refresh = file_get_contents('refresh.txt');
$items = file_get_contents('items.txt');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Feeds</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../mst.css">
    
    <style>
        @media screen and (prefers-color-scheme: dark) {
            .feed_control {
                background-color: var(--grey-e) !important
            }
        }
    </style>

</head>

<body class="login">
	  <header id="masthead" class="site-header">
            <div class="site-branding">
                <h1 class="site-title">
                    <a href="<?php echo BASE_URL; ?>" rel="home">
                        <span class="p-name">Feeds</span>
                    </a>
                </h1>
            </div>
        </header>
        
		<a id="toggle" class="toggle" href="<?php echo BASE_URL; ?>">
            <img src="<?php echo '../images/cancel.png'; ?>" />
        </a>
        
        <div class="adminwrapper mst" style="margin: 20px auto 10px; color: #444;">
		    <h2 class="titleSpan" style="position: relative; top: -5px; color: #444;">Add feed</h2>
		     <form method='post'>
		        <input type='hidden' name='add'>
		        <input class='form-control addfeed' name='newfeed' type='text' placeholder='Add feed' autocomplete="off">
		        <input type='submit' value='Add' style='float: right;'><br/>
		    </form>
		</div>
		
		<div class="adminwrapper mst" style="margin: 20px auto 10px; color: #444;">
		    <form method='post'>
		        <input type='hidden' name='change'>
		        <span style="float: left; min-width: 40%; padding-top: 9px;">Refresh interval: </span><input class='form-control' name='refresh' type='number' style='margin-bottom: 0px; width: 40px;' autocomplete="off" value="<?php echo $refresh; ?>" />
		        <span style="float: left; min-width: 40%; padding-top: 9px;">Items: </span><input class='form-control' name='items' type='number' min='10' max='50' style='margin-bottom: 0px; width: 40px; float: left;' autocomplete="off" value="<?php echo $items; ?>" />
		        <input type='submit' value='Update' style='margin-top: 7px; float: right;'><br/>
		    </form>
		    <div style="clear: both;"></div>
		</div>
		
		<div class="adminwrapper mst" style="margin: 20px auto 10px; color: #444;">

<?php
	if (file_exists($feeds)) {
	$rows = array();
	$f = fopen($feeds, 'r');
    while (($row = fgetcsv($f)) !== false) {
			$rows[] = $row;
	}
	fclose($f);
	
	foreach ($rows as $i=>$row) {
			echo '<div style="margin: 10px 0px;">';
			echo '<span style="float: left; width: calc(100% - 30px); overflow: hidden;">'.$row[0].'</span>';
?>
			<form class="delicon" style="float: right; margin-right: 0px;" method="post">
				<input type="hidden" name="f" value="<?php echo $i; ?>">
				<input type="hidden" name="deletefeed">
				<input class="dict_del" onClick="javascript: return confirm('Are you sure?');" type="image" src="../images/red-cross.png" style="width: 16px; float: right;">
			</form>
<?php			
			echo '<div style="clear: both;"></div>';
			echo '</div>';
	}
	}
?>
		</div>
		<div class="adminwrapper mst" style="background: transparent !important; border: none; margin: 0 auto;">
			<form method='post'>
		        <input type='hidden' name='logout'>
		        <input type='submit' value='Log out' style='float: right;'>
		    </form>
		</div>   
</body>
</html>