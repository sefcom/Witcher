<?php 
if (empty($_REQUEST['action'])) {

    echo 'failed';

}else
{
if($_REQUEST['action']=='passwo'){	
session_start();
		system("/usr/local/bin/passwd_check admin password  >> /dev/null", $authCheck);
		if($authCheck=='0' && $_SESSION['username']=='admin')
		$pswd="password";
		echo $pswd;

}
}
?>