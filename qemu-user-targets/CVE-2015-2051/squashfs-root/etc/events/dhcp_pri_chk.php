<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';

echo 'echo PHYINF: '.$PHYINF.'> /dev/console\n';
echo 'echo INF: '.$INF.'> /dev/console\n';
echo 'echo CONN: '.$CONN.'> /dev/console\n';

$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
//$phyinf = query($infp."/phyinf");
$inet = query($infp."/inet");
$inetp = XNODE_getpathbytarget("inet", "entry", "uid", $inet, 0);
$addrtype = query("/runtime/services/wandetect/originet/addrtype");
$static = query("/runtime/services/wandetect/originet/ipv4/static");
set($inetp."/addrtype", $addrtype);
set($inetp."/ipv4/static", $static);

if($CONN=="connected")
{
	echo 'xmldbc -s /runtime/services/wandetect/wantype "DHCP"\n';
	echo 'xmldbc -s /runtime/services/wandetect/desc "Private IP"\n';
	echo 'event INFSVCS.'.$INF.'.UP add true\n';
}
else if($CONN=="disconnected")
{
	echo 'event INFSVCS.'.$INF.'.UP add "event PPP.DISCOVER"\n';
}

echo 'service WAN restart\n';

echo 'exit 0\n';
?>
