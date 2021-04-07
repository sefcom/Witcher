#!/bin/sh
<?
// This script is used for performing DAD when WANPORT LINKUP.
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$wan_ll_p = XNODE_getpathbytarget("", "inf", "name", "IPv6 LL WAN", "0");
TRACE_debug ("Get the wan_ll = ".$wan_ll_p);

if ($wan_ll_p == "")
{
	TRACE_error("Error: Cannot find the IPv6 link local WAN intf.");
	exit ;
}
if (get ("", $wan_ll_p."/active") == "1")
{
	$WAN_LL_ient_name = get("", $wan_ll_p."/uid");
	$runtime_addrp = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN_LL_ient_name, "0");
	$ll_addr = get ("", $runtime_addrp."/inet/ipv6/ipaddr");
	$ll_devname = get ("", $runtime_addrp."/devnam");
	
	echo "ip -6 addr del ".$ll_addr."/64 dev ".$ll_devname." \n";
	echo "ip -6 addr add ".$ll_addr."/64 dev ".$ll_devname." \n";
}
echo "exit 0 \n";

?>
