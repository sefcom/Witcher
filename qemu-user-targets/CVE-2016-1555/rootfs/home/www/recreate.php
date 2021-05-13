<?php


	ob_start();
	
	function getLoginUser()
	{
		$file = '/tmp/sessionid';
		if (!file_exists($file)) return '';
		$fp = fopen($file, "rb");
		if (!$fp) return '';
		$str = file_get_contents($file);
		fclose($fp);
		$strArr = explode(",",$str);
		return $strArr[2].",".$strArr[3].",".$strArr[4];
	}
	function getPrevilige($val)
	{
		if($val==1)
		return "rw";
		else if($val==2)
		return "ro";
		else
		return "0";
	}
	if(isset($_GET['username'])==false){
         header('location:../index.php');
         exit();
     }     
	else
	{
		$passStr = conf_get("system:basicSettings:adminPasswd");
	    $str = explode(' ',$passStr);
			//user1
		$user1 		= 	conf_get("system:userSettings:user1");
		$user1		=	explode(' ',$user1);
		$user1pwd 	= 	conf_get("system:userSettings:user1pword");
		$user1pwd	=	explode(' ',$user1pwd);
		$user1status= 	conf_get("system:userSettings:user1status");
		$user1status=	explode(' ',$user1status);
		$user1status=	getPrevilige($user1status[1]);
		
		//user2
		$user2 		= 	conf_get("system:userSettings:user2");
		$user2		=	explode(' ',$user2);
		$user2pwd 	=	conf_get("system:userSettings:user2pword");
		$user2pwd	=	explode(' ',$user2pwd);
		$user2status= 	conf_get("system:userSettings:user2status");
		$user2status=	explode(' ',$user2status);
		$user2status=	getPrevilige($user2status[1]);
		
		//user3
		$user3 		= 	conf_get("system:userSettings:user3");
		$user3		=	explode(' ',$user3);
		$user3pwd 	= 	conf_get("system:userSettings:user3pword");
		$user3pwd	=	explode(' ',$user3pwd);
		$user3status= 	conf_get("system:userSettings:user3status");		
		$user3status=	explode(' ',$user3status);
		$user3status=	getPrevilige($user3status[1]);
	
		//user4
		$user4 		= 	conf_get("system:userSettings:user4");
		$user4		=	explode(' ',$user4);
		$user4pwd 	=	conf_get("system:userSettings:user4pword");
		$user4pwd	=	explode(' ',$user4pwd);
		$user4status= 	conf_get("system:userSettings:user4status");		
		$user4status=	explode(' ',$user4status);	
		$user4status=	getPrevilige($user4status[1]);
		
		$usernames	=	array($user1[1],$user2[1],$user3[1],$user4[1]);
		$passwords	=	array($user1pwd[1],$user2pwd[1],$user3pwd[1],$user4pwd[1]);
		$previlige	=	array($user1status,$user2status,$user3status,$user4status);	
		

		//system("/usr/local/bin/passwd_check \"".$_REQUEST['username']."\" \"".$_REQUEST['password']."\" >> /dev/null", $authCheck);
		$userpword = $_REQUEST['password'];
		
        if($_GET['username']=='admin' && htmlentities($_REQUEST['password']) == htmlentities(conf_decrypt($str[1]))){
	session_start();
	$loggeduser=getLoginUser();
	@unlink('/tmp/sessionid');
	session_destroy();
	
	session_start();
	$currentuser=explode(",",$loggeduser);
	/*$_SESSION['username']   = $currentuser[0];
	$_SESSION['previlige']   = $currentuser[1];
	$_SESSION['user_logged']   = $currentuser[2];*/
	$_SESSION['username']   = $_REQUEST['username'];
	$fp = fopen('/tmp/sessionid', 'w');
	fwrite($fp, session_id().','.$_SERVER['REMOTE_ADDR'].','.$_SESSION['username']);
	fclose($fp);
	echo 'recreateok';
	}else if ($_REQUEST['username'] != "admin") {
			$userpword = $_REQUEST['password'];
			for($i=0;$i<count($usernames);$i++)
			{
				if ($_REQUEST['username']== $usernames[$i] && $userpword == conf_decrypt($passwords[$i]))
				{
			    	session_start();
	$loggeduser=getLoginUser();
	@unlink('/tmp/sessionid');
	session_destroy();
	
	session_start();
	$currentuser=explode(",",$loggeduser);
	$_SESSION['username']   = $usernames[$i];
	$_SESSION['previlige']	= $previlige[$i];
	$_SESSION['user_logged']="user".$i;	
	$fp = fopen('/tmp/sessionid', 'w');
	fwrite($fp, session_id().','.$_SERVER['REMOTE_ADDR'].','.$_SESSION['username'].','.$_SESSION['previlige'].','.$_SESSION['user_logged']);
	fclose($fp);
	echo 'recreateok';
	
				}
			}
	}
	 else {
         header('location:../index.php');
         exit();
     }
}
	ob_end_flush();
?>
