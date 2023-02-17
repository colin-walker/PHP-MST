<?php

// Initialise session
session_start();

define('APP_RAN', '');

// Include config file
require_once('../config.php');

$target_dir = dirname(__DIR__).'/';
$session = $target_dir.'session.php';

file_exists($session) ? $auth = file_get_contents($session) : $auth = 0; 

if (isset($_SESSION['mstauth']) && isset($auth) && ($_SESSION['mstauth'] == $auth)) {
    header("location: ".BASE_URL);
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    if(password_verify($username, UNAME) && password_verify($password, HASH)) {
        // Password is correct, so start a new session
        session_start();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $_SESSION['mstauth'] = $hash;

		if (file_exists($session)) {
		  unlink( $session );
		}

		$sessionfile = fopen($session, 'w');
		fwrite($sessionfile, $hash);
		fclose($sessionfile);
		
	    header("location: ".BASE_URL);
	    exit;
        
    } else {
    	$password_err = "Username or password not valid.";	
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Login</title>
	<link rel="stylesheet" href="../style.css" type="text/css" media="all">
</head>
<body class="login">
        <?php
            if (isset($password_err)) {
                echo '<div class="errWrapper">' . $password_err . '</div>';
            }
        ?>
    <div class="wrapper">
        <h2 class="titleSpan">Login</h2>
        <form id="login_form" action="" method="post">
            <div>
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="">
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
    
    	<a class="cancel" href="<?php echo BASE_URL; ?>"><img style="" src="../images/cancel.png" />
    </a>
    
</body>
</html>