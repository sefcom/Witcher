<?php
function checkSessionLink()
{
	if(file_exists('/tmp/sessionid')) {
		return true;
	}
	else {
		return false;
	}
}
function checkSessionExpired()
{
	if (checkSessionLink()) {
		$sd = explode(',',@file_get_contents('/tmp/sessionid'));
					if ($sd[0] != session_id() || (time()-@filemtime('/tmp/sessionid'))>300)
			return true;
		else 
			return false;
	}
	else
		return true;
}

session_start();
if (checkSessionExpired()) {
	@header('location:index.php');
	exit();
}
?>