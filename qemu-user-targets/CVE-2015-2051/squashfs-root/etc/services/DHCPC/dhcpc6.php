<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
function startcmd($cmd)	{fwrite("a", $_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite("a", $_GLOBALS["STOP"], $cmd."\n");}
function dhcperr($errno)
{
	startcmd('exit '.$errno);
	stopcmd( 'exit '.$errno);
	return $errno;
}

function dhcpc6setup($inf)
{
	$hlp = "/var/servd/".$inf."-dhcp6c.sh";
	$pid = "/var/servd/".$inf."-dhcp6c.pid";
	$cfg = "/var/servd/".$inf."-dhcp6c.cfg";

	/* DHCP over PPP session ? */
	//$previnf = XNODE_get_var($inf."_PREVINF");
	//XNODE_del_var($inf."_PREVINF");
	$infp   = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	$previnf = query($infp."/infprevious");
	$phyinf = query($infp."/phyinf");
        $inet   = query($infp."/inet");
	$inetp  = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
	$devnam = PHYINF_getifname($phyinf);

	/* dslite ? */
	$nextinf = query($infp."/infnext");

	//if ($mode=="PPPDHCP" && $_GLOBALS["PREVINF"]!="")
	//msg("mode is ".$mode.", previnf is ".$previnf);
	startcmd('# dhcpc6setup('.$inf.','.$inetp.')');
	startcmd("previnf is ".$previnf.", nextinf is ".$nextinf);

	//if ($mode=="PPPDHCP" && $previnf!="")
	//{
	//	$pppdev = PHYINF_getruntimeifname($previnf);
	//	if ($pppdev=="") return error("no PPP device.");
	//}

	/* Gererate DHCP-IAID from 32-bit of mac address*/
	$mac = PHYINF_getphymac($inf);
	$mac1 = cut($mac, 3, ":"); $mac2 = cut($mac, 0, ":"); $mac3 = cut($mac, 1, ":"); $mac4 = cut($mac, 2, ":");
	$iaidstr = $mac1.$mac2.$mac3.$mac4;
	$iaid = strtoul($iaidstr, 16);	
		
	/* Generate configuration file. */
	$send="\tinformation-only;\n";
	$idas="";

	//if($mode=="PPPDHCP") $dname = $pppdev;
	//else $dname = $devnam;
	$dname = $devnam;

	$nextinfp = XNODE_getpathbytarget("", "inf", "uid", $nextinf, 0);
	$nextinet = query($nextinfp."/inet");
	$nextinetp = XNODE_getpathbytarget("inet", "entry", "uid", $nextinet, 0);
	$nextmode = query($nextinetp."/ipv4/ipv4in6/mode");
		
	if($nextinf!="" && $nextmode=="dslite")
	{ 
		$rqstmsg = "\trequest aftr-server-domain-name;\n";
	}
	else	$rqstmsg = "";

	fwrite(w, $cfg,
		"interface ".$dname." {\n".
		$send.
		$rqstmsg.
		"\tscript \"".$hlp."\";\n".
		"};\n".
		$idas);

	/* generate callback script */
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		//"echo [$0]: [$new_addr] [$new_pd_prefix] [$new_pd_plen] > /dev/console\n".
		"phpsh /etc/services/INET/inet6_dhcpc_helper.php".
			" INF=".$inf.
			" MODE=INFOONLY".
			//" DEVNAM=".$devnam.
			" DEVNAM=".$dname.
			" GATEWAY="."".
			" DHCPOPT="."".
			' "NAMESERVERS=$new_domain_name_servers"'.
			' "NEW_ADDR=$new_addr"'.
			' "NEW_PD_PREFIX=$new_pd_prefix"'.
			' "NEW_PD_PLEN=$new_pd_plen"'.
			' "DNS='."".'"'.
			' "NEW_AFTR_NAME=$new_aftr_name"'.
			' "NTPSERVER=$new_ntp_servers"'.
			"\n");

	/* Start DHCP client */
	startcmd("chmod +x ".$hlp);
	//if ($pppdev=="")
		 startcmd("dhcp6c -c ".$cfg." -p ".$pid." -t LL ".$devnam);
	//else startcmd("dhcp6c -c ".$cfg." -p ".$pid." -t LL -o ".$devnam." ".$pppdev);

	stopcmd("/etc/scripts/killpid.sh /var/servd/".$inf."-dhcp6c.pid");
}
?>

