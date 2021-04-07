#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";

$infp	= XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$detect_status		= query($infp."/dhcpdetect");
if ($detect_status==1)
{
 	echo "service INET.BRIDGE-1 restart\n";
}
echo "exit 0\n";
?>

