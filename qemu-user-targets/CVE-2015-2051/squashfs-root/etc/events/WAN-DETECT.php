<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';

$layout = query("/runtime/device/layout");
del("/runtime/services/wandetect");

if ($layout=="bridge")
{
	/* There is no wan when Device works on bridge mode. */
	set("/runtime/services/wandetect/wantype", "None");
	set("/runtime/services/wandetect/desc", "Bridge Mode");
}
else
{
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$phyinfp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	$linkstatus = query($phyinfp."/linkstatus");
	if ($linkstatus == "")
	{
		/* We can't determine wan type if linkdown. */
		set("/runtime/services/wandetect/wantype", "None");
		set("/runtime/services/wandetect/status", "Link Down");
	}
	else
	{
		del("/runtime/detect/dhcp");
		del("/runtime/detect/pppoe");
		event("PPP.DISCOVER");
		event("DHCP.DISCOVER");
		echo 'xmldbc -t wan_detect_chk:10:"/etc/events/wan_detect_chk.sh '.$INF.'"\n';
	}
}
echo 'exit 0\n';
?>
