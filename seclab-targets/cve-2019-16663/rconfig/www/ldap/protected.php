<?php
// initialize session
session_start();
 // var_dump($_SESSION);
if(!isset($_SESSION['user'])) {
	// user is not logged in, do something like redirect to login.php
	header("Location: login.php");
	die();
}
 
if($_SESSION['access'] != 2) {
	// another example...
	// user is logged in but not a manager, let's stop him
	die("Access Denied");
}
?>
 
<p>Welcome <?= $_SESSION['user'] ?>!</p>
 
<p><strong>Secret Protected Content Here!</strong></p>
 
<p>Mary Had a Little Lamb</p>
 
<p><a href="login.php?out=1">Logout</a></p>