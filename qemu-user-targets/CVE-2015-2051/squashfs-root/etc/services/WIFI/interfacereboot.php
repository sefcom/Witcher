<?
include "/htdocs/phplib/xnode.php";

function isscheduled($uid)
{
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	$sch = XNODE_getschedule($p);
	return $sch;
}
function schcmd($uid)
{
	/* Get schedule setting */
	$sch = isscheduled($uid);
	if ($sch=="") $cmd = "start";
	else
	{
		$days = XNODE_getscheduledays($sch);
		$start = query($sch."/start");
		$end = query($sch."/end");
		if (query($sch."/exclude")=="1") $cmd = 'schedule!';
		else $cmd = 'schedule';
		$cmd = $cmd.' "'.$days.'" "'.$start.'" "'.$end.'"';
	}
	return $cmd;
}

	echo "#!/bin/sh\n";
	if($UID=="BAND24G-1.1" && isscheduled($UID)!="" && isfile("/var/run/".$UID.".DOWN")==1)
	{
		$2g_host=0;
		$2g_guest=0;
		$5g_host=0;
		$5g_guest=0;
		
		if(isfile("/var/run/BAND5G-1.1.UP")==1)
		{
			echo "service PHYINF.BAND5G-1.1 stop\n";
			$5g_host=1;
		}
		if(isfile("/var/run/BAND5G-1.2.UP")==1)
		{
			echo "service PHYINF.BAND5G-1.2 stop\n";
			$5g_guest=1;
		}
		if(isfile("/var/run/BAND24G-1.2.UP")==1)
		{
			echo "service PHYINF.BAND24G-1.2 stop\n";
			$2g_guest=1;
		}
		if($5g_host==1)
		{
			echo "service PHYINF.BAND5G-1.1 ".schcmd("BAND5G-1.1")."\n";
		}
		if($5g_guest==1)
		{
			echo "service PHYINF.BAND5G-1.2 ".schcmd("BAND5G-1.2")."\n";
		}
		if($2g_guest==1)
		{
			echo "service PHYINF.BAND24G-1.2 ".schcmd("BAND24G-1.2")."\n";
		}
			
	}
	if($UID=="BAND24G-1.2"&& isscheduled($UID)!="" && isfile("/var/run/".$UID.".DOWN")==1)
	{
		$2g_host=0;
		$2g_guest=0;
		$5g_host=0;
		$5g_guest=0;
		
		if(isfile("/var/run/BAND24G-1.1.UP")==1)
		{
			echo "service PHYINF.BAND24G-1.1 stop\n";
			$2g_host=1;
		}
		if(isfile("/var/run/BAND5G-1.1.UP")==1)
		{
			echo "service PHYINF.BAND5G-1.1 stop\n";
			$5g_host=1;
		}
		if(isfile("/var/run/BAND5G-1.2.UP")==1)
		{
			echo "service PHYINF.BAND5G-1.2 stop\n";
			$5g_guest=1;
		}
		if($2g_host==1)
		{
			echo "service PHYINF.BAND24G-1.1 ".schcmd("BAND24G-1.1")."\n";
		}
		if($5g_host==1)
		{
			echo "service PHYINF.BAND5G-1.1 ".schcmd("BAND5G-1.1")."\n";
		}
		if($5g_guest==1)
		{
			echo "service PHYINF.BAND5G-1.2 ".schcmd("BAND5G-1.2")."\n";
		}
	}
	if($UID=="BAND5G-1.1"&& isscheduled($UID)!="" && isfile("/var/run/".$UID.".DOWN")==1)
	{
		$2g_host=0;
		$2g_guest=0;
		$5g_host=0;
		$5g_guest=0;
		
		if(isfile("/var/run/BAND5G-1.2.UP")==1)
		{
			echo "service PHYINF.BAND5G-1.2 stop\n";
			$5g_guest=1;
		}
		if(isfile("/var/run/BAND24G-1.1.UP")==1)
		{
			echo "service PHYINF.BAND24G-1.1 stop\n";
			$2g_host=1;
		}
		if(isfile("/var/run/BAND24G-1.2.UP")==1)
		{
			echo "service PHYINF.BAND24G-1.2 stop\n";
			$2g_guest=1;
		}
		if($5g_guest==1)
		{
			echo "service PHYINF.BAND5G-1.2 ".schcmd("BAND5G-1.2")."\n";
		}
		if($2g_host==1)
		{
			echo "service PHYINF.BAND24G-1.1 ".schcmd("BAND24G-1.1")."\n";
		}
		if($2g_guest==1)
		{
			echo "service PHYINF.BAND24G-1.2 ".schcmd("BAND24G-1.2")."\n";
		}
	}
	if($UID=="BAND5G-1.2"&& isscheduled($UID)!="" && isfile("/var/run/".$UID.".DOWN")==1)
	{
		$2g_host=0;
		$2g_guest=0;
		$5g_host=0;
		$5g_guest=0;
		
		if(isfile("/var/run/BAND5G-1.1.UP")==1)
		{
			echo "service PHYINF.BAND5G-1.1 stop\n";
			$5g_host=1;
		}
		if(isfile("/var/run/BAND24G-1.1.UP")==1)
		{
			echo "service PHYINF.BAND24G-1.1 stop\n";
			$2g_host=1;
		}
		if(isfile("/var/run/BAND24G-1.2.UP")==1)
		{
			echo "service PHYINF.BAND24G-1.2 stop\n";
			$2g_guest=1;
		}
		if($5g_host==1)
		{
			echo "service PHYINF.BAND5G-1.1 ".schcmd("BAND5G-1.1")."\n";
		}
		if($2g_host==1)
		{
			echo "service PHYINF.BAND24G-1.1 ".schcmd("BAND24G-1.1")."\n";
		}
		if($2g_guest==1)
		{
			echo "service PHYINF.BAND24G-1.2 ".schcmd("BAND24G-1.2")."\n";
		}
	}
	echo "exit 0\n";
?>