<?php

if (!file_exists('config.php')) {
	header("Location: setup.php");
	exit();
}

// Initialize the session
session_start();

define('APP_RAN', '');

// Include config file
require_once('config.php');
require_once('Feeds.php');
require_once('Parsedown.php');

$target_dir = dirname(__FILE__).'/';
$session = $target_dir.'session.php';
$feeds = $target_dir.'admin/feeds.csv';
$xml = $target_dir.'feed.xml';

$refresh = 60 * (int)file_get_contents($target_dir.'/admin/refresh.txt');

//echo $refresh;

file_exists($session) ? $auth = file_get_contents($session) : $auth = 0;

//if (isset($_POST['r']) && $_POST['r'] == 'yes') {
	$count = $target_dir.'notify/count.txt';
	if (file_exists($count)) {
		unlink($count);
	}
//}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title><?php echo NAME; ?></title>
    <meta name="description" content="PHP-MST for <?php echo NAME; ?>" />
	<link rel="stylesheet" href="style.css" type="text/css" media="all">
	<link rel="stylesheet" href="mst.css" type="text/css" media="all">
	<link rel="me" href="mailto:<?php echo MAILTO; ?>">
	<script src="htmx.min.js"></script>
</head>

<body style="font-family: sans-serif; font-size: 16px;">
	<header id="masthead" class="site-header">
    	<div class="site-branding">
        	<h1 class="site-title">
        		<a href="<?php echo BASE_URL; ?>" rel="home">
	          		<span class="p-name">
	          			<?php echo NAME; ?>
	          		</span>
          		</a>
        	</h1>
      	</div>
  	</header>
  	
<?php if (isset($_SESSION['mstauth']) && $_SESSION['mstauth'] == $auth) { ?>
    <a id="toggle" class="toggle" href="admin/">
    	<picture>
            <source srcset="../images/admin_dark.png" media="(prefers-color-scheme: dark)">
            <img src="../images/admin_light.png" />
        </picture>
    </a>
<?php } else { ?>
	<a id="toggle" class="toggle" href="login/">
		<span class="login">ⓜ</a>
	</a>
<?php } ?>
  	<div id="wrapper" style="width: 100vw; position: absolute; left: 0px;">
    	<div id="page" class="site">
        	<div id="primary" class="content-area">
	    		<main id="main" class="site-main today-container">

					<nav class="mst">
			        	<ul>
			        		<li><a href="<?php echo BASE_URL; ?>">Home</a></li>
				        	<li><a href="<?php echo BASE_URL; ?>about/">About</a></li>
			        		<li><a href="<?php echo BASE_URL; ?>feed.xml">Feed</a></li>
			        	</ul>
			        </nav>
	    		
<?php
if (isset($_SESSION['mstauth']) && $_SESSION['mstauth'] == $auth) {
?>
					<div style="margin-bottom: 70px;">
					<form name="form" method="post" action="post.php">
<?php
	$rand = bin2hex(random_bytes(32));
	$_SESSION['rand']=$rand;
?>
						<input type="hidden" value="<?php echo $rand; ?>" name="randcheck" />
						<input type="hidden" id="dopost" name="dopost" value="true">
        				<textarea rows="5" id="content" name="content" class="text" placeholder="Write..." required></textarea>
        				<input type="submit" name="submit" id="submit" value="Post"><span style="float: left; font-size: 75%;">
					</form>
					</div>
<?php
}
?>
					<div hx-get="count.php" hx-trigger="every <?php echo $refresh; ?>s"></div>
<?php

$options = array(
	CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0) Gecko/20100101 Firefox/9.0',
	CURLOPT_ENCODING => '',
	CURLOPT_HEADER => FALSE,
	CURLOPT_CONNECTTIMEOUT => 5,
	CURLOPT_TIMEOUT => 20,
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_FOLLOWLOCATION => TRUE
);

$websites = array();
if (file_exists($target_dir.'feed.xml')) {
	$websites[] = BASE_URL.'feed.xml';
}

