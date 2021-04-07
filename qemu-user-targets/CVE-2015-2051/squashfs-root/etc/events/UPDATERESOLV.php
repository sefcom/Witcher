<?
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/inet.php";
$wan1_infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
$opendns_type = query($wan1_infp."/open_dns/type");

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

fwrite("w", "/etc/resolv.conf", "# Auto-Generated\n");
$domainlist="";
foreach ("/runtime/inf")
{
	$addrtype = query("inet/addrtype");
	$upperlayer = query("upperlayer");
	$lower_infp = "";
	$ppp_server_ip = "";
	if ($addrtype=="ipv4" || $addrtype=="ppp4")
	{
		if (query("inet/".$addrtype."/valid")=="1" && $opendns_type!="family" && $opendns_type!="parent")
		{
			$def = query("defaultroute");
		//	fwrite("a", $START, "#def: ".$def."\n");
			$uid = query("uid");
			if ($addrtype=="ipv4")
			{
				$ip = query("inet/".$addrtype."/ipaddr");
				$gw = query("inet/".$addrtype."/gateway");
				$mask = query("inet/".$addrtype."/mask");
			}
			else if ($addrtype=="ppp4")
			{
				$ip = query("inet/".$addrtype."/local");
				$gw = query("inet/".$addrtype."/peer");
				$mask = 32;

				// michael_lee because don't have lowerlayer, do it manualy
				$ppp_infp = XNODE_getpathbytarget("", "inf", "uid", $uid, 0);
				$lowerlayer = query($ppp_infp."/lowerlayer");
				if ($lowerlayer != "")
				{
					$lower_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $lowerlayer, 0);
					$ppp_inet = query("inet/uid");
					$inet_p = XNODE_getpathbytarget("/inet", "entry", "uid", $ppp_inet, 0);
					$ppp_over = query($inet_p."/ppp4/over");
					$ppp_server_ip = query($inet_p."/ppp4/".$ppp_over."/server");
					if (INET_validv4addr($ppp_server_ip) == 0)
					{
						$ppp_server_ip = query($inet_p."/ppp4/".$ppp_over."/olddomainip");
					}
				}
			}
			foreach ("inet/".$addrtype."/dns")
			{
				//fwrite("a", "/etc/resolv.conf", "nameserver ".$VaLuE."\n");
				fwrite("a", "/tmp/ipv4_resolv.conf", "nameserver ".$VaLuE."\n");
				// if dns is same subnet as lower layer. not add it via ppp interface
				if ($lower_infp != "")
				{
					$lower_ip = query($lower_infp."/inet/ipv4/ipaddr");
					$lower_mask = query($lower_infp."/inet/ipv4/mask");
					if (INET_validv4network($lower_ip, $VaLuE, $lower_mask) == 1) { continue; }
				}
				if (INET_validv4network($ip, $VaLuE, $mask) == 1)
				{
					continue;
				}
				if ($def!="" && $def>1)
				{
					if ($gw!="")
					{
						//hendry, workaround for loopback issue (if dns is same as ppp server ip)
						if($addrtype == "ppp4" &&  $ppp_server_ip==$VaLuE) { continue; }
						fwrite(a,$START,'ip route add '.$VaLuE.' via '.$gw.' metric '.$def.' table RESOLV\n');
						fwrite(a,$STOP, 'ip route del '.$VaLuE.' via '.$gw.' metric '.$def.' table RESOLV\n');
					}
					else
					{
						fwrite(a,$START,'ip route add '.$VaLuE.' metric '.$def.' table RESOLV\n');
						fwrite(a,$STOP, 'ip route del '.$VaLuE.' metric '.$def.' table RESOLV\n');
					}
				}
				else
				{
					if ($upperlayer!="" && $gw!="")
					{
						/* Joseph
						Prevent the status when we set the static mode in PPTP or L2TP with DNS server, 
						the DNS query from LAN side would go through the physic wan path but not ppp wan path.
						*/
						$ppp_metric = INF_getinfinfo($upperlayer, "defaultroute") + 500;
						fwrite(a,$START,'ip route add '.$VaLuE.' via '.$gw.' metric '.$ppp_metric.' table RESOLV\n');
						fwrite(a,$STOP, 'ip route del '.$VaLuE.' via '.$gw.' metric '.$ppp_metric.' table RESOLV\n');
					}	
					else if ($gw!="")
					{
						fwrite(a,$START,'ip route add '.$VaLuE.' via '.$gw.' table RESOLV\n');
						fwrite(a,$STOP, 'ip route del '.$VaLuE.' via '.$gw.' table RESOLV\n');
					}
					else
					{
						fwrite(a,$START,'ip route add '.$VaLuE.' table RESOLV\n');
						fwrite(a,$STOP, 'ip route del '.$VaLuE.' table RESOLV\n');
					}
				}
			}
		}
	}
	
	/* Check if mode is ppp6+ppp4 */
	if ($addrtype=="ppp4")
	{
		if (query("inet/".$addrtype."/valid")=="1")
		{
			foreach ("inet/ppp6/dns")
			{
				fwrite("a", "/etc/resolv.conf", "nameserver ".$VaLuE."\n");
			}
		}
	}

	if ($addrtype=="ipv6" || $addrtype=="ppp6")
	{
		if (query("inet/".$addrtype."/valid")=="1")
		{
			$def = query("defaultroute");
		//	fwrite("a", $START, "#def: ".$def."\n");
			$uid = query("uid");
			if ($addrtype=="ipv6")		{ $gw = query("inet/".$addrtype."/gateway"); }
			else if ($addrtype=="ppp6")	{ $gw = query("inet/".$addrtype."/peer"); }
			foreach ("inet/".$addrtype."/dns")
			{
				//fwrite("a", "/etc/resolv.conf", "nameserver ".$VaLuE."\n");
				fwrite("a", "/tmp/ipv6_resolv.conf", "nameserver ".$VaLuE."\n");
				if ($def!="" && $def>1)
				{
					if ($gw!="")
					{
						fwrite(a,$START,'ip -6 route add '.$VaLuE.' via '.$gw.' metric '.$def.' table RESOLV\n');
						fwrite(a,$STOP, 'ip -6 route del '.$VaLuE.' via '.$gw.' metric '.$def.' table RESOLV\n');
					}
					else
					{
						fwrite(a,$START,'ip -6 route add '.$VaLuE.' metric '.$def.' table RESOLV\n');
						fwrite(a,$STOP, 'ip -6 route del '.$VaLuE.' metric '.$def.' table RESOLV\n');
					}
				}
				else
				{
					if ($gw!="")
					{
						fwrite(a,$START,'ip -6 route add '.$VaLuE.' via '.$gw.' table RESOLV\n');
						fwrite(a,$STOP, 'ip -6 route del '.$VaLuE.' via '.$gw.' table RESOLV\n');
					}
					else
					{
						fwrite(a,$START,'ip -6 route add '.$VaLuE.' table RESOLV\n');
						fwrite(a,$STOP, 'ip -6 route del '.$VaLuE.' table RESOLV\n');
					}
				}
			}
                        $domain = query("inet/".$addrtype."/domain");
                        if($domainlist=="")
			{
                                $domainlist = $domain;
			}
                        else
			{
                                $domainlist = $domainlist." ".$domain;
			}
		}
	}
}
	//let v6 DNS priority first 
	$resolv_v6 = fread("s", "/tmp/ipv6_resolv.conf");
	$resolv_v4 = fread("s", "/tmp/ipv4_resolv.conf");
	fwrite("a", "/etc/resolv.conf", $resolv_v6);
	fwrite("a", "/etc/resolv.conf", $resolv_v4);
	unlink("/tmp/ipv4_resolv.conf");
	unlink("/tmp/ipv6_resolv.conf");

