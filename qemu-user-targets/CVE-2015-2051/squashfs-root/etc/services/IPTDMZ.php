<?
/* DMZ is depends on LAN services.
 * Be sure to start LAN services first. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inf.php";
include "/etc/services/IPTABLES/iptlib.php";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");

/* Get all the LAN interface IP address */
IPT_scan_lan();

$i = 0;
$cnt = query("/nat/count");
if ($cnt=="") $cnt = 0;
TRACE_debug("IPTDMZ: cnt= ".$cnt);
while ($i < $cnt)
{
	$i++;
	anchor("/nat/entry:".$i);
	$UID	= query("uid");
	$CHAIN	= "DNAT.DMZ.".$UID;

	XNODE_set_var($CHAIN.".USED", "0");

	fwrite("a", $START, "iptables -t nat -F ".$CHAIN."\n");
	fwrite("a", $STOP,  "iptables -t nat -F ".$CHAIN."\n");

	$enable	= query("dmz/enable");
	$inf	= query("dmz/inf");
	$hostid	= query("dmz/hostid");
	$sch	= query("dmz/schedule"); 

	if ($enable=="1" && $inf!="" && $hostid!="")
	{
		$lanip	= XNODE_get_var($inf.".IPADDR");
		$mask	= XNODE_get_var($inf.".MASK");
		$ipaddr = ipv4ip($lanip, $mask, $hostid);
		if ($ipaddr!="")
		{
			if ($sch=="") $timecmd = "";
			else $timecmd = IPT_build_time_command($sch);
			fwrite("a", $START, "iptables -t nat -A ".$CHAIN." ".$timecmd." -j DNAT --to-destination ".$ipaddr."\n");
			XNODE_set_var($CHAIN.".USED", "1");
		}
	}
}

fwrite("a", $START, "exit 0\n");
fwrite("a", $STOP,  "exit 0\n");
?>
