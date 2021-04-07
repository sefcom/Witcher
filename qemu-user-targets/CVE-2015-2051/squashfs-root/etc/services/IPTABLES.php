<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/etc/services/IPTABLES/iptlib.php";

fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");

/* script starts here !! */
$layout = query("/runtime/device/layout");

/* rt_tables index */
$rtidx = 0;
$rttbl = '/etc/iproute2/rt_tables';

/* stop script */
if ($layout == "router")
{
	IPT_setfile($STOP, "/proc/sys/net/ipv4/ip_forward", "0");
	IPT_killall($STOP, "portt");
	fwrite("a", $START, 'echo -n > '.$rttbl.'\n');
}
IPT_flushall($STOP);
IPT_saverun($STOP, "/etc/scripts/iptables_rmmod.sh");
fwrite("a",$STOP, "exit 0\n");

/* start script */
IPT_saverun($START, "/etc/scripts/iptables_insmod.sh");
fwrite("a", $START, "portt -c DNAT.PORTT &\n");

/* user-defined chains */
/* Move the DOS/SPI from nat to filter table.
 * David Hsieh <david_hsieh@alphanetworks.com> */
IPT_newchain($START, "filter", "DOS");
IPT_newchain($START, "filter", "SPI");
IPT_newchain($START, "nat", "PRE.DOS");
IPT_newchain($START, "nat", "PRE.SPI");
IPT_newchain($START, "nat", "PRE.IGMP");
IPT_newchain($START, "nat", "DNAT.UPNP");
IPT_newchain($START, "filter", "FIREWALL");
IPT_newchain($START, "filter", "FIREWALL-2");
IPT_newchain($START, "filter", "FIREWALL-3");
/*Add by Joseph for Access control and Website filter*/
IPT_newchain($START, "filter", "FOR_POLICY");
/*Add by michael_lee for pptpd*/
IPT_newchain($START, "nat", "PRE.VPN");
IPT_newchain($START, "filter", "FWD.VPN");

/* add chain for Outbound filter */
IPT_newchain($START, "filter", "FWD.OBFILTER");
IPT_newchain($START, "filter", "INP.OBFILTER");

/*add by jeffery for WFA GetDirectServer API*/
IPT_newchain($START, "nat", "PRE.WFA");

/* Create sub-chain for NAT */
$nat = fread("e", "/etc/config/nat");
fwrite(a, $START, "# nat = ".$nat." (Daniel's NAT)\n");
foreach ("/nat/entry")
{
	$uid = query("uid");
	/* user-defined chain for MASQUERADE */
	if ($nat=="Daniel's NAT")
	{
		IPT_newchain($START, "nat", "PRE.MASQ.".$uid);
		IPT_newchain($START, "nat", "PST.MASQ.".$uid);
	}
	else
	{
		IPT_newchain($START, "nat", "MASQ.".$uid);
	}
	/* user-defined chain for PortForwarding */
	IPT_newchain($START, "nat", "DNAT.VSVR.".$uid);
	IPT_newchain($START, "nat", "DNAT.PFWD.".$uid);
	IPT_newchain($START, "nat", "DNAT.DMZ.".$uid);
	IPT_newchain($START, "nat", "DNAT.PORTT.".$uid);
	IPT_newchain($START, "nat", "PFWD.".$uid);
	IPT_newchain($START, "filter", "PORTT.".$uid);
}
/* Create sub-chain for LAN */
$i = 1;
while ($i>0)
{
	$ifname = "LAN-".$i;
	$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($ifpath == "") { $i=0; break; }

	/* define chain */
	IPT_newchain($START, "mangle", "PRE.BWC.".$ifname);
	IPT_newchain($START, "mangle", "PST.BWC.".$ifname);
	/* Bouble add for BWC */
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname);
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname.".HTTP");
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname.".P2P");
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname.".VOICE");
	IPT_newchain($START, "filter", "OUTP.BWC.".$ifname);

	/* define chain */
	IPT_newchain($START, "nat", "PRE.".$ifname);
	
	/* define chain */
	IPT_newchain($START, "filter", "FWD.".$ifname);
	IPT_newchain($START, "filter", "INP.".$ifname);
	IPT_newchain($START, "filter", "MACF.".$ifname);
	IPT_newchain($START, "filter", "URLF.".$ifname);
	IPT_newchain($START, "filter", "DOMAINF.".$ifname);
	$rtidx++;
	fwrite(a,$START, 'echo '.$rtidx.' '.$ifname.' >> '.$rttbl.'\n');

	$i++;
}
/* Create sub-chain for WAN */
$i = 1;
while ($i>0)
{
	$ifname = "WAN-".$i;
	$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($ifpath == "") { $i=0; break; }

	/* define chain */
	IPT_newchain($START, "mangle", "PRE.BWC.".$ifname);
	IPT_newchain($START, "mangle", "PST.BWC.".$ifname);
	/* Bouble add for BWC */
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname);
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname.".HTTP");
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname.".P2P");
	IPT_newchain($START, "filter", "FWD.BWC.".$ifname.".VOICE");
	IPT_newchain($START, "filter", "OUTP.BWC.".$ifname);

	/* define chain */
	IPT_newchain($START, "nat", "PRE.".$ifname);
	IPT_newchain($START, "filter", "FWD.".$ifname);
	IPT_newchain($START, "filter", "INP.".$ifname);
	//IPT_newchain($START, "mangle", "MARK.VSVR.".$ifname);
	//IPT_newchain($START, "mangle", "MARK.PFWD.".$ifname);

	$rtidx++;
	fwrite(a,$START, 'echo '.$rtidx.' '.$ifname.' >> '.$rttbl.'\n');
	$i++;
}
/* Create sub-chain for BRIDGE */
$i = 1;
while ($i>0)
{
	$ifname = "BRIDGE-".$i;
	$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($ifpath == "") { $i=0; break; }
	/* define chain */
	IPT_newchain($START, "nat", "PRE.".$ifname);
	$rtidx++;
	fwrite(a,$START, 'echo '.$rtidx.' '.$ifname.' >> '.$rttbl.'\n');
	$i++;
}

