#!/bin/sh
<? /* $IFNAME, $DEVICE, $SPEED, $IP, $REMOTE, $PARAM */
include "/htdocs/phplib/xnode.php";

/*add 3g connecttime*/
$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $PARAM, 1);
if ($infp == "") exit;

$UP_TIME        = query($infp."/inet/uptime");
$SYS_TIME       = query("/runtime/device/uptime");
$OLD_TIME       = query("/runtime/device/connecttime");
if ($UP_TIME != "")
	{ $CONN_TIME = $SYS_TIME - $UP_TIME;}
else {$CONN_TIME = 0;}

if ($OLD_TIME != "")
	{ $OLD_TIME = $CONN_TIME + $OLD_TIME;}
else { $OLD_TIME = $CONN_TIME; }
set("/runtime/device/connecttime", $OLD_TIME);

/* Destroy inf */
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $PARAM, 0);
if ($stsp != "")
{
	del($stsp."/inet");
	del($stsp."/phyinf");
	del($stsp."/child");
}
/* Destroy phyinf */
$stsp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "PPP.".$PARAM, 0);
if ($stsp != "") del($stsp);

echo "ip -6 route flush dev ".$IFNAME."\n";
echo "event ".$PARAM.".DOWN\n";
echo "rm -f /var/run/".$PARAM.".UP\n";

$base = "/runtime/dynamic/route6";
$cnt = query($base."/entry#");
$i=1;
while($i<=$cnt)
{
	$dest = query($base."/entry:".$i."/ipaddr");
	$pfx = query($base."/entry:".$i."/prefix");
	$inf = query($base."/entry:".$i."/inf");

	if($dest=="::" && $pfx=="0" && $inf=="PPP")
		del($base."/entry:".$i);

	$i++;
}
?>
exit 0
