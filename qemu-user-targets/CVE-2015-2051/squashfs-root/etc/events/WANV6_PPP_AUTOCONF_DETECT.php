<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

/***********************************************************************/
function dhcp_client($mode, $inf, $devnam, $opt, $router, $dns)
{
	$hlp = "/var/servd/autodet-".$inf."-dhcp6c.sh";
	$pid = "/var/servd/autodet-".$inf."-dhcp6c.pid";
	$cfg = "/var/servd/autodet-".$inf."-dhcp6c.cfg";

	$iaid = "0";
	$send=$send."\tsend ia-pd 0;\n";
	$idas=$idas."id-assoc pd {\n};\n";

	if($mode=="PPPDHCP")
	{
		echo 'pppname=`pfile -f /var/run/ppp-DISCOVER.pid -l 2`\n';
		$inf_str = 'interface $pppname';
		$dev_str = ' DEVNAM=$pppname';
	}
	else
	{
		$inf_str = 'interface '.$devnam;
		$dev_str = ' DEVNAM='.$devnam;
	}

	fwrite(w, $cfg,
		//"interface ppp0{\n".  //not important
		$inf_str."{\n".
		$send.
		"\trequest domain-name-servers;\n".
		"\trequest domain-name;\n".
		"\trequest ntp-servers;\n".
		"\tscript \"".$hlp."\";\n".
		"};\n".
		$idas);

	/* generate callback script */
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		"phpsh /etc/services/INET/inet6_dhcpc_helper.php".
			" INF=".$inf.
			" MODE=".$mode.
			//" DEVNAM=ppp0". //not important
			$dev_str.
			" GATEWAY=".$router.
			" DHCPOPT=".$opt.
			' "NAMESERVERS=$new_domain_name_servers"'.
			' "DOMAIN=$new_domain_name"'.
			' "NEW_ADDR=$new_addr"'.
			' "NEW_PD_PREFIX=$new_pd_prefix"'.
			' "NEW_PD_PLEN=$new_pd_plen"'.
			' "NEW_PD_PLTIME=$new_pd_pltime"'.
			' "NEW_PD_VLTIME=$new_pd_vltime"'.
			' "DNS='.$dns.'"'.
			' "NEW_AFTR_NAME=$new_aftr_name"'.
			' "NTPSERVER=$new_ntp_servers"'.
			"\n");

	/* Start DHCP client */
	if($mode=="PPPDHCP")
	{
		echo 'pppname=`pfile -f /var/run/ppp-DISCOVER.pid -l 2`\n';
		echo 'chmod +x '.$hlp.'\n';
		echo 'echo dhcp6c -c '.$cfg.' -p '.$pid.' -t LL -o '.$devnam.' -n '.$inf.' $pppname > /dev/console\n';
		echo 'dhcp6c -c '.$cfg.' -p '.$pid.' -t LL -o '.$devnam.' -n '.$inf.' $pppname\n';
	}
	else
	{
		echo 'chmod +x '.$hlp.'\n';
		echo 'echo dhcp6c -c '.$cfg.' -p '.$pid.' -t LL -n '.$inf.' '.$devnam.' > /dev/console\n';
		echo 'dhcp6c -c '.$cfg.' -p '.$pid.' -t LL -n '.$inf.' '.$devnam.'\n';
	}
	return 0;
}
/***********************************************************************/

echo '#!/bin/sh\n';

if($ACT=="START")
{
	/* Detect Broadband network */
	echo 'echo PPP AUTOCONF detect ... > /dev/console\n';
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$devnam = PHYINF_getifname($phyinf);
	echo 'pppname=`pfile -f /var/run/ppp-DISCOVER.pid -l 2`\n';

	/* send RS */
	$hlp = "/var/servd/".$INF."-test-rdisc6.sh";
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		"echo [$0]: [$IFNAME] [$MFLAG] [$OFLAG] > /dev/console\n".
		"phpsh /etc/services/INET/inet6_rdisc6_helper.php".
			' "IFNAME=$IFNAME"'.
			' "MFLAG=$MFLAG"'.
			' "OFLAG=$OFLAG"'.
			' "PREFIX=$PREFIX"'.
			' "PFXLEN=$PFXLEN"'.
			' "LLADDR=$LLADDR"'.
			' "RDNSS=$RDNSS"'.
			"\n");

	echo 'chmod +x '.$hlp.'\n';
	echo 'rdisc6 -c '.$hlp.' -f -q -e fe80::1 $pppname &\n';	// -f: wait RA forever.

	/* DHCPv6 process */
	dhcp_client("PPPDHCP", $INF, $devnam, "IA-PD" ,"" ,"");

	echo 'xmldbc -t autoconf.dis.guest:20:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' CHECK"\n';
}
if($ACT=="NO_DHCP6_START")	// For Setup 5, added by Jerry_Kao.
{
	echo 'echo RA detect ... > /dev/console\n';
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$devnam = PHYINF_getifname($phyinf);

	// generate callback script

	// Send RS.
	$hlp = "/var/servd/".$INF."-test-rdisc6.sh";
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		"echo [$0]: [$IFNAME] [$MFLAG] [$OFLAG] > /dev/console\n".
		"phpsh /etc/services/INET/inet6_rdisc6_helper.php".
			' "IFNAME=$IFNAME"'.
			' "MFLAG=$MFLAG"'.
			' "OFLAG=$OFLAG"'.
			' "PREFIX=$PREFIX"'.
			' "PFXLEN=$PFXLEN"'.
			' "LLADDR=$LLADDR"'.
			' "RDNSS=$RDNSS"'.
			"\n");

	echo 'chmod +x '.$hlp.'\n';
	//echo 'rdisc6 -c '.$hlp.' -q '.$devnam.' &\n';
	echo 'rdisc6 -c '.$hlp.' -q -f '.$devnam.' &\n';	// -f: wait RA forever.

	echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' NO_DHCP6_CHECK"\n';
}

