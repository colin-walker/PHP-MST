<?php
// Initialize the session
session_start();

define('APP_RAN', '');

// Include files

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
	<link rel="stylesheet" href="style.css" type="text/css" media="all">
	<link rel="stylesheet" href="mst.css" type="text/css" media="all">
</head>

<body>
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
  	<div id="pageWrapper">
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
		        <p>
		        <a class="back" href="<?php echo BASE_URL; ?>">&lt;&lt;</a>
		        </p>
		        <br>
		        
<?php
	if (file_exists($file)) {
		$f = fopen($file, 'r');
		while (($row = fgetcsv($f)) !== false) {
		    $posts[] = $row;
		}
		fclose($f);
	
		foreach ($posts as $post) {
			if ($post[0] == $t) {
				$content = $post[1];
				break;
			}
		}
	
		$Parsedown = new Parsedown();
		$content = $Parsedown->text($post[1]);
		
		if (AVATAR != '') {
			$avatar = '<img src="'.AVATAR.'" />';
		} else {
			$avatar = substr(NAME,0,1);
		}
		
		echo '<article class="h-entry hentry mst">'.PHP_EOL;
			echo '<div class="section">'.PHP_EOL;
				echo '<div class="entry-content e-content">'.PHP_EOL;
					echo '<div class="photo-box page"><div class="box-content"><div><span>'.$avatar.'</span></div></div></div>'.PHP_EOL;
					echo '<div class="pagePost">'.PHP_EOL;
					echo '<p class="pagePostTop">'.PHP_EOL;
					echo $content.PHP_EOL;
					echo '</p><span class="pagePostBottom">'.date("d/m/Y H:i:s", (int)$post[0]).'</span></div>'.PHP_EOL;
					echo '<div class="clear"></div>'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			echo '</div>'.PHP_EOL;
		echo '</article>'.PHP_EOL;
		
		// get replies
		
		$replies = array();
		foreach ($posts as $p=>$post) {
			if (isset($post[2]) && $post[2] == BASE_URL.'page.php?t='.$t) {
				$replies[$p][0] = $post[0];
				$replies[$p][1] = $post[1];
			}
		}
		
		$i = fopen('items.csv', 'r');
		while (($rows = fgetcsv($i)) !== false) {
			if (isset($rows[8]) && $rows[8] == BASE_URL.'page.php?t='.$t) {
				$replies[$p][0] = $rows[0];
				$replies[$p][1] = $rows[3];
				$replies[$p][2] = $rows[5];
				$replies[$p][3] = $rows[6];
				if (isset($row[7]) && $row[7] != '') {
					$avatar = '<img src="'.$row[7].'" />';
				} else {
					$avatar = substr($rows[5],0,1);
				}
			}
		}
		
		
		if (count($replies) > 0) {
			sort($replies);
			echo '<div id="replies">'; // style="padding-left: 25px;">';
			foreach ($replies as $reply) {
				echo '<article class="h-entry hentry mst">'.PHP_EOL;
					echo '<div class="section">'.PHP_EOL;
						echo '<div class="entry-content e-content">'.PHP_EOL;
							echo '<div class="photo-box page"><div class="box-content"><div><span>'.$avatar.'</span></div></div></div>'.PHP_EOL;
							echo '<div class="pagePost">'.PHP_EOL;
							echo '<p class="pagePostTop" style="margin-block-start: 0.25em;">'.PHP_EOL;
							if (isset($reply[2])) {
							echo '<span class="pagePostLink"><a href="'.$reply[3].'">'.$reply[2].'</a></span>'.PHP_EOL;
							}
							$Parsedown = new Parsedown();
							$reply[1] = $Parsedown->text($reply[1]);
							echo $reply[1].PHP_EOL;
							echo '</p><span class="pagePostBottom">'.date("d/m/Y H:i:s", (int)$reply[0]).'</span></div>'.PHP_EOL;
							echo '<div class="clear"></div>'.PHP_EOL;
						echo '</div>'.PHP_EOL;
					echo '</div>'.PHP_EOL;
				echo '</article>'.PHP_EOL;
			}
			echo '</div>';
		}
	}
?>
	    		</main>
	    	</div>
	    </div>
	</div>
</body>
</html>