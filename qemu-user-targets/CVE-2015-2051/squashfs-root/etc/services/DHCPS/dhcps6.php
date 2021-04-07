<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
require "/htdocs/phplib/trace.php";


function startcmd($cmd)	{fwrite("a", $_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite("a", $_GLOBALS["STOP"], $cmd."\n");}
/***********************************************/
function error($err, $msg)
{
	startcmd('# '.$msg);
	startcmd('exit '.$err);
	stopcmd( '# '.$msg);
	stopcmd( 'exit '.$err);
	return $err;
}
/***********************************************/

function commands($inf, $stsp, $phyinf, $dhcpsp)
{
	startcmd('# dhcps6: inf='.$inf.', stsp='.$stsp.', phyinf='.$phyinf.', dhcps='.$dhcpsp);
	
	/* get the network info. */
	$ifname = PHYINF_getifname($phyinf);

	$dhcps6_mode    = query($dhcpsp.'/mode');
	$dhcps6_network = query($dhcpsp.'/network');
	$dhcps6_prefix  = query($dhcpsp.'/prefix');
	$dhcps6_start   = query($dhcpsp.'/start');
	$dhcps6_count   = query($dhcpsp.'/count');
	$dhcps6_domain  = query($dhcpsp.'/domain');

	/* get pd info */
	$pd_enable    = query($dhcpsp.'/pd/enable');
	$pd_mode      = query($dhcpsp.'/pd/mode');
	$pd_slalen    = query($dhcpsp.'/pd/slalen');
	$pd_network   = query($dhcpsp.'/pd/network');
	$pd_prefix    = query($dhcpsp.'/pd/prefix');
	$pd_start     = query($dhcpsp.'/pd/start');
	$pd_count     = query($dhcpsp.'/pd/count');
	$pd_plft      = query($dhcpsp.'/pd/preferlft');
	$pd_vlft      = query($dhcpsp.'/pd/validlft');

	$inet_network   = query($stsp.'/inet/ipv6/ipaddr');
	$inet_prefix    = query($stsp.'/inet/ipv6/prefix');

	if ($dhcps6_network == '')
	{
		$network = ipv6networkid($inet_network, $inet_prefix);
		$prefix  = $inet_prefix;
		$dhcps6_domain  = XNODE_get_var($inf."_DOMAIN");
		XNODE_del_var($inf."_DOMAIN");
	}
	else
	{
		$network = $dhcps6_network;
		$prefix  = $dhcps6_prefix;
	}

	$domain = $dhcps6_domain;
	$mode   = $dhcps6_mode;
	$start  = ipv6ip($network, $prefix, $dhcps6_start, 0, 0);
	$stop	= ipv6addradd($start, $dhcps6_count);

	startcmd('# phyifname='.$ifname.', network='.$network.'/'.$prefix.', domain='.$domain);

	/* Update status. */
	set($stsp.'/dhcps6/mode',		$mode);
	set($stsp.'/dhcps6/network',		$network);
	set($stsp.'/dhcps6/prefix',		$prefix);
	set($stsp.'/dhcps6/start',		$start);
	set($stsp.'/dhcps6/count',		$dhcps6_count);
	set($stsp.'/dhcps6/domain',		$domain);
	setattr($stsp.'/dfltrtmtu', "get", "ip -6 route show default | scut -p mtu");

	/* check if ula, if yes we don't start dhcp6s */
	if(ipv6networkid($network,8)=="fd00::")	$isula = 1;
	else					$isula = 0;

	/* Update pd status. */
	$rpdnetwork = query($stsp.'/dhcps6/pd/network');
	$rpdplen = query($stsp.'/dhcps6/pd/prefix');
	
	if($pd_mode=="0") /* generic */
	{
		/* all information can acquire from db */
	}
	else if($pd_mode=="1") /* dlink */
	{
		if($rpdplen<="56")				{$pd_slalen="4";$pd_start="1";$pd_count="15";}
		else if($rpdplen>="57" && $rpdplen<="59")	{$pd_slalen="3";$pd_start="1";$pd_count="7";}
		else if($rpdplen>="60" && $rpdplen<="61")	{$pd_slalen="2";$pd_start="1";$pd_count="3";}
		else if($rpdplen=="62")				{$pd_slalen="1";$pd_start="1";$pd_count="1";}
		else 						{$pd_enable="0";}
	}
	
	set($stsp.'/dhcps6/pd/enable',		$pd_enable);
	set($stsp.'/dhcps6/pd/mode',		$pd_mode);	
	$pdmsg = " ";
	
	if($pd_enable=="1" && $rpdnetwork!="" && $rpdplen!="")
	{
		set($stsp.'/dhcps6/pd/slalen',		$pd_slalen);
		if($rpdnetwork!="")
		{
			$pd_network = $rpdnetwork;
			$pd_prefix  = $rpdplen; 
		}
		set($stsp.'/dhcps6/pd/network',	$pd_network);
		set($stsp.'/dhcps6/pd/prefix',	$pd_prefix);
		set($stsp.'/dhcps6/pd/start',	$pd_start);
		set($stsp.'/dhcps6/pd/count',	$pd_count);
		
		$rpd_plft = query($stsp."/dhcps6/pd/preferlft");
		$rpd_vlft = query($stsp."/dhcps6/pd/validlft");
		if($pd_plft=="") 
		{
			if($rpd_plft!="")
			{
				$pd_plft = $rpd_plft;
				set($stsp.'/inet/ipv6/preferlft',	$pd_plft);
			}
			else
			{
				$pd_plft="3600"; 
				set($stsp.'/dhcps6/pd/preferlft',	$pd_plft);
			}
		}
		if($pd_vlft=="")
		{ 
			if($rpd_vlft!="")
			{
				$pd_vlft = $rpd_vlft;
				set($stsp.'/inet/ipv6/validlft',	$pd_vlft);
			}
			else
			{
				$pd_vlft="7200"; 
				set($stsp.'/dhcps6/pd/validlft',	$pd_vlft);
			}
		}

		/* generate pd config */
		$pdmsg= '\npd_conf default\n'.
			'{\n'.
			'	prefix '.$pd_network.'/'.$pd_prefix.' '.$pd_plft.' '.$pd_vlft.';\n'.
			'	segment_bits '.$pd_slalen.';\n'.
			'	pd_range '.$pd_start.' to '.$pd_count.';\n'.
			'};\n';
	}

	/* generate static pd config */
	foreach ("/runtime/inf")
	{
		//check if WAN type is 6to4
		$wantype = query("inet/addrypte");	
		if($wantype=="ipv6" || $wantype=="ppp6")
		{
			if(query("inet/".$wantype."/valid")=="1")
			{
				$wanmode = query("inet/".$wantype."/mode");
				if($wanmode=="6TO4") { break;}
			}
		}
	}
	
	$spdmsg = '\n';
	if($wanmode != "6TO4")
	{
		$cnt = query("/route6/static/count");
		foreach("/route6/static/entry")
		{
			if($InDeX > $cnt) break;
			$rinf = query("inf");
			if($rinf!="PD") continue;
			$rname = query("description");
			$rnetwork= query("network");
			$rprefix = query("prefix");

			/* generate static pd config */
			if($pd_plft=="") {$pd_plft="3600"; }
			if($pd_vlft=="") {$pd_vlft="7200"; }
			$spdmsg= $spdmsg.
				'\nhost '.$rname.'\n'.
				'{\n'.
				'	prefix '.$rnetwork.'/'.$rprefix.' '.$pd_plft.' '.$pd_vlft.';\n'.
				'};\n';
		}
	}

	$cnt = query($stsp."/dhcps6/dns/entry#");
	while ($cnt > 0) {del($stsp."/dhcps6/dns/entry"); $cnt--;}

	/* Get DNS from user config */
	$dns = '';
	$cnt = query($dhcpsp.'/dns/count');
	foreach ($dhcpsp."/dns/entry")
	{
		if ($InDeX > $cnt) break;
		add($stsp."/dhcps6/dns/entry", $VaLuE);
		if ($dns=="")	$dns = $VaLuE;
		else		$dns = $dns.' '.$VaLuE;
	}

	$infp = XNODE_getpathbytarget("","inf","uid",$inf,0);
	/*
	$dns6 = query($infp."/dns6");
	if ($dns == '' && $dns6 != "")
	{
		$dns = $inet_network;
		add($stsp.'/dhcps6/dns/entry',	$dns);
	}
	*/
	$needrelay = query($infp."/dnsrelay");
	if($needrelay == "") $needrelay = 1;
	if ($dns == '' && $needrelay == "1")
	{
		$dns = $inet_network;
		add($stsp.'/dhcps6/dns/entry',	$dns);
	}

	if($needrelay == "0") 
	{
		//$cntmax = 3;
		foreach("/runtime/inf")
		{
			$addrtype = query("inet/addrtype");
			if($addrtype=="ipv6" || $addrtype=="ppp6")
			{
				if(query("inet/".$addrtype."/valid")=="1")
				{
					foreach("inet/".$addrtype."/dns")
					{
						//if($InDeX>$cntmax) break;
						add($stsp."/dhcps6/dns/entry", $VaLuE);
						if ($dns=="")	$dns = $VaLuE;
						else		$dns = $dns.' '.$VaLuE;
					}
				}
			}
			
			if($addrtype=="ppp4")
			{
				if(query("inet/ppp4/valid")=="1" || query("inet/ppp6/valid")=="1" )
				{
					foreach("inet/ppp6/dns")
					{
						//if($InDeX>$cntmax) break;
						add($stsp."/dhcps6/dns/entry", $VaLuE);
						if ($dns=="")	$dns = $VaLuE;
						else		$dns = $dns.' '.$VaLuE;
					}
				}
			}
		}	
		if ($dns == '')//needed?
		{
			$dns = $inet_network;
			add($stsp.'/dhcps6/dns/entry',	$dns);
		}
	}

	$mtu = query($stsp."/dfltrtmtu");
	if($mtu!="")
	{
		$mtucmd="	AdvLinkMTU ".$mtu.";\n";
	}
	else $mtucmd=" ";
	
	$routerlft = query($stsp.'/inet/ipv6/routerlft');
	$rdnss = query("/device/rdnss");
	
	if (get("",$stsp."/inet/ipv6/mode") == "STATIC")
	{
		$pd_ptlft = get("",$stsp."/inet/ipv6/preferlft");
		$pd_vtlft = get("",$stsp."/inet/ipv6/validlft");
	}
	else
	{
		$pd_ptlft = get("","/runtime/ipv6/pre_pdplft");
		$pd_vtlft = get("","/runtime/ipv6/pre_pdvlft");
	}
	if($pd_ptlft == "" || $pd_vtlft == "") 
	{ 
		if ($routerlft == "")
		{
			$pd_ptlft="3600"; 
			$pd_vtlft ="7200";
		}
		else
		{
			$pd_ptlft = $routerlft / 2 ; 
			$pd_vtlft = $routerlft;
		}
	}
	$ralft = $routerlft/3;
	
	if ($routerlft != "")
	{ $default_router_left_msg = "	AdvDefaultLifetime ".$routerlft."; \n"; }
	else 
	{ $default_router_left_msg = ""; }

	/* parse router Information option */	
	$wanpdvlft = query($stsp.'/dhcps6/pd/validlft');
	startcmd('# dhcps6: PDVLFT='.$wanpdvlft.", network=".$rpdnetwork."/".$rpdplen);
	if($wanpdvlft!="" && $rpdnetwork!="" && $rpdplen!="")
	{		
		$routemsg='\troute '.$rpdnetwork.'/'.$rpdplen.'\n'.
			'\t{\n'.
			'	\tAdvRoutePreference high;\n'.
			'	\tAdvRouteLifetime '.$wanpdvlft.';\n'.
			'\t};\n';
	}
	else
	{
		if ($routerlft != "")
		{
			$rpdnetwork = get("",$stsp."/dhcps6/network");
			$rpdplen = get("",$stsp."/dhcps6/prefix");
			$routemsg='\troute '.$rpdnetwork.'/'.$rpdplen.'\n'.
				'\t{\n'.
				'	\tAdvRoutePreference high;\n'.
				'	\tAdvRouteLifetime '.$routerlft.';\n'.
				'\t};\n';
		}
		else
		{
			$routemsg='\n';
		}
	}
 
	/* add ntp option */
	$ntpsvr = query("/runtime/device/ntp6/server");
	if($ntpsvr!="")
	{
		$ntpsrvmsg = 'option ntp-servers '.$ntpsvr.';\n';
	}
	else
	{
		$ntpsrvmsg = '\n';
	}
	
	//+++ Jerry Kao, Add RDNSS in RA (Stateful and Stateless) for UNH-IOL (Ver. 1.0.0b14).
	//               radvd only accept 3 dns server.			
	$i = 0;
	$dnsr = '';
	while ($i < 3)
	{
		$val = scut($dns, $i, "");
		if ($dnsr=="")	$dnsr = $val;
		else		$dnsr = $dnsr.' '.$val;
		$i++;
	}	 
	
	if($routerlft !="") {$rdnsslt=2*$ralft;}
	else {$rdnsslt=1800;}
	 
	if ($dnsr != "")
	{
		$rdnssmsg='\tRDNSS '.$dnsr.'\n'.
			'\t{\n'.
			'	\tAdvRDNSSPreference 8;\n'.
			'#	\tAdvRDNSSOpen off;\n'.
			'	\tAdvRDNSSLifetime '.$rdnsslt.';\n'.
			'\t};\n';	
	}
	else
	{
		$rdnssmsg = "";
	}
	if($domain!="")
	{
		$dnsslmsg='\tDNSSL '.$domain.'\n'.
			'\t{\n'.
			'\t};\n';
	}
	else
	{		
		$dnsslmsg='\n';				
	}	
	//--- Jerry Kao.

	/* Generate callback script */
	$hlp = "/var/servd/dhcps6.".$inf.".sh";
	fwrite(w, $hlp,
		"#!/bin/sh\n".
		"echo [$0]: [$DHCP6S_INF] [$DHCP6S_ACTION] [$DHCP6S_HOST] [$DHCP6S_MAC] [$DHCP6S_DST] [$DHCP6S_GATEWAY] [$DHCP6S_DEV]> /dev/console\n".
		"phpsh /etc/scripts/dhcp6s_helper.php".
			' "INF=$DHCP6S_INF"'.
			' "ACTION=$DHCP6S_ACTION"'.
			' "HOST=$DHCP6S_HOST"'.
			' "MAC=$DHCP6S_MAC"'.
			' "DST=$DHCP6S_DST"'.
			' "GATEWAY=$DHCP6S_GATEWAY"'.
			' "DEVNAM=$DHCP6S_DEV"'.
		"\n");
	startcmd('chmod +x '.$hlp);

	
	/* Generate radvd config, then run radvd and callback script */
	if ($mode == 'STATELESS')
	{
		// M flag = 0: Get Prefix from RA.

		startcmd('# stateless mode!!!');

		$racfg = '/var/run/radvd.'.$inf.'.conf';
		$rapid = '/var/run/radvd.'.$inf.'.pid';

		/* add rdnss info */
		if($rdnss=="1")		
		{
			// Set O flag = 0: Get DNS by RA.
			$oflagmsg ='	AdvOtherConfigFlag off;\n';										
		}
		else
		{
			$oflagmsg ='	AdvOtherConfigFlag on;\n';
			
			//+++ Jerry Kao, remove below for Add RDNSS in RA (Stateful and Stateless)
			//               in order to meet UNH-IOL (Ver. 1.0.0b14).
			//$rdnssmsg='\n';
			//$dnsslmsg='\n';
		}				
								
	//+++ UNH-IOL test Rev1.0.0b5 4.1
	
		/* Only ULA should set RA lifetime as 0 */
//		$onlyula = 1;
//		$ula_exist = 0;
		//startcmd('# onlyula='.$onlyula.', isula ='.$isula);
		
		if($isula==1)
		{
			/* check if other interface also runs radvd */
			/* if yes, set router lifetime of ula to zero */
			foreach ("/runtime/inf")
			{
				//check if LAN type is ipv6
				$lantype = query("inet/addrtype");	
				if($lantype=="ipv6")
				{
					if(query("inet/".$lantype."/valid")=="1")
					{
						$temp_uid = query("uid");
						$temp_rapid = "/var/run/radvd.".$temp_uid.".pid";
						if(isfile($temp_rapid)==1) 
						{
							$onlyula = 0;
							break;
						}
					}
				}
			}
		}
		else
		{
			/* not ula but should check if ula exists */
			$ula_uid = query("/runtime/ipv6/ula/uid");
			if($ula_uid!="")	$ula_exist = 1;
			else			$ula_exist = 0;
		}
	
		$gz_uid = query("/runtime/ipv6/gzuid");
		if($gz_uid == $inf)	$isgz = 1;
		else			$isgz = 0;	
		startcmd('# isula='.$isula.', ula_exist='.$ula_exist.', onlyula='.$onlyula.', isgz='.$isgz.',global uid='.$temp_uid);

		if($isula==1 && $onlyula==1)
		{
			// Only have ula address (without Global address, $isula == 1).
			// radvd: Set both M and O flags = 0, and
			//        DefaultRouterTime = 0 (LAN PCs recv. prefix, but not set DEV as Default gateway).
			
			//startcmd(' # Only have ula address');
			
			//+++ Jerry Kao, Set O flag = 0 in Only ULA.
			$oflagmsg ='	AdvOtherConfigFlag off;\n';
			
			fwrite(w, $racfg,
				'# radvd config for '.$inf.'\n'.
				'interface '.$ifname.'\n'.
				'{\n'.
				'	AdvSendAdvert on;\n'.
				'	AdvManagedFlag off;\n'.
				'	AdvDefaultLifetime 0;\n'.			
				$oflagmsg.$mtucmd.
				$default_router_left_msg.
				'	MinRtrAdvInterval 3;\n'.
				'	MaxRtrAdvInterval 10;\n'.
				'	prefix '.$network.'/'.$prefix.'\n'.
				'	{\n'.
				'		AdvOnLink on;\n'.
				'		AdvAutonomous on;\n'.
				//'	};\n'.$rdnssmsg.$routemsg.
				'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
				'};\n'.
			);
		}
		else if($isula==1 && $onlyula==0)
		{
			// 1) Have Global address ($onlyula == 0), then 2) set ula address.			
			
			//+++ Jerry Kao, Added RDNSS and DNSSL to meet UNH-IOL (ver. 1.0.0b14).
/*			$dnsslmsg='\tDNSSL '.$dns.'\n'.
					'\t{\n'.
					'\t};\n';
*/			
			//set ula address but global address already existed
			$rginfp = XNODE_getpathbytarget("/runtime","inf","uid",$temp_uid,0);
			$g_network = query($rginfp."/dhcps6/network");
			$g_prefix = query($rginfp."/dhcps6/prefix");
			$g_racfg = '/var/run/radvd.'.$temp_uid.'.conf';
			$g_rapid = '/var/run/radvd.'.$temp_uid.'.pid';
			$combine_exist = query("/runtime/ipv6/ula_global_combine_ra");
			
			if($combine_exist=="")//check guest zone
			{
				startcmd('/etc/scripts/killpid.sh '.'/var/run/radvd.'.$temp_uid.'.pid');
				startcmd("xmldbc -s /runtime/ipv6/ula_global_combine_ra ".$inf);
				stopcmd('radvd -C '.$g_racfg.' -p '.$g_rapid);
				stopcmd("xmldbc -X /runtime/ipv6/ula_global_combine_ra");
			}
			
			if($routerlft != "")
			{
				fwrite(w, $racfg,
					'# radvd config for '.$inf.'\n'.
					'interface '.$ifname.'\n'.
					'{\n'.
					'	AdvSendAdvert on;\n'.
					'	AdvManagedFlag off;\n'.					
					$oflagmsg.$mtucmd.
					$default_router_left_msg.
					'	MaxRtrAdvInterval '.$ralft.';\n'.
					'	prefix '.$g_network.'/'.$g_prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					'	};\n'.
					'	prefix '.$network.'/'.$prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					//'	};\n'.$rdnssmsg.$routemsg.
					'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
					'};\n'.
				);
			}
			else
			{
				fwrite(w, $racfg,
					'# radvd config for '.$inf.'\n'.
					'interface '.$ifname.'\n'.
					'{\n'.
					'	AdvSendAdvert on;\n'.
					'	AdvManagedFlag off;\n'.					
					$oflagmsg.$mtucmd.
					$default_router_left_msg.
					'	MinRtrAdvInterval 3;\n'.
					'	MaxRtrAdvInterval 10;\n'.
					'	prefix '.$g_network.'/'.$g_prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					'	};\n'.
					'	prefix '.$network.'/'.$prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					//'	};\n'.$rdnssmsg.$routemsg.
					'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
					'};\n'.
				);
			}
			
		}
		else if($isula==0 && $ula_exist==1)
		{
			// 1) Have ULA address ($ula_exist == 1), then 2) set Global address ($isula == 0).

			//+++ Jerry Kao, Added RDNSS and DNSSL to meet UNH-IOL (ver. 1.0.0b14).
		/*
			$dnsslmsg='\tDNSSL '.$dns.'\n'.
					'\t{\n'.
					'\t};\n';
		*/			
			//set global address but ula address already existed
			$ula_network = query("/runtime/ipv6/ula/network");
			$ula_prefix = query("/runtime/ipv6/ula/plen");
			$ula_racfg = '/var/run/radvd.'.$ula_uid.'.conf';
			$ula_rapid = '/var/run/radvd.'.$ula_uid.'.pid';
			$combine_exist = query("/runtime/ipv6/ula_global_combine_ra");
			
			if($isgz==0)
			{
				//host zone
				if($combine_exist=="")
				{
					startcmd('/etc/scripts/killpid.sh '.'/var/run/radvd.'.$ula_uid.'.pid');
					startcmd("xmldbc -s /runtime/ipv6/ula_global_combine_ra ".$inf);
					stopcmd('radvd -C '.$ula_racfg.' -p '.$ula_rapid);
					stopcmd("xmldbc -X /runtime/ipv6/ula_global_combine_ra");
					$ula_prefix_msg = '	prefix '.$ula_network.'/'.$ula_prefix.'\n'.
							'	{\n'.
							'		AdvOnLink on;\n'.
							/*+++ HuanYao Kang, UNH-IOL requires the A-flag in ULA prefix enabled. */
							'		AdvAutonomous on;\n'.
							'	};\n';
				}
			}
			else	$ula_prefix_msg = '\n';
			
			if($routerlft != "")
			{
				fwrite(w, $racfg,
					'# radvd config for '.$inf.'\n'.
					'interface '.$ifname.'\n'.
					'{\n'.
					'	AdvSendAdvert on;\n'.
					'	AdvManagedFlag off;\n'.					
					$oflagmsg.$mtucmd.
					$default_router_left_msg.
					'	MaxRtrAdvInterval '.$ralft.';\n'.
					//'	prefix '.$ula_network.'/'.$ula_prefix.'\n'.
					//'	{\n'.
					//'		AdvOnLink on;\n'.
					//'		AdvAutonomous on;\n'.
					//'	};\n'.
					$ula_prefix_msg.
					'	prefix '.$network.'/'.$prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					//'	};\n'.$rdnssmsg.$routemsg.
					'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
					'};\n'.
				);
			}
			else
			{
				fwrite(w, $racfg,
					'# radvd config for '.$inf.'\n'.
					'interface '.$ifname.'\n'.
					'{\n'.
					'	AdvSendAdvert on;\n'.
					'	AdvManagedFlag off;\n'.					
					$oflagmsg.$mtucmd.
					$default_router_left_msg.
					'	MinRtrAdvInterval 3;\n'.
					'	MaxRtrAdvInterval 10;\n'.
					//'	prefix '.$ula_network.'/'.$ula_prefix.'\n'.
					//'	{\n'.
					//'		AdvOnLink on;\n'.
					//'		AdvAutonomous on;\n'.
					//'	};\n'.
					$ula_prefix_msg.
					'	prefix '.$network.'/'.$prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					//'	};\n'.$rdnssmsg.$routemsg.
					'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
					'};\n'.
				);
			}
		}
		else	//$isula =0 && $ula_exist=0
		{
			// Only have Global address.	
			if($pd_ptlft == "" || $pd_vtlft == "")
			{
				fwrite(w, $racfg,
					'# radvd config for '.$inf.'\n'.
					'interface '.$ifname.'\n'.
					'{\n'.
					'	AdvSendAdvert on;\n'.
					'	AdvManagedFlag off;\n'.					
					$oflagmsg.$mtucmd.
					$default_router_left_msg.
					'	MaxRtrAdvInterval '.$ralft.';\n'.
					'	prefix '.$network.'/'.$prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					//'	};\n'.$rdnssmsg.$routemsg.
					'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
					'};\n'.
				);
			}
			else
			{
				fwrite(w, $racfg,
					'# radvd config for '.$inf.'\n'.
					'interface '.$ifname.'\n'.
					'{\n'.
					'	AdvSendAdvert on;\n'.
					'	AdvManagedFlag off;\n'.					
					$oflagmsg.$mtucmd.
					$default_router_left_msg.
					'	MinRtrAdvInterval 3;\n'.
					'	MaxRtrAdvInterval 10;\n'.
					'	prefix '.$network.'/'.$prefix.'\n'.
					'	{\n'.
					'		AdvOnLink on;\n'.
					'		AdvAutonomous on;\n'.
					'		AdvPreferredLifetime '.$pd_ptlft.';\n'.
					'		AdvValidLifetime '.$pd_vtlft.';\n'.
					//'	};\n'.$rdnssmsg.$routemsg.
					'	};\n'.$routemsg.$rdnssmsg.$dnsslmsg.
					'};\n'.
				);
			}
		}
	//--- UNH-IOL.

		if($network!="")
		{
			startcmd('radvd -C '.$racfg.' -p '.$rapid);
		}

		/*dns information via dhcpv6*/
		if($pd_enable=="1")
		{
			$mainmsg='interface '.$ifname.'\n'.
				'{\n'.
				'	allow rapid-commit;\n'.
				'	address-pool dummy 3600 7200;\n'.
				'};\n'.
				'pool dummy\n'.
				'{\n'.
				'	range fe80::1 to fe80::1;\n'.
				'};\n';
		}
		else
		{
			$mainmsg='interface '.$ifname.'\n'.
				'{\n'.
				'	allow rapid-commit;\n'.
				'};\n';
		}

		if($rdnss=="" || $rdnss=="0")
		{       
			/* SLAAC + Stateless DHCP (O flag = 1) */
			$dhcpcfg = '/var/run/dhcps6.'.$inf.'.conf';
			$dhcppid = '/var/run/dhcps6.'.$inf.'.pid';
			fwrite(w,$dhcpcfg,
				'option domain-name-servers '.$dns.';\n'.
				'option domain-name "'.$domain.'";\n'.
				$ntpsrvmsg.
				$mainmsg.
				$pdmsg.
				$spdmsg.
				'\n'
			);
			//startcmd('dhcp6s -c '.$dhcpcfg.' -P '.$dhcppid.' '.$ifname);
		}
		else
		{
			/* SLAAC + RDNSS (O flag = 0) */
			$dhcpcfg = '/var/run/dhcps6.'.$inf.'.conf';
			$dhcppid = '/var/run/dhcps6.'.$inf.'.pid';
			fwrite(w,$dhcpcfg,
				$mainmsg.
				$pdmsg.
				$spdmsg.
				'\n'
			);
			//startcmd('dhcp6s -c '.$dhcpcfg.' -P '.$dhcppid.' '.$ifname);
		}
		if($network!="" && $isula!=1)
		{
			startcmd('dhcp6s -c '.$dhcpcfg.' -P '.$dhcppid.' -s '.$hlp.' -u '.$inf.' '.$ifname);
		}
	}
	else // if ($mode == 'STATELESS')
	{
		/* STATEFUL */
		
		// M flag = 1: Get Prefix and DNS from DHCPv6 server.
		
		startcmd('# stateful mode!!!');

		/* not ula but should check if ula exists */
		$ula_uid = query("/runtime/ipv6/ula/uid");
		if($ula_uid!="")	$ula_exist = 1;
		else			$ula_exist = 0;

		$ula_prefix_msg ='';
		if($ula_exist=="1")
		{
			//set global address but ula address already existed
			//1. Delete ula radvd
			$ula_network = query("/runtime/ipv6/ula/network");
			$ula_prefix = query("/runtime/ipv6/ula/plen");
			$ula_racfg = '/var/run/radvd.'.$ula_uid.'.conf';
			$ula_rapid = '/var/run/radvd.'.$ula_uid.'.pid';
			$combine_exist = query("/runtime/ipv6/ula_global_combine_ra");
			if($combine_exist=="")//check guest zone
			{
				startcmd('/etc/scripts/killpid.sh '.'/var/run/radvd.'.$ula_uid.'.pid');
				startcmd("xmldbc -s /runtime/ipv6/ula_global_combine_ra ".$inf);
				stopcmd('radvd -C '.$ula_racfg.' -p '.$ula_rapid);
				stopcmd("xmldbc -X /runtime/ipv6/ula_global_combine_ra");
			}
			$ula_prefix_msg = '	prefix '.$ula_network.'/'.$ula_prefix.'\n'.
						'	{\n'.
						'		AdvOnLink on;\n'.
						'		AdvAutonomous off;\n'.
						'	};\n';
		}
													
		$racfg = '/var/run/radvd.'.$inf.'.conf';
		$rapid = '/var/run/radvd.'.$inf.'.pid';

		fwrite(w,$racfg,
			'# radvd config for '.$inf.'\n'.
			'interface '.$ifname.'\n'.
			'{\n'.
			'	AdvSendAdvert on;\n'.
			'	AdvManagedFlag on;\n'.
			'	AdvOtherConfigFlag on;\n'.
			$mtucmd.
			$default_router_left_msg.
			'	MinRtrAdvInterval 3;\n'.
			'	MaxRtrAdvInterval 10;\n'.
			'	prefix '.$network.'/'.$prefix.'\n'.
			'	{\n'.
			'		AdvOnLink on;\n'.
			'		AdvAutonomous off;\n'.
			'		AdvPreferredLifetime '.$pd_ptlft.';\n'.
			'		AdvValidLifetime '.$pd_vtlft.';\n'.
			//+++ Jerry Kao, Added RDNSS and DNSSL to meet UNH-IOL (ver. 1.0.0b14).
			'	};\n'.$ula_prefix_msg.$routemsg.$rdnssmsg.$dnsslmsg.	
			//'	};\n'.$ula_prefix_msg.$routemsg.
			'};\n'.
			);							
			
		if($network!="")
		{
			startcmd('radvd -C '.$racfg.' -p .'.$rapid);
		}

		if($isula==0)
		{
			$dhcpcfg = '/var/run/dhcps6.'.$inf.'.conf';
			$dhcppid = '/var/run/dhcps6.'.$inf.'.pid';
		
			fwrite(w,$dhcpcfg,
				'option domain-name-servers '.$dns.';\n'.
				'option domain-name "'.$domain.'";\n'.
				$ntpsrvmsg.
				'interface '.$ifname.' {\n'.
				'	allow rapid-commit;\n'.
				'	preference 128;\n'.
				'	address-pool dhcpv6pool '.$pd_ptlft.' '.$pd_vtlft.';\n'.
				'};\n'.
				'\n'.
				'pool dhcpv6pool {\n'.
				'	range '.$start.' to '.$stop.';\n'.
				'};\n'.
				$pdmsg.
				$spdmsg
			);
	
			if($network!="")
			{
				startcmd('dhcp6s -c '.$dhcpcfg.' -P '.$dhcppid.' -s '.$hlp.' -u '.$inf.' '.$ifname);
			}
		}
	}	// if ($mode == 'STATELESS')
	
	startcmd('exit 0');

	/* stop dhcps & radvd */
	stopcmd("/etc/scripts/killpid.sh ".$rapid);
	stopcmd("rm -f ".$racfg);
	
	if($isula==0)
	{
		stopcmd("/etc/scripts/killpid.sh ".$dhcppid);
		stopcmd("rm -f ".$dhcpcfg);
		stopcmd("/etc/scripts/delpathbytarget.sh /runtime inf uid ".$inf." dhcps6");
	}
	
	//check if ra combine
	stopcmd('combuid=`xmldbc -w /runtime/ipv6/ula_global_combine_ra`');
	stopcmd('if [ "$combuid" != "'.$inf.'" ]; then\n'.
		'	service DHCPS6."$combuid" restart\n'.
		'fi\n');
		
	stopcmd('exit 0;');
}

/* Service will exit with:
 *	0 - Success
 *	8 - Interface is not available/active.
 *	9 - Something wrong with the configuration.
 */
function dhcps6setup($name)
{
	/* Get the interface */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
	startcmd('# '.$name.': infp='.$infp.', stsp='.$stsp);
	if ($stsp=="") return error(8, "dhcps6setup: ".$name." is not active.");

	/* Check runtime status */
	if (query($stsp."/inet/addrtype")!="ipv6" || query($stsp."/inet/ipv6/valid")!="1")
		return error(9, "dhcps6setup: ".$name." not IPv6.");

	/* Get the physical interface */
	$phyinf = query($stsp."/phyinf");
	if ($phyinf=="") return error(9, "dhcps6setup: ".$name." no phyinf.");

	/* Get the profile */
	$dhcps6 = query($infp."/dhcps6");
	$dhcpsp = XNODE_getpathbytarget("/dhcps6", "entry", "uid", $dhcps6, 0);
	if ($dhcpsp=="") return error(9, "dhcps6setup: ".$name." no profile.");

	/* */
	return commands($name, $stsp, $phyinf, $dhcpsp);
}
?>

