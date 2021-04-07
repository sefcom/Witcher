<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';

/***********************************************************************/
function dhcp_client($mode, $inf, $devnam, $opt, $router, $dns, $dslite)
{
	$hlp = "/var/servd/autodet-".$inf."-dhcp6c.sh";
	$pid = "/var/servd/autodet-".$inf."-dhcp6c.pid";
	$cfg = "/var/servd/autodet-".$inf."-dhcp6c.cfg";

	$iaid = "0";
	$send=$send."\tsend ia-pd 0;\n";
	$idas=$idas."id-assoc pd {\n};\n";

	if($dslite=="1") $dslitemsg = "\trequest aftr-server-domain-name;\n";
	else $dslitemsg = "";

	fwrite(w, $cfg,
		"interface ".$devnam."{\n".
		$send.
		"\trequest domain-name-servers;\n".
		"\trequest domain-name;\n".
		"\trequest ntp-servers;\n".
		$dslitemsg.
		"\tscript \"".$hlp."\";\n".
		"};\n".
		$idas);

	/* generate callback script */
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		"phpsh /etc/services/INET/inet6_dhcpc_helper.php".
			" INF=".$inf.
			" MODE=".$mode.
			" DEVNAM=".$devnam.
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
	echo 'chmod +x '.$hlp.'\n';
	echo 'dhcp6c -c '.$cfg.' -p '.$pid.' -t LL '.$devnam.'\n';
	return 0;
}
/***********************************************************************/

$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$phyinf = query($infp."/phyinf");
$devnam = PHYINF_getifname($phyinf);

if($ACT=="START")
{
	echo 'echo RA detect in START... > /dev/console\n';	

	/* generate callback script */

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
	echo 'rdisc6 -c '.$hlp.' -q -f '.$devnam.' &\n';	// -f: wait RA forever.

	echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$INF.' CHECK"\n';
}
else if($ACT=="CABLESTART")		// for WANV6_6RD_DETECT
{
	echo 'echo RA detect ... > /dev/console\n';
	$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
	$phyinf = query($infp."/phyinf");
	$devnam = PHYINF_getifname($phyinf);
	/* generate callback script */
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
	echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$INF.' CABLECHECK"\n';
}
else if($ACT=="DHCP6START")
{
	echo 'xmldbc -X /runtime/services/wandetect6\n';
	dhcp_client("STATEFUL", $INF, $devnam, "IA-PD" ,"" ,"","0");
	echo 'xmldbc -t autoconf.dis.guest:20:"sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$INF.' CHECK"\n';	
}
else if($ACT=="DHCP6DSSTART")
{
	echo 'xmldbc -X /runtime/services/wandetect6\n';
	dhcp_client("STATEFUL", $INF, $devnam, "IA-PD" ,"" ,"","1");
	echo 'xmldbc -t autoconf.dis.guest:20:"sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$INF.' CHECK"\n';	
}
else if($ACT=="CHECK")
{
	$mode = query("/runtime/services/wandetect6/autoconf/".$ACT);	// write by rdisc6_helper.php.

	echo 'echo mode is '.$mode.' in CHECK > /dev/console\n';
	
	echo 'killall rdisc6\n';
	echo 'killall dhcp6c\n';

	if($mode=="STATEFUL" || $mode=="STATELESS")
	{		
		set("/runtime/services/wandetect6/wantype", $mode);
		set("/runtime/services/wandetect6/desc", "Normal");
	}
	else
	{
		set("/runtime/services/wandetect6/wantype", "unknown");
		set("/runtime/services/wandetect6/desc", "No Response");				
		
		/* if result is "unknown", restart INET.WAN-5 */
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
		
		echo 'xmldbc -t autoconf.dis.guest:10:"sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$INF.' DHCP6START"\n';
	}
	else
	{
		set("/runtime/services/wandetect6/wantype", "unknown");
		set("/runtime/services/wandetect6/desc", "No Response");
		
		/* if result is "unknown", restart INET.WAN-5 */
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
