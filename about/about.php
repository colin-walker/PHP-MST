<?php
// Initialize the session
session_start();

define('APP_RAN', '');

// Include config file
require_once('../config.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>About PHP-MST</title>
	<link rel="stylesheet" href="../style.css" type="text/css" media="all">
	<link rel="stylesheet" href="../mst.css" type="text/css" media="all">
</head>

<body>
	<header id="masthead" class="site-header">
    	<div class="site-branding">
        	<h1 class="site-title">
        		<a href="<?php echo BASE_URL; ?>" rel="home">
	          		<span class="p-name">
	          			About PHP-MST
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
			        
			        <article class="h-entry hentry">
						<div class="section">
							<div class="entry-content e-content">
								<h2>What is PHP-MST?</h2>
								<p>
									PHP-MST is a simple PHP implementation of <a href="http://fedwiki.andysylvester.com:443/">My Status Tool</a> by Andy Sylvester.
								</p>
								<p>
									In Andy's words:
									<blockquote>
									My Status Tool is an application that provides the basic posting and reading functionality within Twitter, but using RSS and rssCloud as the enabling technologies.
									</blockquote>
								</p>
								<p>
									PHP-MST has the following functionality:
									<ul>
										<li>make a short post (although there is technically no limit)</li>
										<li>add posts to a local file and build an RSS feed (min 10, max 50 items)</li>
										<li>each post has a page</li>
										<li>posts can be edited by double-clicking them in the timeline</li>
										<li>posts are sorted by timestamp along with those from any other subscribed feeds</li>
										<li>local storage for remote posts and display limited to 100</li>
										<li>initial reply functionality using a new <a href="https://php-mst.colinwalker.blog/">'mst' namespace</a></li>
										<li>an admin page for feed subscription & removal and other settings</li>
									</ul>
								</p>
								<p>
									PHP-MST uses Emanuil Rusev's <a href="https://github.com/erusev/parsedown">Parsedown</a> library for Markdown and <a href="https://github.com/colin-walker/rss-php">my forked version</a> of David Grudl's RSS-PHP.
								</p>
								<p>
									It doesn't auto-refresh but prompts when your subscribed feeds have been updated so you can manually reload the page.
								</p>
								<p>
								<h3>To do:</h3>
								<ul>
									<li>clean up some inline CSS in /admin</li>
									<li>possible support for rss enclosures (podcast)</li>
								</ul>
								</p>
							</div>
						</div>
					</article>
		        </main>
	    	</div>
	    </div>
	</div>
</body>
</html>