//fwrite(a,$START, "iptables -t mangle -A PREROUTING -j TTL --ttl-inc 1\n");

/* IGMP */
fwrite(a,$START, "iptables -t nat -A PRE.IGMP -d 224.0.0.1 -j ACCEPT\n");


/* Move the DOS/SPI from nat to filter table.
 * David Hsieh <david_hsieh@alphanetworks.com> */
/* DOS */
$limit	= "-m limit --limit 50/s --limit-burst 100";
$logcmd	= "-m limit --limit 10/m -j LOG --log-level notice --log-prefix";
$i = 0;
while ($i<2)
{
	if ($i==0)
		$iptcmd	= "iptables -t nat -A PRE.DOS";
	else
		$iptcmd	= "iptables -A DOS";
	fwrite("a",$START,
		$iptcmd." -p tcp --syn ".$limit." -j RETURN\n".
		$iptcmd." -p tcp --syn ".$logcmd." 'ATT:002[SYN-FLOODING]:'\n".
		$iptcmd." -p tcp --syn -j DROP\n".
		$iptcmd." -p icmp --icmp-type echo-request ".$limit." -j RETURN\n".
		$iptcmd." -p icmp --icmp-type echo-reply ".  $limit." -j RETURN\n".
		$iptcmd." -p icmp --icmp-type echo-request ".$logcmd." 'ATT:002[PING-FLOODING]:'\n".
		$iptcmd." -p icmp --icmp-type echo-reply ".$logcmd." 'ATT:002[PING-FLOODING]:'\n".
		$iptcmd." -p icmp --icmp-type echo-reply -j DROP\n".
		$iptcmd." -p icmp --icmp-type echo-request -j DROP\n"
		);
	$i++;
}

