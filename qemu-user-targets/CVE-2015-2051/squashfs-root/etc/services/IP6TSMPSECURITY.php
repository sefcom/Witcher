<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/etc/services/IP6TABLES/ip6tlib.php";

function startcmd($cmd) { fwrite(a, $_GLOBALS["START"], $cmd."\n"); }
function stopcmd($cmd)  { fwrite(a, $_GLOBALS["STOP"],  $cmd."\n"); }
function debug_SmpSecurity($debug_msg, $enable)  
{
	if ($enable >0)
		startcmd("# ".$debug_msg); 
}

function LAN_security($stsp, $name, $dev, $CHAIN, $CHAIN_CommFWD, $CHAIN_CommINP, $CHAIN_CommOUT, $debug)
{
	/* Special Address Definition  */	
	$gl_uni_pfx = "2000::/4";	// Global unicast prefix
	$mcast_addr = "FF00::/8";	// Multicast address
			
	
	$ip6t_FWD_cmd = "ip6tables -t filter -A FWD".$CHAIN;
	$ip6t_INP_cmd = "ip6tables -t filter -A INP".$CHAIN;
	$ip6t_OUT_cmd = "ip6tables -t filter -A OUT".$CHAIN;		
	
	
	anchor($stsp."/inet");
	$addrtype = query("addrtype");
	//if ($addrtype=="ipv6" && query("ipv6/valid")=="1")		/* Check if IPv6 */
	if ($addrtype=="ipv6" && query("ipv6/valid")=="1" && query("ipv6/mode")!="LL")
	{ 
		anchor($stsp."/dhcps6");
		$subnets6 = query("network");
		$prefixs6 = query("prefix");
		
		startcmd("# Rules in: ". $name);
		
	/* FORWARD */		
		if($subnets6!="" && $prefixs6!="")
		{	
			/* Reverse Path Filtering */
			debug_SmpSecurity("Reverse Path Filtering", $debug);		
			startcmd($ip6t_FWD_cmd. " ! -s ". $subnets6."/". $prefixs6 ." -j DROP");
		}
		startcmd($ip6t_FWD_cmd. " -j ". $CHAIN_CommFWD);
		
	/* INPUT */
		startcmd($ip6t_INP_cmd. " -j ". $CHAIN_CommINP);
		
	/* OUTPUT */	
		/* REC-1 */
		debug_SmpSecurity("REC-1: MUST NOT fwd/tx pkt with src is multicast addr.", $debug);
		startcmd($ip6t_OUT_cmd. " -s ". $mcast_addr ." -j DROP");				
		startcmd($ip6t_OUT_cmd. " -j ". $CHAIN_CommOUT);
							
	}	
	else 
		return;	
}

function WAN_security($stsp, $name, $dev, $CHAIN, $CHAIN_CommFWD, $CHAIN_CommINP, $CHAIN_CommOUT, $debug)
{
	/* Special Address Definition  */
	$gl_uni_pfx = "2000::/4";	// Global unicast prefix.		
	
	$ip6t_FWD_cmd = "ip6tables -t filter -A FWD".$CHAIN;
	$ip6t_INP_cmd = "ip6tables -t filter -A INP".$CHAIN;
	$ip6t_OUT_cmd = "ip6tables -t filter -A OUT".$CHAIN;
			
	
	anchor($stsp."/inet");
	$addrtype = query("addrtype");
	//if ($addrtype=="ipv6" && query("ipv6/valid")=="1")		/* Check if IPv6 */
	if ($addrtype=="ipv6" && query("ipv6/valid")=="1" && query("ipv6/mode")!="LL")	
	{ 
		anchor($stsp."/child");
		$subnets6 = query("pdnetwork");
		$prefixs6 = query("pdprefix");
		
		startcmd("# Rules in: ". $name);
					
	/* FORWARD */		
		/* FEC-6 */
		debug_SmpSecurity("REC-6: MUST NOT fwd the inBound pkt with src addr that is a global unicast prefix assifned by interior network.", $debug);
		if($subnets6!="" && $prefixs6!="")
		{
			startcmd($ip6t_FWD_cmd. " -s ". $subnets6."/". $prefixs6 ." -j DROP");
		}
	
		startcmd($ip6t_FWD_cmd. " -j ". $CHAIN_CommFWD);

	/* INPUT */	
		/* FEC-8, 9 */
		debug_SmpSecurity("REC-8,9: MUST NOT process inBound DNS queries and DHCPv6 discovery pkts.", $debug);		
		startcmd($ip6t_INP_cmd. " -p udp -m multiport --dports 53,547 -j DROP");

		/* REC-50 */
		debug_SmpSecurity("REC-50: By Default, gateway MUST NOT offer management application services to the exterior network.", $debug);
		/* We don't allow user to do remote management */
		//startcmd($ip6t_INP_cmd. " -p tcp --dport 80 -j DROP");	
		startcmd($ip6t_INP_cmd. " -j ". $CHAIN_CommINP);
		
	/* OUTPUT */
		startcmd($ip6t_OUT_cmd. " -j ". $CHAIN_CommOUT);
	}	
	else 
		return;					
	
}

