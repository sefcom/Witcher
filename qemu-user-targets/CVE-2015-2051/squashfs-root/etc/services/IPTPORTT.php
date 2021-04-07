<?
include "/htdocs/phplib/trace.php";
include "/etc/services/IPTABLES/iptlib.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

$i = 0;
$cnt = query("/nat/count");
if ($cnt=="") $cnt = 0;
while ($i < $cnt)
{
	$i++;
	$UID = query("/nat/entry:".$i."/uid");
	$CHAIN="PORTT.".$UID;

	XNODE_set_var($CHAIN.".USED", "0");

	fwrite("a",$START, "iptables -t filter -F ".$CHAIN."\n");
	fwrite("a",$START, "iptables -t nat -F DNAT.".$CHAIN."\n");
	fwrite("a",$START, "iptables -t nat -F PFWD.".$UID."\n");
	fwrite("a",$START, "[ -d /var/porttrigger ] || mkdir -p /var/porttrigger\n");
	fwrite("a",$STOP,  "rm -f /var/porttrigger/*\n");
	fwrite("a",$STOP,  "iptables -t filter -F ".$CHAIN."\n");
	fwrite("a",$STOP,  "iptables -t nat -F DNAT.".$CHAIN."\n");
	fwrite("a",$STOP,  "trigger -m flush\n");

	$ei = 0;
	$portt_idx = 1;
	$ecnt = query("/nat/entry:".$i."/porttrigger/count");
	if ($ecnt=="") $ecnt=0;
	$limit=" -m limit --limit 30/m --limit-burst 5";
	$log  =" -j LOG --log-level info --log-prefix PT:".$UID.":";
	while ($ei < $ecnt)
	{
		$ei++;
		anchor("/nat/entry:".$i."/porttrigger/entry:".$ei);

		/* enable ? */
		if (query("enable")!=1) continue;

		/* check trigger port protocol */
		$prot_tcp = 0; $prot_udp = 0; $prot_icmp = 0;
		$prot = query("trigger/protocol");
		if ($prot=="TCP+UDP") {	$prot_tcp++; $prot_udp++; }
		else if	($prot=="TCP")	$prot_tcp++;
		else if	($prot=="UDP")	$prot_udp++;
		else continue;

		/* check trigger port setting */
		$pt_start	= query("trigger/start");	if ($pt_start=="") continue;
		$pt_end		= query("trigger/end");

		/* check external port protocol */
		$prot = query("external/protocol");
		if ($prot=="TCP+UDP")	$ext_prot = "both";
		else if ($prot=="TCP")	$ext_prot = "tcp";
		else if ($prot=="UDP")	$ext_prot = "udp";
		else continue;

		/* port */
		if ($pt_end=="" || $pt_end==$pt_start) $portcmd = "--dport ".$pt_start;	/* Single port forwarding */
		else $portcmd = "-m mport --ports ".$pt_start.":".$pt_end; /* Multi port forwarding */
		/* time */
		$sch = query("schedule");
		if ($sch=="") $timecmd = "";
		else $timecmd = IPT_build_time_command($sch);

		$iptcmd = "iptables -t filter -A ".$CHAIN." ".$timecmd;
		if ($prot_tcp>0)	fwrite("a",$START, $iptcmd." -p tcp ".$portcmd.$limit.$log.$portt_idx.":\n");
		if ($prot_udp>0)	fwrite("a",$START, $iptcmd." -p udp ".$portcmd.$limit.$log.$portt_idx.":\n");
		IPT_setfile($START, "/var/porttrigger/".$portt_idx, $ext_prot.",".query("external/portlist"));
		$portt_idx++;
		XNODE_set_var($CHAIN.".USED", "1");
	}
	/* Add the chain to PFWD.$UID, 
	 * So that the LAN hosts can correctly access the triggering host with IP of WAN. */
	include "/etc/services/_add_chains_to_pfwd.php";
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