/* SPI */
$drpcmd = "-j DROP";
$logcmd	= "-m limit --limit 10/m -j LOG --log-level notice --log-prefix";
$state	= "-m state --state NEW";
$i = 0;
while ($i<2)
{
	/*
	This should not happen but it DOES happen.
	Is it really an attach or just a normal case ?
	Not sure now, need to find it out .. David.

	$iptcmd = "iptables -t nat -A PRE.SPI -p tcp";
	fwrite("a",$START, $iptcmd." ! --syn ".$state." ".$logcmd." 'ATT:001[Xmas]:'\n");
	fwrite("a",$START, $iptcmd." ! --syn ".$state." ".$drpcmd."\n");
	*/
	if ($i==0)
		$iptcmd	= "iptables -t nat -A PRE.SPI -p tcp --tcp-flags";
	else
		$iptcmd	= "iptables -A SPI -p tcp --tcp-flags";
	fwrite("a",$START,
		$iptcmd." SYN,ACK SYN,ACK ".$state." ".	$logcmd." 'ATT:001[SYN-ACK]:'\n".
		$iptcmd." SYN,ACK SYN,ACK ".$state." ".	$drpcmd."\n".
		$iptcmd." ACK ACK ".$state." ".	        $logcmd." 'ATT:001[SYN-ACK]:'\n".
		$iptcmd." ACK ACK ".$state." ".	        $drpcmd."\n".
		$iptcmd." ALL NONE ".					$logcmd." 'ATT:001[NULL]:'\n".
		$iptcmd." ALL NONE ".					$drpcmd."\n".
		$iptcmd." ALL FIN,URG,PSH ".			$logcmd." 'ATT:001[NMAP]:'\n".
		$iptcmd." ALL FIN,URG,PSH ".			$drpcmd."\n".
		$iptcmd." SYN,RST SYN,RST ".			$logcmd." 'ATT:001[SYN-RST]:'\n".
		$iptcmd." SYN,RST SYN,RST ".			$drpcmd."\n".
		$iptcmd." SYN,FIN SYN,FIN ".			$logcmd." 'ATT:001[SYN-FIN]:'\n".
		$iptcmd." SYN,FIN SYN,FIN ".			$drpcmd."\n".
		$iptcmd." ALL ALL ".					$logcmd." 'ATT:001[Xmas]:'\n".
		$iptcmd." ALL ALL ".					$drpcmd."\n".
		$iptcmd." ALL SYN,RST,ACK,FIN,URG ".	$logcmd." 'ATT:001[Xmas]:'\n".
		$iptcmd." ALL SYN,RST,ACK,FIN,URG ".	$drpcmd."\n"
		);
	$i++;
}

/* Turn IPv4 forward ON if in router mode. */
if ($layout == "router") IPT_setfile($START, "/proc/sys/net/ipv4/ip_forward", "1");
if ($layout == "bridge") IPT_setfile($START, "/proc/sys/net/ipv4/ip_forward", "0");

/* Setup routing tables */
fwrite(a,$START,
	'echo 100 LOCAL >> '.	$rttbl.'\n'.
	'echo 110 DOMAIN >> '.	$rttbl.'\n'.
	'echo 120 DEST >> '.	$rttbl.'\n'.
	'echo 130 STATIC >>'.	$rttbl.'\n'.
	'echo 140 RESOLV >>'.	$rttbl.'\n'.
	'echo 150 IPUNNUMBERED >>'.	$rttbl.'\n'.
	'echo 160 DYNAMIC >>'.   $rttbl.'\n'.
	'echo 170 DHCP >>'.   $rttbl.'\n'.
	'echo 180 CLSSTATICROUTE >>'.	$rttbl.'\n'
	);

fwrite(a, $START,
			'ip rule add table DOMAIN	prio 1000'.
	'\n'.	'ip rule add table DEST		prio 1010'.
	'\n'.	'ip rule add table STATIC	prio 1020'.
	'\n'.	'ip rule add table IPUNNUMBERED	prio 1030'.
	'\n'.   'ip rule add table DYNAMIC  prio 1040'.
	'\n'.   'ip rule add table DHCP  	prio 5000'.
	'\n'.	'ip rule add table RESOLV	prio 6000'.
	'\n'.	'ip rule add table CLSSTATICROUTE	prio 6666'.
//	'\n'.	'ip rule add table LOCAL	prio 6100'.
	'\n'
	);

//marco, add this to pass wins 7 logo session policy test
fwrite(a, $START,'echo 80 > /proc/sys/net/netfilter/nf_conntrack_udp_timeout'.
	'\n'.   'echo 80 > /proc/sys/net/ipv4/netfilter/ip_conntrack_udp_timeout'.
	'\n'
	);
/* exit */
fwrite(a,$START,'exit 0\n');
?>
