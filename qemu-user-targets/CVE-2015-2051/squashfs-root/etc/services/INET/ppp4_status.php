#!/bin/sh
<?
/* $IFNAME, $STATUS, $SESSID, $MTU */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$infp = XNODE_getpathbytarget("", "inf", "uid", $IFNAME, 0);
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $IFNAME, 1);

if($IFNAME=="DISCOVER")
{
	if($STATUS=="PPPoE:PADO")
	{
		set("/runtime/services/wandetect/ppp/".$IFNAME."/PADO", "1");
		echo "echo PADO > /var/PCI_WANDETECT.result \n";
	}
	else if($STATUS=="connected")
	{
		set("/runtime/services/wandetect/ppp/".$IFNAME."/connected", "1");
	}
	else if($STATUS=="authFailed")
	{
		set("/runtime/services/wandetect/ppp/".$IFNAME."/authFailed", "1");
	}
}

if($STATUS=="PPPoE:PADI" || $STATUS=="PPPoE:PADO")
{
	set($stsp."/pppd/process", $STATUS);
}
else if($STATUS=="authFailed")
{
	set($stsp."/pppd/process", $STATUS);
	set($stsp."/pppd/process2", $STATUS);	
	echo "event ".$IFNAME.".PPP.AUTHFAILED\n";
}

echo "# status=".$STATUS.", sessid=".$SESSID.", MTU=".$MTU."\n";
set($stsp."/pppd/status", $STATUS);
set($stsp."/pppd/sessid", $SESSID);
set($stsp."/pppd/mtu", $MTU);

if ($STATUS == "on demand")
{
	$fakedns = "10.112.113.".cut($IFNAME, 1, "-");
	$default = query($infp."/defaultroute");
	if ($default!="" && $default>1)
	{
		echo "ip route add default via ".$fakedns." metric ".$default." table default\n";
	}
	else
	{
		echo "ip route add default via ".$fakedns." table default\n";
	}
	set($stsp."/inet/addrtype", "ppp4");
	set($stsp."/inet/ppp4/valid", "1");
	set($stsp."/inet/ppp4/peer",	$fakedns);
	set($stsp."/inet/ppp4/dns",		$fakedns);
	set($stsp."/defaultroute",		$default);
	echo "event UPDATERESOLV\n";
	echo "event ".$IFNAME.".PPP.ONDEMAND\n";

	/*+++, Builder, 2012/04/10 */
	/* pppd won't be triggered when (PPtP,L2TP/on demand) because ip_forward isn't set. */
	/* There is the same issue when runtime change from (PPtP,L2TP/on demand) to (PPPoE/on demand)*/
	echo "echo 1 > /proc/sys/net/ipv4/ip_forward\n";
	/*+++*/
}
?>
exit 0;
