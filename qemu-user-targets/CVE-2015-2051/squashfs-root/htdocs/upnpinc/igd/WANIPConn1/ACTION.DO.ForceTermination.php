<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$wid = $_GLOBALS["WID"];
if (query("/runtime/device/layout")=="router")
{
	if (query("/upnpigd/wanconnaction")!=0)
	{
		/* there is only one envent below will be executed. */
		fwrite("a", $_GLOBALS["SHELL_FILE"],
			"echo UPNP disconnecting WAN-".$wid." ... > /dev/console\n".
			"event WAN-".$wid.".DHCP.RELEASE\n".
			"event WAN-".$wid.".PPP4.DISCONNECT\n"
			);
	}
	$_GLOBALS["errorCode"] = 200;
}
else
{
	$_GLOBALS["errorCode"] = 501;
}
?>

