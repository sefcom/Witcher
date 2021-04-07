<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$phyinf = query($infp."/phyinf");
$ifname	= PHYINF_getifname($phyinf);

if($ifname=="")
{
	echo "echo WAN_dhcp_dis.php : ifname not found .. Return > /dev/console\n";	
	return;
}
echo "xmldbc -s /runtime/detect/dhcp `udhcpc -i ".$ifname." -d -D 1 -R 3`\n";

?>
