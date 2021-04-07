<NewConnectionStatus><?
	include "/htdocs/phplib/xnode.php";

	$_GLOBALS["errorCode"]=200;
	
	if ($WID=="") $WID=1;
	if (query("/runtime/device/layout")=="router")
	{
		$p = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-".$WID, 0);
		$t = query($p."/inet/addrtype");
		if (query($p."/inet/".$t."/valid")==1) echo "Connected";
		else echo "Disconnected";
	}
	else
	{
		echo "Connected";
	}
	
?></NewConnectionStatus>
<NewLastConnectionError>ERROR_NONE</NewLastConnectionError>
<NewUptime><?

	$v1 = query("/runtime/device/uptime");
	$v2 = query($p."/inet/uptime");
	$uptime = $v1 - $v2;
	echo $uptime;

?></NewUptime>

