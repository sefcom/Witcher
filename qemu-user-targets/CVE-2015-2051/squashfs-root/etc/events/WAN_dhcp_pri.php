<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';

$helper = "/etc/events/dhcp_pri_chk.sh";
$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$phyinf = query($infp."/phyinf");
$ifname = PHYINF_getifname($phyinf);
$inet = query($infp."/inet");
$inetp = XNODE_getpathbytarget("inet", "entry", "uid", $inet, 0);
$infstsp = XNODE_getpathbytarget("runtime", "inf", "uid", $INF, 0);
echo 'echo infstsp: '.$infstsp.'\n';
$addrtype = query($infstsp."/inet/addrtype");
echo 'echo addrtype: '.$addrtype.'\n';
$dns = query($infstsp."/inet/".$addrtype."/dns");

echo 'chkconn -i '.$ifname.' -n '.$INF.' -d '.$dns.' -H '.'www.dlink.com'.' -s '.$helper.'\n';

echo 'exit 0\n';
?>