else if($ACT=="CABLESTART")
{
	echo 'echo RA detect ... > /dev/console\n';
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$devnam = PHYINF_getifname($phyinf);
	echo 'pppname=`pfile -f /var/run/ppp-DISCOVER.pid -l 2`\n';
	// generate callback script.
	$hlp = "/var/servd/".$INF."-test-rdisc6.sh";
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		"echo [$0]: [$IFNAME] [$MFLAG] [$OFLAG] > /dev/console\n".
		"phpsh /etc/services/INET/inet6_rdisc6_helper.php".
			' "IFNAME=$IFNAME"'.
			' "MFLAG=$MFLAG"'.
			' "OFLAG=$OFLAG"'.
			' "PREFIX=$PREFIX"'.
			' "PFXLEN=$PFXLEN"'.
			' "LLADDR=$LLADDR"'.
			' "RDNSS=$RDNSS"'.
			"\n");

	echo 'chmod +x '.$hlp.'\n';
	echo 'rdisc6 -c '.$hlp.' -f -q -e fe80::1 $pppname &\n';
	echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' CABLECHECK"\n';
}
else if($ACT=="DHCP6START")
{
	echo 'xmldbc -X /runtime/services/wandetect6\n';
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$devnam = PHYINF_getifname($phyinf);
	dhcp_client("STATEFUL", $INF, $devnam, "IA-PD" ,"" ,"");
	echo 'xmldbc -t autoconf.dis.guest:20:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' CHECK"\n';
}
else if($ACT=="SEND_DHCP6_START")	  // For Setup 5, added by Jerry_Kao.
{
	echo 'xmldbc -X /runtime/services/wandetect6\n';
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$devnam = PHYINF_getifname($phyinf);
	dhcp_client("STATEFUL", $INF, $devnam, "IA-PD" ,"" ,"");
	
	echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' SEND_DHCP6_CHECK"\n';
}
else if($ACT=="CHECK")	// check mode == (STATEFUL || STATELESS)
{
	$mode = query("/runtime/services/wandetect6/autoconf/".$ACT);	// write by rdisc6.

	echo 'mode is '.$mode.' > /dev/console\n';
	echo 'if [ -f /var/run/ppp-DISCOVER.pid ]; then\n';
	echo '	pid=`pfile -f /var/run/ppp-DISCOVER.pid`\n';
	echo '	[ "$pid" != "0" ] && kill $pid > /dev/console 2>&1\n';
	echo '	rm -rf /var/run/ppp-DISCOVER.pid\n';
	echo 'fi\n';
	echo 'killall rdisc6\n';
	echo 'killall dhcp6c\n';

	if($mode=="STATEFUL" || $mode=="STATELESS")
	{
		set("/runtime/services/wandetect6/wantype", "PPPoE");
		set("/runtime/services/wandetect6/desc", "Normal");
	}
	else
	{
		//set("/runtime/services/wandetect6/wantype", "unknown");//don't record it
		set("/runtime/services/wandetect6/wantype", "");
		set("/runtime/services/wandetect6/desc", "No RA DHCP6");
		
		// if result is "unknown", restart INET.WAN-5.
		/*
		$wizard = query("/runtime/services/wizard6");
		if ($wizard != "1")
		{
			set("/inf:12/active", "1");
			echo 'service INET.WAN-5 restart\n';
		}
		*/	
	}
}
else if($ACT=="NO_DHCP6_CHECK")    // For Setup 5, added by Jerry_Kao.
{
	$mode = query("/runtime/services/wandetect6/autoconf/CHECK");	// write by rdisc6_helper.php.

	echo 'echo mode is '.$mode.' > /dev/console\n';	
	echo 'pppname=`pfile -f /var/run/ppp-DISCOVER.pid -l 2`\n';
	echo '/etc/scripts/killpid.sh /var/run/ppp-DISCOVER.pid\n';
	echo 'killall rdisc6\n';
	//echo 'killall dhcp6c\n';

	if($mode=="STATEFUL" || $mode=="STATELESS")
	{				
		echo 'xmldbc -t autoconf.dis.guest:0:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' SEND_DHCP6_START"\n';
	}
	else
	{				
		// Wait for RA.				
		echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' SEND_DHCP6_START"\n';			
	}
}