if (file_exists($feeds)) {
	$f = fopen($feeds, 'r');
	while (($row = fgetcsv($f)) !== false) {
			$websites[] = $row[0];
	}
	fclose($f);
}

$mh = curl_multi_init();
foreach ($websites as $i=>$website) {
    $worker[$i] = curl_init($website);
    curl_setopt_array($worker[$i], $options);
    curl_multi_add_handle($mh, $worker[$i]);
}

do {
	$status = curl_multi_exec($mh, $still_running);
	if ($still_running) {
		curl_multi_select($mh);
    }
} while ($still_running && $status == CURLM_OK);

GLOBAL $results;
$results = [];

foreach ($websites as $i=>$website) {
	$results[$website] = curl_multi_getcontent($worker[$i]);
	curl_multi_remove_handle($mh, $worker[$i]);
    curl_close($worker[$i]);
}

curl_multi_close($mh);

$posts = array();

foreach ($websites as $i=>$website) {
try {
	$xml = new SimpleXMLElement(trim($results[$website]), LIBXML_NOWARNING | LIBXML_NOERROR);
        
    $rss = Feed::fromRss($xml);
    
    $feed = $rss->title;
    $link = $rss->link;
    if (isset($rss->image->url)) {
    	$image = $rss->image->url[0];
    } else {
    	$image = '';	
    }
	
	$c = 0;	
	foreach ($rss->item as $item) {
		$itemLink = $item->link;
		$itemTime = $item->timestamp;
		if (isset($item->{'content:encoded'})) {
			$itemContent = $item->{'content:encoded'};
		} else {
			$itemContent = $item->description;
		}
		if (substr($itemContent, 0, 3) != '<p>') {
		$itemContent = '<p>' . $itemContent;
		}
		if (substr($itemContent, -4) != '</p>') {
			$itemContent = $itemContent . '<br></p>';
		}
		
		$Parsedown = new Parsedown();
		$itemContent = $Parsedown->text($itemContent);
		
		$posts[] = [$itemTime, $itemLink, $itemContent, $feed, $link, $image];
		$c++;
		if ($c == 10) {
			break;
		}
	}
} catch (exception $e) {
}
}

function cmp($a, $b) {
    return strcmp($b[0], $a[0]);
}
uasort($posts, "cmp");

foreach ($posts as $post) {

if ($post[5] != '') {
	$avatar = '<img src="'.$post[5].'" />';
} else {
	$avatar = substr($post[3],0,1);
}
			echo '<article class="h-entry hentry">'.PHP_EOL;
				echo '<div class="section">'.PHP_EOL;
					echo '<div class="entry-content e-content">'.PHP_EOL;
						echo '<div class="photo-box"><div class="box-content"><div><span><a style="text-decoration: none; color: white !important; cursor: pointer;" href="'.$post[4].'">' . $avatar . '</a></span></div></div></div>'.PHP_EOL;
						echo '<div style="float: right; width: calc(100% - 30px); margin-block-start: 0em; word-wrap: break-word;">'.PHP_EOL;
						echo '<p style="line-height: 0.9em; margin-block-start: 0em;">'.PHP_EOL;
						echo '<span style="font-size: 0.8em; line-height: 1em; position: relative; top: 4px; margin-bottom: 10px;"><a style="text-decoration: none;" href="'.$post[4].'">'.$post[3].'</a></span><br>'.PHP_EOL;
						echo $post[2].PHP_EOL;
						echo '<div style="clear:both;"></div>'.PHP_EOL;
						echo '<a style="font-size: 0.6em; line-height: 0.6em; text-decoration: none; position: relative; top: -10px;" href="'.$post[1].'">'.date(DATEFORMAT." H:i:s", (int)$post[0]).'</a></p></div>'.PHP_EOL;
						echo '<div style="clear: both;"></div>'.PHP_EOL;
					echo '</div>'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			echo '</article>'.PHP_EOL;
}

?>
	    		
	    		</main>
	    	</div>
	    </div>
	</div>
</body>
</html>