function Comm_chain($CHAIN_FWD, $CHAIN_INP, $CHAIN_OUT, $debug)
{
	/* Special Address Definition  */
	$gl_uni_pfx  = "2000::/4";			// Global unicast prefix
	$ULA_addr    = "FD00::/8";			// Unique local address ("FC00::/7" in RFC-4193)	
	
	$mcast_addr  = "FF00::/16";			// Multicast address
	$mcast_nl    = "FF01::/16";			// Node-Local multicast address
	$mcast_ll    = "FF02::/16";			// Link-Local multicast address
	$mcast_sl    = "FF05::/16";			// Site-Local multicast address
	
	$ucast_ll    = "FE80::/10";			// Link-Local unicast address
	$ucast_sl    = "FEC0::/10";			// Site-Local unicast address
	$v4_maped    = "::/96";				// IPv4 Mapped address
	$v4_comp     = "::FFFF/96";			// IPv4 Compatible address		
	$Doc_pfx     = "2001:DB8::/32";		// Doc prefix (RFC-3849)	
	$ORCHID_addr = "2001:10::/28";		// Overlay Routable Cryptographic Hash Identifiers (RFC-4843)
	
	
	$ip6t_fwd_cmd = "ip6tables -t filter -A ".$CHAIN_FWD;
	$ip6t_inp_cmd = "ip6tables -t filter -A ".$CHAIN_INP;
	$ip6t_out_cmd = "ip6tables -t filter -A ".$CHAIN_OUT;
	
	startcmd("# Common Chain: ");
			
	/* REC-1 */
	debug_SmpSecurity("REC-1: MUST NOT fwd/tx pkt with src is multicast addr.", $debug);
	startcmd($ip6t_fwd_cmd. " -s ". $mcast_addr ." -j DROP");
	//startcmd($ip6t_out_cmd. " -s ". $mcast_addr ." -j DROP");
	
	/* REC-2 */
	debug_SmpSecurity("REC-2: MUST NOT fwd multicast dest addr <= organization-local scope (FF08).", $debug);
	startcmd($ip6t_fwd_cmd. " -d ". $mcast_nl . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $mcast_ll . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $mcast_sl . " -j DROP");
	
	
	/* REC-3 */
	debug_SmpSecurity("REC-3: MUST NOT fwd/tx src/dest = Site-local, IPv4-mapped and -compatible, Doc prefix, and ORCHID over public internet.", $debug);
	startcmd($ip6t_fwd_cmd. " -s ". $ucast_sl . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $ucast_sl . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -s ". $v4_maped . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $v4_maped . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -s ". $v4_comp . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $v4_comp . " -j DROP");		
	startcmd($ip6t_fwd_cmd. " -s ". $Doc_pfx . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $Doc_pfx . " -j DROP");	
	startcmd($ip6t_fwd_cmd. " -s ". $ORCHID_addr . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $ORCHID_addr . " -j DROP");

	/* REC-4 */
	debug_SmpSecurity("REC-4: MUST NOT fwd/tx the pkt with routing extension header type 0.", $debug);
	//startcmd($ip6t_fwd_cmd. " -m ipv6header --header hop -j DROP");
	//startcmd($ip6t_out_cmd. " -m ipv6header --header hop -j DROP");
	startcmd($ip6t_fwd_cmd. " -m rt --rt-type 0 -j DROP");
	startcmd($ip6t_inp_cmd. " -m rt --rt-type 0 -j DROP");
	startcmd($ip6t_out_cmd. " -m rt --rt-type 0 -j DROP");

	/* REC-5 */
	debug_SmpSecurity("REC-5: MUST NOT fwd the outBound pkt's src does not have a unicast prefix.", $debug);	
	startcmd($ip6t_fwd_cmd. " ! -s ". $gl_uni_pfx ." -j DROP");
	
	/* REC-7 */
	debug_SmpSecurity("REC-7: SHOULD NOT fwd pkt with unique local src/dest addr (ULA) to or from the exterior network.", $debug);
	startcmd($ip6t_fwd_cmd. " -s ". $ULA_addr . " -j DROP");
	startcmd($ip6t_fwd_cmd. " -d ". $ULA_addr . " -j DROP");
		
	/* REC-10 */
	debug_SmpSecurity("REC-10: SHOULD NOT fwd ICMPv6 destination-unreachable and packet-too-big if not match generic upper-layer transport state record.", $debug);
	//startcmd($ip6t_fwd_cmd. " -p icmpv6 --icmpv6-type destination-unreachable -j DROP");
	//startcmd($ip6t_fwd_cmd. " -p icmpv6 --icmpv6-type packet-too-big -j DROP");
	startcmd($ip6t_fwd_cmd. " -m state ! --state ESTABLISHED,RELATED -p icmpv6 --icmpv6-type destination-unreachable -j DROP");
	startcmd($ip6t_fwd_cmd. " -m state ! --state ESTABLISHED,RELATED -p icmpv6 --icmpv6-type packet-too-big -j DROP");

	/* Synfrag attack */
	startcmd($ip6t_fwd_cmd. " -m ipv6header --shortheader -j DROP");
	
	/* REC-11 */
	debug_SmpSecurity("REC-11: A stateful UDP Filters should have endpoint-independent filter and address-dependent filtering for management. Default is endpoint independent.", $debug);

	/* REC-12 */
	debug_SmpSecurity("REC-12: Idle time of state records for generic upper-layer transport  protocol shouldn't less than two minuates.", $debug);

	/* REC-13 */
	debug_SmpSecurity("REC-13: Provide a convenient means to update fw.", $debug);

	/* REC-14 */
	debug_SmpSecurity("REC-14: A stateful UDP Filters where both src/dest ports are outside the well-know range.", $debug);
	// NOT expire in less than 2 minutes (Default 5 minutes) ?
	//startcmd($ip6t_fwd_cmd. " -p udp -m state --state NEW ! --dport 0:1023 -j ACCEPT");
	
	/* REC-15 */
	debug_SmpSecurity("REC-15: A stateful UDP Filters where both src/dest ports are in the well-know range.", $debug);
	//startcmd($ip6t_fwd_cmd. " -p udp -m state --state NEW --dport 0:1023 -j ACCEPT");
	// MAY expire after a idle time shorter than 2 minutes ?
	startcmd("echo 150 > /proc/sys/net/netfilter/nf_conntrack_udp_timeout");
	startcmd("echo 300 > /proc/sys/net/netfilter/nf_conntrack_udp_timeout_stream");
	stopcmd("echo 80 > /proc/sys/net/netfilter/nf_conntrack_udp_timeout");
	stopcmd("echo 180 > /proc/sys/net/netfilter/nf_conntrack_udp_timeout_stream");
	
	/* REC-16 */
	debug_SmpSecurity("REC-16: A UDP state record MUST be refreshed when pkt fwd from interior to exterior.", $debug);
	
	/* REC-17 */
	debug_SmpSecurity("REC-17: Filtering behavior SHOULD be endpoint-independent by DEFAULT in gateways.", $debug);
	
	/* REC-18 */
	debug_SmpSecurity("REC-18: If fwd a UDP flow, it MUST also fwd ICMPv6 pkts below.", $debug);
	startcmd($ip6t_fwd_cmd. " -p udp -m state --state ESTABLISHED,RELATED -j ACCEPT");	
	startcmd($ip6t_fwd_cmd. " -m state --state ESTABLISHED,RELATED -p icmpv6 --icmpv6-type destination-unreachable -j ACCEPT");
	startcmd($ip6t_fwd_cmd. " -m state --state ESTABLISHED,RELATED -p icmpv6 --icmpv6-type packet-too-big -j ACCEPT");
	
	/* REC-19 */
	debug_SmpSecurity("REC-19: Recept of any sort of ICMPv6 message MUST NOT terminate the UDP state record.", $debug);
	
	
	/* REC-21, 22 */
	debug_SmpSecurity("REC-21, 22: MUST NOT prohibit to fwd the pkt with dest extension header of type AH and ESP.", $debug);
	startcmd($ip6t_fwd_cmd. " -m ipv6header --header auth -j ACCEPT");
	startcmd($ip6t_fwd_cmd. " -m ipv6header --header esp -j ACCEPT");	
	//startcmd($ip6t_fwd_cmd. " -m ipv6header --header esp -m state --state NEW -j ACCEPT");	

	/* REC-23 */
	debug_SmpSecurity("REC-23: If fwd an ESP flow, MUST also fwd ICMPv6 pkts below.", $debug);
	debug_SmpSecurity("REC-23: The same as REC-18.", $debug);
	//startcmd($ip6t_fwd_cmd. " -m ipv6header --header esp -m state --state ESTABLISHED,RELATED -j ACCEPT");	
	//startcmd($ip6t_fwd_cmd. " -p icmpv6 --icmpv6-type destination-unreachable -j ACCEPT");
	//startcmd($ip6t_fwd_cmd. " -p icmpv6 --icmpv6-type packet-too-big -j ACCEPT");
	
	/* REC-24 */
	debug_SmpSecurity("REC-24: MUST NOT prohibit any UDP pkt with port 500 (for IKE protocol).", $debug);
	startcmd($ip6t_fwd_cmd. " -p udp --dport 500 -j ACCEPT");
		
	/* REC-31 */
	debug_SmpSecurity("REC-31: MUST fwd all valid (TCP 3-way and simultaneous-open) sequences of TCP pkts.", $debug);
	//startcmd($ip6t_fwd_cmd. " -p tcp --syn -m state --state ESTABLISHED,RELATED -j ACCEPT");
	startcmd($ip6t_fwd_cmd. " -m state --state ESTABLISHED,RELATED -j ACCEPT");//rbj
	
	/* REC-33 */
	debug_SmpSecurity("REC-33: MAY provide filtering behavior options (endpoint- or address-independent) by administrator. Filting behavior SHOULD be endpoint independent in gateway.", $debug);
	
	/* REC-36 */
	debug_SmpSecurity("REC-36: If fwd a TCP flow, MUST also fwd ICMPv6 pkts below.", $debug);
	debug_SmpSecurity("REC-36 is the same as REC-18", $debug);
	//startcmd($ip6t_fwd_cmd. " -p tcp -m state --state ESTABLISHED,RELATED -j ACCEPT");	
	//startcmd($ip6t_fwd_cmd. " -p icmpv6 --icmpv6-type destination-unreachable -j ACCEPT");
	//startcmd($ip6t_fwd_cmd. " -p icmpv6 --icmpv6-type packet-too-big -j ACCEPT");
	
	/* REC-37 */
	debug_SmpSecurity("REC-37: Receipt of any sort of ICMPv6 message MUST NOT terminate the state record for TCP flow.", $debug);
	/* REC-48 */
	debug_SmpSecurity("REC-48: Internet gateways with IPv6 simple security capabilities SHOULD implement a protocol to permit applications to solicit inbound traffic without advance knowledge of the address of exterior node with which they expect to commu.", $debug);
	debug_SmpSecurity("UPNP-IGD", $debug);

	/* REC-49 */
	debug_SmpSecurity("REC-49: IPv6 Simple security capablities MUST provide a easily selected configuration option. The transparent mode of operation MAY be the default configuration", $debug);
	debug_SmpSecurity("Simple Security is disabled by default", $debug);

	/* REC-50 */
	//debug_SmpSecurity("REC-50: By Default, gateway MUST NOT offer management application services to the exterior network.", $debug);
	//startcmd($ip6t_fwd_cmd. " -m state --state NEW -j DROP");	
}