else if($ACT=="SEND_DHCP6_CHECK")	  // For Setup 5, added by Jerry_Kao.
{
	$mode = query("/runtime/services/wandetect6/autoconf/CHECK");	// write by rdisc6_helper.php.

	echo 'echo mode is '.$mode.' > /dev/console\n';	
	//echo 'killall rdisc6\n';
	echo 'killall dhcp6c\n';

	if($mode=="STATEFUL" || $mode=="STATELESS")
	{		
		set("/runtime/services/wandetect6/wantype", $mode);
		set("/runtime/services/wandetect6/desc", 	"Normal");
		
		// Do Setup 5, added by Jerry_Kao.
		$wizard = query("/runtime/services/wizard6");
		echo 'echo wizard is '.$wizard.' > /dev/console\n';
		if ($wizard != "1" || $wizard == "")
		{
			echo 'echo Setup 5 - PPPoE and Autoconfiguration - Autoset > /dev/console\n';
			$infp   	= XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
			$inet   	= query($infp."/inet");
			$child   	= query($infp."/child");
			$inetp  	= XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
			$v6actuid 	= query($inetp."/ipv6/detectuid/v6actuid");
			$v6actinfp  = XNODE_getpathbytarget("", "inf", "uid", $v6actuid, 0);		
			$v6actinet  = query($v6actinfp."/inet");	
			$v6actinetp = XNODE_getpathbytarget("/inet", "entry", "uid", $v6actinet, 0);
		
			$pdns	  = query($inetp."/ipv6/dns/entry:1");
			$sdns	  = query($inetp."/ipv6/dns/entry:2");
			$dnscnt	  = query($inetp."/ipv6/dns/count");

			echo 'xmldbc -s '.$v6actinfp.'/infprevious "'.$INF.'"\n';
			echo 'xmldbc -s '.$v6actinfp.'/child "'.$child.'"\n';
			echo 'xmldbc -s '.$v6actinfp.'/infnext "'.$next.'"\n';
			echo 'xmldbc -s '.$v6actinfp.'/defaultroute "1"\n';
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/mode "AUTO"\n';
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:1 "'.$pdns.'"\n';
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:2 "'.$sdns.'"\n';
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/count "'.$dnscnt.'"\n';
			echo 'echo "service INET.'.$v6actuid.' restart" > /var/servd/INET.'.$INF.'_start.sh\n';
			echo 'echo "event DBSAVE" >> /var/servd/INET.'.$INF.'_start.sh\n';
			echo 'echo "service INET.'.$v6actuid.' stop" > /var/servd/INET.'.$INF.'_stop.sh\n';
			echo 'echo "xmldbc -X /runtime/services/wandetect6" >> /var/servd/INET.'.$INF.'_stop.sh\n';
			echo 'echo "rm -f /var/run/'.$INF.'.UP" >> /var/servd/INET.'.$INF.'_stop.sh\n';
			echo 'service INET.'.$v6actuid.' restart\n';
			
			//+++ Jerry Kao, restart pppoe v4 after autodetection.
			foreach("/runtime/inf")
			{
				$addrtype = query("inet/addrtype");
				if ($addrtype == "ppp4")
				{
					$uid = query("uid");
					echo 'service INET.'.$uid.' restart\n';	
				}				
			}				
		}	
		else
		{
			echo 'echo Setup 5 - PPPoE and Autoconfiguration > /dev/console\n';
		}	
	}
	else
	{
		set("/runtime/services/wandetect6/wantype", "unknown");
		set("/runtime/services/wandetect6/desc", "No Response");
		
		//+++ Jerry Kao, restart pppoe v4 after autodetection.
		foreach("/runtime/inf")
		{
			$addrtype = query("inet/addrtype");
			if ($addrtype == "ppp4")
			{
				$uid = query("uid");
				echo 'service INET.'.$uid.' restart\n';	
			}				
		}	
			
		// if result is "unknown", restart INET.WAN-5.
		/*
		$wizard = query("/runtime/services/wizard6");
		if ($wizard != "1")
		{
			set("/inf:12/active", "1");
			echo 'service INET.WAN-5 restart\n'; 
		}
		*/				
	}
}
else if($ACT=="CABLECHECK")
{
	$mode = query("/runtime/services/wandetect6/autoconf/CHECK");
	if($mode!="")
	{
		echo 'killall rdisc6\n';
		echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' DHCP6START"\n';
	}
	else
	{
		set("/runtime/services/wandetect6/wantype", "unknown");
		set("/runtime/services/wandetect6/desc", "No Response");
		
		// if result is "unknown", restart INET.WAN-5.
		/*	
		$wizard = query("/runtime/services/wizard6");
		if ($wizard != "1")
		{
			set("/inf:12/active", "1");
			echo 'service INET.WAN-5 restart\n';
		}
		*/
	}
}


echo 'exit 0\n';
?>
