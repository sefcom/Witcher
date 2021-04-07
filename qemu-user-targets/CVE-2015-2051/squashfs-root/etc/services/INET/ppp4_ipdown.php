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
	$inetuid = query($stsp."/inet/uid");
	$addrtype= query($stsp."/inet/addrtype");
	del($stsp."/inet");
	set($stsp."/inet/uid", $inetuid);
	set($stsp."/inet/addrtype", $addrtype);
}
/* Destroy phyinf */
$stsp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "PPP.".$PARAM, 0);
if ($stsp != "") del($stsp);

/* Delete this network in 'LOCAL' */
echo 'ip route del '.$REMOTE.' dev '.$IFNAME.' src '.$IP.' table LOCAL\n';

echo "ip route flush table ".$PARAM."\n";
echo "event ".$PARAM.".DOWN\n";
echo "rm -f /var/run/".$PARAM.".UP\n";

/* 3G connection mode */
$infp = XNODE_getpathbytarget("", "inf", "uid", $PARAM, 1);
if ($infp == "") exit;
$inet = query($infp."/inet");
if ($inet == "") exit;
$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
if ($inetp != "")
{
	$over = query($inetp."/ppp4/over");
	if ($over=="tty")
	{
		echo "event TTY.DOWN\n";
	} 
}
?>
exit 0
