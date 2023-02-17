<?php

define('APP_RAN', '');

// Include config file
require_once('config.php');

$target_dir = dirname(__FILE__).'/notify/';
$file = $target_dir.'count.txt';

file_exists($file) ? $count = (int)file_get_contents($file) : $count = 0;

if ($count > 0) {
	http_response_code(286);
?>
	<div class="rDiv">
		<a class="rLink" hx-target="body" hx-post="<?php echo BASE_URL; ?>">
			Load updates
		</a>
	</div>
<?php
}

?>