if ($opendns_type == "family" || $opendns_type == "parent")
{
	fwrite("a", "/etc/resolv.conf", "nameserver ".query($wan1_infp."/open_dns/".$opendns_type."_dns_srv/dns1")."\n");
	fwrite("a", "/etc/resolv.conf", "nameserver ".query($wan1_infp."/open_dns/".$opendns_type."_dns_srv/dns2")."\n");
	fwrite(a,$START,'ip route add '.query($wan1_infp."/open_dns/".$opendns_type."_dns_srv/dns1").' table RESOLV\n');
	fwrite(a,$START,'ip route add '.query($wan1_infp."/open_dns/".$opendns_type."_dns_srv/dns2").' table RESOLV\n');
	fwrite(a,$STOP,'ip route add '.query($wan1_infp."/open_dns/".$opendns_type."_dns_srv/dns1").' table RESOLV\n');
	fwrite(a,$STOP,'ip route add '.query($wan1_infp."/open_dns/".$opendns_type."_dns_srv/dns2").' table RESOLV\n');
}

fwrite("a", "/etc/resolv.conf", "search ".$domainlist."\n");

fwrite("a",$START,"service DNS restart\n");
fwrite("a",$START,"exit 0\n");
fwrite("a",$STOP, "exit 0\n");
?>
