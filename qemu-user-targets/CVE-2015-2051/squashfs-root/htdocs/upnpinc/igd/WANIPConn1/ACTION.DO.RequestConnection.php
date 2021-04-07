<?
$wid = $_GLOBALS["WID"];
if (query("/runtime/device/layout")=="router")
{
	if (query("/upnpigd/wanconnaction")!="0")
	{
		fwrite("a", $_GLOBALS["SHELL_FILE"],
				"echo UPNP connecting WAN".$wid." ... > /dev/console\n".
				"event WAN-".$wid.".DHCP.RENEW\n".
				"event WAN-".$wid.".PPP4.CONNECT\n"
				);
	}
	$_GLOBALS["errorCode"]=200;
}
else
{
	$_GLOBALS["errorCode"]=501;
}
?>
