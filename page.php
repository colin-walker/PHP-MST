<?php
// Initialize the session
session_start();

define('APP_RAN', '');

// Include config file
require_once('config.php');
require_once('Feeds.php');
require_once('Parsedown.php');

$target_dir = dirname(__FILE__).'/';
$file = $target_dir.'posts.csv';

if (isset($_GET['t'])) {
	$t = $_GET['t'];
} else {
    header("Location: ".BASE_URL);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>PHP-MST</title>
	<link rel="stylesheet" href="../style.css" type="text/css" media="all">
	<link rel="stylesheet" href="mst.css" type="text/css" media="all">
</head>

<body style="font-family: sans-serif; font-size: 16px;">
	<header id="masthead" class="site-header">
    	<div class="site-branding">
        	<h1 class="site-title">
        		<a href="<?php echo BASE_URL; ?>" rel="home">
	          		<span class="p-name">
	          			PHP-MST
	          		</span>
          		</a>
        	</h1>
      	</div>
  	</header>
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
		        <br>
<?php
	if (file_exists($file)) {
		$f = fopen($file, 'r');
		while (($row = fgetcsv($f)) !== false) {
		    $posts[] = $row;
		}
		fclose($f);
	
		foreach ($posts as $post) {;
			if ($post[0] == $t) {
				$content = $post[1];
			}
		}
	
		$Parsedown = new Parsedown();
		$content = $Parsedown->text($post[1]);
		
		if (AVATAR != '') {
			$avatar = '<img src="'.AVATAR.'" />';
		} else {
			$avatar = substr(NAME,0,1);
		}
		
				echo '<article class="h-entry hentry">'.PHP_EOL;
				echo '<div class="section">'.PHP_EOL;
					echo '<div class="entry-content e-content">'.PHP_EOL;
						echo '<div class="photo-box page"><div class="box-content"><div><span>'.$avatar.'</span></div></div></div>'.PHP_EOL;
						echo '<div style="float: right; width: calc(100% - 30px); margin-block-start: 0em;">'.PHP_EOL;
						echo '<p style="line-height: 0.9em; margin-block-start: 0em;">'.PHP_EOL;
						echo $content.PHP_EOL;
						echo '<span style="font-size: 0.6em; font-weight: bold; line-height: 0.6em; text-decoration: none; position: relative;
    top: -10px;">'.date("d/m/Y H:i:s", (int)$post[0]).'</span></p></div>'.PHP_EOL;
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