/**************************************************************************/
fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");

/* Create and flush Simple-Security chain */
$CHAIN_CommFWD = 'FWD.SMPSECURITY';
$CHAIN_CommINP = 'INP.SMPSECURITY';
$CHAIN_CommOUT = 'OUT.SMPSECURITY';
$CHAIN_INP     = 'INP.SMPSECURITY.';
$CHAIN_OUT     = 'OUT.SMPSECURITY.';
$CHAIN_FWD     = 'FWD.SMPSECURITY.';
$CHAIN_pfx     = '.SMPSECURITY.';

startcmd("ip6tables -t filter -F ". $CHAIN_CommFWD);
stopcmd("ip6tables -t filter -F ".  $CHAIN_CommFWD);
startcmd("ip6tables -t filter -F ". $CHAIN_CommINP);
stopcmd("ip6tables -t filter -F ".  $CHAIN_CommINP);
startcmd("ip6tables -t filter -F ". $CHAIN_CommOUT);
stopcmd("ip6tables -t filter -F ".  $CHAIN_CommOUT);

foreach ("/runtime/inf")	$cnt++;

$i=1;
while ($i<=$cnt)
{			
	$name = "LAN-".$i;	 				
	
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);	/* If LAN activated ? */
	if ($stsp!="")
	{
		anchor($stsp."/inet");
		$addrtype = query("addrtype");
		if ($addrtype=="ipv6" && query("ipv6/valid")=="1") 
		{							 
			startcmd("ip6tables -t filter -F ". $CHAIN_INP.$name);			
			startcmd("ip6tables -t filter -F ". $CHAIN_OUT.$name);	
			startcmd("ip6tables -t filter -F ". $CHAIN_FWD.$name);	
			
			stopcmd("ip6tables -t filter -F ". $CHAIN_INP.$name);			
			stopcmd("ip6tables -t filter -F ". $CHAIN_OUT.$name);	
			stopcmd("ip6tables -t filter -F ". $CHAIN_FWD.$name);			
		}
	}
	
	$i++;	/* Advance to next */
}

