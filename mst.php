<?php

if (!file_exists('config.php')) {
	header("Location: setup.php");
	exit();
}

// Initialize the session
session_start();

define('APP_RAN', '');

// Include files

require_once('config.php');
require_once('Feeds.php');
require_once('Parsedown.php');

$target_dir = dirname(__FILE__).'/';
$session = $target_dir.'session.php';
$feeds = $target_dir.'admin/feeds.csv';
$xml = $target_dir.'feed.xml';

$refresh = 60 * (int)file_get_contents($target_dir.'/admin/refresh.txt');

file_exists($session) ? $auth = file_get_contents($session) : $auth = 0;

$count = $target_dir.'notify/count.txt';

if (file_exists($count)) {
	unlink($count);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title><?php echo NAME; ?></title>
    <meta name="description" content="PHP-MST for <?php echo NAME; ?>" />
    <link rel="icon" type="image/png" href="<?php echo AVATAR; ?>">
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
    <a id="toggle" class="toggle" href="admin">
    	<picture>
            <source srcset="images/admin_dark.png" media="(prefers-color-scheme: dark)">
            <img src="images/admin_light.png" />
        </picture>
    </a>
<?php } else { ?>
	<a id="toggle" class="toggle" href="login">
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
				        	<li><a href="<?php echo BASE_URL; ?>about">About</a></li>
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

$rows = array();

$h = fopen('posts.csv', 'r');
while (($row = fgetcsv($h)) !== false) {
	$post_title = '';
	
	$rows[] = array(
		$row[0],
		BASE_URL.'page.php?t='.$row[0],
		$post_title,
		$row[1],
		BASE_URL.'feed.xml',
		NAME,
		BASE_URL,AVATAR
	);
}
fclose($h);

$f = fopen('items.csv', 'r');
while (($row = fgetcsv($f)) !== false) {
	$rows[] = $row;
}
fclose($f);

function cmp($a, $b) {
    return strcmp($b[0], $a[0]);
}
uasort($rows, "cmp");

foreach ($rows as $post) {
	$newposts[] = $post;
}

foreach ($newposts as $p=>$row) {
	if ($p < 100) {
							$Parsedown = new Parsedown;
					$content = $Parsedown->text($row[3]);
		if (isset($row[7]) && $row[7] != '') {
			$avatar = '<img src="'.$row[7].'" />';
		} else {
			$avatar = substr($row[5],0,1);
		}
		echo '<article class="h-entry hentry">'.PHP_EOL;
			echo '<div class="section">'.PHP_EOL;
				echo '<div class="entry-content e-content">'.PHP_EOL;
					echo '<div class="photo-box"><div class="box-content"><div><span><a href="'.$row[6].'">' . $avatar . '</a></span></div></div></div>'.PHP_EOL;
					echo '<div class="pagePost">'.PHP_EOL;
					echo '<div class="pagePostTop">'.PHP_EOL;
					echo '<span class="pagePostLink"><a href="'.$row[6].'">'.$row[5].'</a></span><br>'.PHP_EOL;
					
					if (isset($_SESSION['mstauth']) && $_SESSION['mstauth'] == $auth && strpos($row[1], BASE_URL) === 0) {
						$edit = 'hx-target="this" hx-trigger="dblclick" hx-get="edit.php?c='.urlencode($row[3]).'&l='.urlencode($row[1]).'"';
					} else {
						$edit = '';
					}
					
					echo '<div class="contentDiv" '.$edit.'>'.$content.'</div>'.PHP_EOL;
					echo '<a class="cd" href="'.$row[1].'">'.date(DATEFORMAT." H:i:s", (int)$row[0]).'</a></div></div>'.PHP_EOL;
					echo '<div class="clear"></div>'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			echo '</div>'.PHP_EOL;
		echo '</article>'.PHP_EOL;
	}
}

		
?>

	    		</main>
	    	</div>
	    </div>
	</div>
</body>
</html>