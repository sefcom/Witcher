<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// initialize session
session_start();
 
include("authenticate.php");
 
// check to see if user is logging out
if(isset($_GET['out'])) {
	// destroy session
	session_unset();
	$_SESSION = array();
	unset($_SESSION['user'],$_SESSION['access']);
	session_destroy();
}
 
// check to see if login form has been submitted
if(isset($_POST['userLogin'])){
	// run information through authenticator
	if(authenticate($_POST['userLogin'],$_POST['userPassword']))
	{
		// authentication passed
		header("Location: protected.php");
		die();
	} else {
		// authentication failed
		$error = 1;
	}
}
 
// output error to user
if(isset($error)) echo "Login failed: Incorrect user name, password, or rights<br />";
 
// output logout success
if(isset($_GET['out'])) echo "Logout successful";
?>
 
<form action="login.php" method="post">
	User: <input type="text" name="userLogin" /><br />
	Password: <input type="password" name="userPassword" />
	<input type="submit" name="submit" value="Submit" />
</form>