$i=1;
while ($i<=$cnt)
{			
	$name = "WAN-".$i;	 				
	
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);	/* If WAN activated ? */
	if ($stsp!="")
	{
		anchor($stsp."/inet");
		$addrtype = query("addrtype");
		if ($addrtype=="ipv6" && query("ipv6/valid")=="1") 
		{							 
			startcmd("ip6tables -t filter -F ". $CHAIN_INP.$name);			
			startcmd("ip6tables -t filter -F ". $CHAIN_OUT.$name);	
			startcmd("ip6tables -t filter -F ". $CHAIN_FWD.$name);	
			
			stopcmd("ip6tables -t filter -F ". $CHAIN_INP.$name);			
			stopcmd("ip6tables -t filter -F ". $CHAIN_OUT.$name);	
			stopcmd("ip6tables -t filter -F ". $CHAIN_FWD.$name);			
		}
	}
	
	$i++;	/* Advance to next */
}


$smp_security = query("/device/simple_security");

if ($smp_security == "1")
{
	$debug_SmpSecurity = 0;		// 1: show DEBUG msg.
	
	Comm_chain($CHAIN_CommFWD, $CHAIN_CommINP, $CHAIN_CommOUT, $debug_SmpSecurity);

	$layout = query("/runtime/device/layout");
	if ($layout == "router")
	{				
		/* Walk through all the actived LAN interfaces. */
		startcmd("# LAN interfaces");
		
		foreach ("/runtime/inf")	$count++;
		
		$i=1;
		while ($i<=$count)
		{			
			$name = "LAN-".$i;	/* If LAN exist ? */			
			$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
			if ($infp=="") break;
				
			$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);	/* If LAN activated ? */
			if ($stsp!="")
			{
				anchor($stsp."/inet");
				$addrtype = query("addrtype");
				if ($addrtype=="ipv6" && query("ipv6/valid")=="1") 
				{				
					$laninf = PHYINF_getruntimeifname($name);	/* Get phyinf */			 			
					if ($laninf!="")
					{ 
						LAN_security($stsp, $name, $laninf, $CHAIN_pfx.$name, $CHAIN_CommFWD, $CHAIN_CommINP, $CHAIN_CommOUT, $debug_SmpSecurity);						
					}
				}
			}
			
			$i++;	/* Advance to next */
		}
		
		
		/* Walk through all the actived WAN interfaces. */
		startcmd("# WAN interfaces");
		
		$i = 1;
		while ($i<=$count)
		{			
			$name = "WAN-".$i;	/* If WAN exist ? */
			$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
			if($infp=="") break;
				
			$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);	/* If WAN activated ? */
			if ($stsp!="")
			{
				anchor($stsp."/inet");
				$addrtype = query("addrtype");
				if ($addrtype=="ipv6" && query("ipv6/valid")=="1") 
				{					
					$waninf = PHYINF_getruntimeifname($name);		/* Get phyinf */
					if ($waninf!="") 
					{
						WAN_security($stsp, $name, $waninf, $CHAIN_pfx.$name, $CHAIN_CommFWD, $CHAIN_CommINP, $CHAIN_CommOUT, $debug_SmpSecurity);						
					}
				}
			}
			
			$i++;	/* Advance to next */
		}
		
	}	// if ($layout == "router")
}

fwrite("a", $START, "exit 0\n");
fwrite("a", $STOP,  "exit 0\n");
?>
