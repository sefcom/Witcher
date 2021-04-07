<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/inf.php";

function dhcps4start($inf, $phyinf, $dhcpsp)
{
	anchor($dhcpsp);

	/* File names */
	$udhcpd_conf  = "/var/servd/".$inf."-udhcpd.conf";
	$udhcpd_pid   = "/var/servd/".$inf."-udhcpd.pid";
	$udhcpd_lease = "/var/servd/".$inf."-udhcpd.lease";

	/* the interface status */
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);

	/* Get the network info. */
	$ifname = PHYINF_getifname($phyinf);
	$net	= query("network");
	$mask	= query("mask");
	$router = query("router");
	if ($net=="" || $mask=="")
	{
		/* If there is not network setting, this should be the router application.
		* Use the interface as the network and router.*/
		$net	= query($stsp."/inet/ipv4/ipaddr");
		$mask	= query($stsp."/inet/ipv4/mask");
		if ($router=="") $router = query($stsp."/inet/ipv4/ipaddr");
	}

	/* Get the pool setting */
	$start	= query("start");
	$end	= query("end");
	$domain	= query("domain");
	$lease	= query("leasetime");
	$subnet	= ipv4int2mask($mask);	
	
	/*sam_pan add*/
	$broadcast = query("broadcast");	
	
	if($broadcast!="yes") {$broadcast="no";}
	$poolstart	= ipv4ip($net, $mask, $start);
	$poolend	= ipv4ip($net, $mask, $end);

	/* If no domain setting, we use the domain name in /runtime/device.
	   This node might be set by the DHCP client of the WAN port. */
	if ($domain == "") $domain = query("/runtime/device/domain");

	/* If the network address is changed, clear the leases */
	$curr_net = query($stsp."/dhcps4/pool/network");
	$curr_mask = query($stsp."/dhcps4/pool/mask");
	if (ipv4networkid($net, $mask) != ipv4networkid($curr_net, $curr_mask))
	{
		fwrite("a",$_GLOBALS["START"], "rm -f ".$udhcpd_lease."\n");
		fwrite("a",$_GLOBALS["START"], "xmldbc -X ".$stsp."/dhcps4/leases\n");
	}

	fwrite("a",$_GLOBALS["START"],
		"xmldbc -s ".$stsp."/dhcps4/pool/start ".		$poolstart.	"\n".
		"xmldbc -s ".$stsp."/dhcps4/pool/end ".			$poolend.	"\n".
		"xmldbc -s ".$stsp."/dhcps4/pool/leasetime ".	$lease.		"\n".
		"xmldbc -s ".$stsp."/dhcps4/pool/network ".		$net.		"\n".
		"xmldbc -s ".$stsp."/dhcps4/pool/mask ".		$mask.		"\n"
		);

	/* Create the config file for udhcpd. */
	fwrite("w",$udhcpd_conf,
		//"auto_time 10\n".
		"remaining no\n".	
		"start ".			$poolstart.		"\n".
		"end ".				$poolend.		"\n".
		"interface ".		$ifname.		"\n".
		"lease_file ".		$udhcpd_lease.	"\n".
		"pidfile ".			$udhcpd_pid.	"\n".
		"force_bcast ".     $broadcast.		"\n".
		"opt subnet ".		$subnet.		"\n".		
		);

	/* the default value of max_leases is 254, if we need more, overwrite it. */
	$maxleases = $end - $start + 1;
	if ($maxleases > 254) fwrite("a", $udhcpd_conf, "max_leases ".$maxleases."\n");

	if ($domain!="")
	{
		fwrite("a",$udhcpd_conf, "opt domain ".$domain."\n");
		fwrite("a",$_GLOBALS["START"], "xmldbc -s ".$stsp."/dhcps4/pool/domain ".$domain."\n");
	}
	if ($router!="")
	{
		fwrite("a",$udhcpd_conf, "opt router ".$router."\n");
		fwrite("a",$_GLOBALS["START"], "xmldbc -s ".$stsp."/dhcps4/pool/router ".$router."\n");
	}
	
	/* write NetBIOS -- sam_pan add*/
	$netbios_active       = query("netbios/active");
	$netbios_broadcast    = query("netbios/broadcast");
	$netbios_learnfromwan = query("netbios/learnfromwan");
	$netbios_scope        = query("netbios/scope");	
	$netbios_ntype        = query("netbios/ntype");
	if($netbios_active == "1")
	{				
		fwrite("a",$udhcpd_conf, "opt winstype ".$netbios_ntype."\n");		
		fwrite("a",$udhcpd_conf, "opt scope ".$netbios_scope."\n");		
	}	
	
	/* write DNS */
	$cnt = query("dns/count");
	$i = 0;
	$dns_ready=0;
	while ($i < $cnt)
	{
		$i++;
		$value = query("dns/entry:".$i);
		if ($value != "")
		{
			fwrite("a",$udhcpd_conf, "opt dns ".$value."\n");
			fwrite("a",$_GLOBALS["START"], "xmldbc -a ".$stsp."/dhcps4/pool/dns ".$value."\n");
			$dns_ready=1;
		}
	}
	$dns = INF_getinfinfo($inf, "dns");
	if ($dns=="") $dns = INF_getinfinfo($inf, "dns4");
	if ($dns != "") /* if DNS is enabled, use router ip as dns */
	{
		fwrite("a",$udhcpd_conf, "opt dns ".query($stsp."/inet/ipv4/ipaddr")."\n");
		$dns_ready=1;
	}
	else	// added by snding chen.   if DNS is  disabled,    first,  retrieve  wan's  DNS  from xmldb runtime node,  
	{
		$inf_num = query("/runtime/inf#");
		$inf_curpos = 0;
		while( $inf_curpos < $inf_num )
		{
			$inf_curpos = $inf_curpos + 1;
			$cur_inf = "/runtime/inf:".$inf_curpos; 
			$inf_uid = query( $cur_inf."/uid" );
			if( substr($inf_uid, 0, 3) != "WAN" ) continue;

			$wanMode = query($cur_inf."/inet/addrtype"); 
			//TRACE_debug( $inf_uid." wanMode: ".$wanMode);
			
			if ($wanMode == "ipv4")	// second,  wite the IP of theses DNS to the config file. 
			{
				$wanValid = query($cur_inf."/inet/ipv4/valid");
				if($wanValid == "1")
				{
					$dns_curpos	= "1";
					$dns_num	= query($cur_inf."/inet/ipv4/dns#");

					while( $dns_curpos <= $dns_num )
					{
						fwrite("a",$udhcpd_conf, "opt dns ".query($cur_inf ."/inet/ipv4/dns:".$dns_curpos)."\n");
						$dns_curpos = $dns_curpos + 1;
						$dns_ready=1;
					}
				}
			}
			else if ($wanMode == "ppp4")	// second,  wite the IP of theses DNS to the config file. 
			{
				$connStat = query($cur_inf."/pppd/status");
				$wanValid = query($cur_inf."/inet/ppp4/valid");

				if($connStat =="connected" && $wanValid  != "0")
				{
					$dns_curpos = "1";
					$dns_num = query($cur_inf."/inet/ppp4/dns#");

					while( $dns_curpos <= $dns_num )
					{
						fwrite("a",$udhcpd_conf, "opt dns ".query($cur_inf."/inet/ppp4/dns:".$dns_curpos)."\n");
						$dns_curpos = $dns_curpos + 1;
						$dns_ready=1;
					}
				}
			}
			/* It's not error, just not handled here.
			 * DO NOT say 'error' when it is not an error. */
			/*
			else if ($wanMode == "ipv6" || $wanMode == "ppp6")
				TRACE_debug("# TODO: ".$inf_uid." Type: ".$wanMode); 
			else
				TRACE_debug("ERROR : ".$inf_uid." Type: ".$wanMode);
			 */
		}
	}
	/*lease time*/
	if ($dns_ready!=1) $lease = 60;
    fwrite("a",$udhcpd_conf, "opt lease ".$lease."\n");
	
	/* write WINS sam_pan add*/
	if($netbios_active == "1")
	{
		$cnt = query("wins/count");
		$i = 0;
		while ($i < $cnt)
		{
			$i++;
			$value = query("wins/entry:".$i);
			if ($value != "")
			{
				fwrite("a",$udhcpd_conf, "opt wins ".$value."\n");
				fwrite("a",$_GLOBALS["START"], "xmldbc -a ".$stsp."/dhcps4/pool/wins ".$value."\n");
			}
		}
	}	

	/* dhcp helper */
	fwrite("a",$udhcpd_conf, "dhcp_helper event UPDATELEASES.".$inf."\n");

	/* Static DHCP leases */
	$cnt = query("staticleases/count");
	foreach("staticleases/entry")
	{
		if ($InDeX>$cnt) break;
		if (query("enable")=="1")
		{
			$hostname = get("s", "hostname");
			if($hostname == "") //avoid hostname is empty
			{
				$hostname = "(unknown)";	
			}
			else
			{
				$hostname = $hostname;
			}
			$hostid = query("hostid");
			$macaddr= query("macaddr");
			$ipaddr = ipv4ip($net, $mask, $hostid);
			fwrite("a",$udhcpd_conf, "static ".$hostname." ".$ipaddr." ".$macaddr."\n");
		}
	}

	/* start the application */
	fwrite("a",$_GLOBALS["START"], "event UPDATELEASES.".$inf.
		" add \"@/etc/events/UPDATELEASES.sh ".$inf." ".$udhcpd_lease."\"\n");
	fwrite("a",$_GLOBALS["START"], "udhcpd ".$udhcpd_conf." &\n");
	fwrite("a",$_GLOBALS["START"], "exit 0\n");
}

