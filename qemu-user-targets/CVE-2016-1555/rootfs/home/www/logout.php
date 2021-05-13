<?php
    if ($_REQUEST['logout']=='logout') {
		session_start();
		if ($_REQUEST['emptySession'] == false) {
			$sd = explode(',',@file_get_contents('/tmp/sessionid'));
			if ($sd[0] == session_id())
				@unlink('/tmp/sessionid');
		}
		session_destroy();
		echo 'logoutok';
	}
	else {
		echo 'failed';
	}
?>