function dhcps4stop($inf, $phyinf, $dhcpsp)
{
	$udhcpd_pid = "/var/servd/".$inf."-udhcpd.pid";
	fwrite(a, $_GLOBALS["STOP"], "/etc/scripts/killpid.sh ".$udhcpd_pid."\n");
}

function dhcps_error($errno)
{
	fwrite("a", $_GLOBALS["START"], "exit ".$errno."\n");
	fwrite("a", $_GLOBALS["STOP"],  "exit ".$errno."\n");
}

function dhcps4setup($name)
{
	/* Get the interface */
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	if ($stsp=="" || $infp=="")
	{
		SHELL_info($_GLOBALS["START"], "dhcps4setup: (".$name.") no interface.");
		SHELL_info($_GLOBALS["STOP"],  "dhcps4setup: (".$name.") no interface.");
		dhcps_error("9");
		return;
	}
	/* Is this interface active ? */
	$active	= query($infp."/active");
	$dhcps	= query($infp."/dhcps4");
	if ($active!="1" || $dhcps == "")
	{
		SHELL_info($_GLOBALS["START"], "dhcps4setup: (".$name.") not active.");
		SHELL_info($_GLOBALS["STOP"],  "dhcps4setup: (".$name.") not active.");
		dhcps_error("8");
		return;
	}
	/* Check runtime status */
	if (query($stsp."/inet/addrtype")!="ipv4" || query($stsp."/inet/ipv4/valid")!="1")
	{
		SHELL_info($_GLOBALS["START"], "dhcps4setup: (".$name.") invalid IPv4.");
		SHELL_info($_GLOBALS["STOP"],  "dhcps4setup: (".$name.") invalid IPv4.");
		dhcps_error("7");
		return;
	}
	/* Get the physical interface */
	$phyinf = query($infp."/phyinf");
	if ($phyinf == "")
	{
		SHELL_info($_GLOBALS["START"], "dhcps4setup: (".$name.") no phyinf.");
		SHELL_info($_GLOBALS["STOP"],  "dhcps4setup: (".$name.") no phyinf.");
		dhcps_error("9");
		return;
	}
	/* Get the profile */
	$dhcpsp = XNODE_getpathbytarget("/dhcps4", "entry", "uid", $dhcps, 0);
	if ($dhcpsp=="")
	{
		SHELL_info($_GLOBALS["START"], "dhcps4setup: (".$name.") no profile.");
		SHELL_info($_GLOBALS["STOP"],  "dhcps4setup: (".$name.") no profile.");
		dhcps_error("9");
		return;
	}
	
	dhcps4start($name, $phyinf, $dhcpsp);
	dhcps4stop( $name, $phyinf, $dhcpsp);
	dhcps_error("0");
}
?>
