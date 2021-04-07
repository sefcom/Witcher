<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inf.php";
/* include "/htdocs/phplib/inet6.php"; */

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function check_ipv4($path, $needgw)
{
	include "/htdocs/webinc/feature.php";
	anchor($path);
	$static = query("static");

	$ipipmode = query($path."/ipv4in6/mode");
	if($ipipmode != "")
	{
		TRACE_debug("FATLADY: INET_IPV4: IPIP mode :".$ipipmode);
		$ipipremote = query($path."/ipv4in6/remote");
		if($ipipremote!="")
		{
			TRACE_debug("FATLADY: IPIP remote IPv6 address :".$ipipremote);
			//if(INET_validv6addr($ipipremote) == 0)
			if(ipv6checkip($ipipremote) != 1)
			{
				set_result("FAILED", $path."/ipaddr", "Invalid IPv6 address");
				return;
			}
			//$type = INET_v6addrtype($ipipremote);
			$type = ipv6addrtype($ipipremote);
			TRACE_debug("FATLADY: IPIP remote IPv6 address type :".$type);
			if($type=="ANY" || $type=="MULTICAST" || $type=="LOOPBACK")
			{
				set_result("FAILED", $path."/ipaddr", "Invalid IPv6 address type.");
				return;

			}
		}
		$ip = query("ipaddr");/* ip address of B4 */
		if($ip != "")
		{
			$ip_part = cut($ip,3,'.');
			if($ip_part<2 || $ip_part>7)
			{
				set_result("FAILED", $path."/ipaddr", i18n("The range of B4 IPv4 address is from 192.0.0.2 to 192.0.0.7"));
				return;
			}

		}
		set_result("OK","","");
		return;
	}

	if ($static != "1") set("static", "0");
	TRACE_debug("FATLADY: INET_IPV4: static = ".$static);
	if ($static == "1")
	{
		$ip = query("ipaddr");
		$mask = query("mask");
		$dhcps4 = INF_getinfinfo($_GLOBALS["FATLADY_INF_UID"], "dhcps4");

		TRACE_debug("FATLADY: INET_IPV4: ip = ".$ip);
		TRACE_debug("FATLADY: INET_IPV4: mask = ".$mask);
		if (INET_validv4addr($ip)==0)
		{
			set_result("FAILED", $path."/ipaddr", i18n("Invalid IP Address"));
			return;
		}
		if ($mask=="")
		{
			set_result("FAILED", $path."/mask", i18n("No Subnet Mask value"));
			return;
		}
		if ($mask <0 || $mask >32)
		{
			set_result("FAILED", $path."/mask", i18n("Invalid Subnet Mask value"));
			return;
		}
		if ($mask <8)
		{
			set_result("FAILED", $path."/mask", i18n("The router would not support the subnet mask which length is less than Class A."));
			return;
		}		
		if (INET_validv4host($ip, $mask)==0)
		{
			set_result("FAILED", $path."/ipaddr", i18n("Invalid IP Address"));
			return;
		}
		
		if ( INET_addr_strip0($gw) == $ip )
		{
			set_result("FAILED", $path."/gateway", i18n("The IP address cannot be equal to the gateway address."));
			return;
		}
		
		set("ipaddr", INET_addr_strip0($ip));
		$ip = query("ipaddr");


		$gw = query("gateway");
		TRACE_debug("FATLADY: INET_IPV4: gw=".$gw);
		if ($gw=="")
		{
			if ($needgw=="1" && $static=="1")
			{
				set_result("FAILED", $path."/gateway", i18n("No  gateway  address"));
				return;
			}
		}
		else
		{
			if (INET_validv4host($gw, $mask) == 0)
			{
				set_result("FAILED", $path."/gateway", i18n("Invalid Default Gateway address"));
				return;
			}
			if (ipv4networkid($gw,$mask) != ipv4networkid($ip,$mask))
			{
				set_result("FAILED", $path."/gateway", i18n("The default gateway should be in the same network"));
				return;
			}
						
			if ( INET_addr_strip0($gw) == $ip )
			{
				set_result("FAILED", $path."/gateway", i18n("The IP address can not be equal to the Default Gateway address"));
				return;
			}
			
			set("gateway", INET_addr_strip0($gw));
		}
	}
	else if (query("dhcpplus/enable")!="")
	{
		/* User Name & Password */
		if (query("dhcpplus/enable")=="1" && query("dhcpplus/username")=="")
		{
			set_result("FAILED",$path."/dhcpplus/username",i18n("The user name can not be empty"));
			return;
		}
	}

	$cnt = query("dns/count");
	$i = 0;
	while ($i < $cnt)
	{
		$i++;
		$value = query("dns/entry:".$i);
		TRACE_debug("FATLADY: INET_IPV4: dns".$i."=".$value);
		if (INET_validv4addr($value)==0)
		{
			set_result("FAILED", $path."/dns/entry:".$i, i18n("Invalid DNS address"));
			return;
		}
		
		set("dns/entry:".$i, INET_addr_strip0($value));

		if ($static == "1")
		{
			if (ipv4networkid($value,$mask) == ipv4networkid($ip,$mask))
			{
				TRACE_debug("FATLADY: INET_IPV4: dns".$i."=".$value." is in the same network as IP:".$ip);
				if (INET_validv4host($value, $mask) == 0)
				{
					set_result("FAILED", $path."/dns/entry:".$i, i18n("Invalid DNS address"));
					return;
				}
				if ( $value == $ip )
				{
					set_result("FAILED", $path."/dns/entry:".$i, i18n("Invalid DNS address"));
					return;
				}
			}
		}

		if ($i > 1)
		{
			$j = $i - 1;
			$k = 0;
			while ($k < $j)
			{
				$k++;
				$dns = query("dns/entry:".$k);
				if($value == $dns)
				{
					set_result("FAILED", $path."/dns/entry:2", i18n("Secondary DNS server should not be the same as Primary DNS server."));
					return;
				}
			}
		}
	}

	$mtu = query("mtu");
	TRACE_debug("FATLADY: INET_IPV4: mtu=".$mtu);
	if ($mtu!="")
	{
		if (isdigit($mtu)=="0")
		{
			set_result("FAILED", $path."/mtu",
				i18n("The MTU value is invalid."));
			return;
		}
		if ($mtu<576 && $FEATURE_NOIPV6==1)
		{
			set_result("FAILED", $path."/mtu",
				i18n("The MTU value is not within the required range. Enter a value between 576 and 1500."));
			return;
		}
		if ($mtu<1280 && $FEATURE_NOIPV6==0)
		{
			set_result("FAILED", $path."/mtu",
				i18n("The MTU value is too small, the valid value is 1280 ~ 1500."));
			return;
		}		
		if ($mtu>1500)
		{
			if($FEATURE_NOIPV6==0)	{set_result("FAILED", $path."/mtu",i18n("The MTU value is too large, the valid value is 1280 ~ 1500."));}
			else {set_result("FAILED", $path."/mtu",i18n("The MTU value is too large, the valid value is 576 ~ 1500."));}
			return;
		}
	}

	set_result("OK","","");
}

TRACE_debug("FATLADY: INET: inetentry=[".$_GLOBALS["FATLADY_INET_ENTRY"]."]");
set_result("FAILED","","");
if ($_GLOBALS["FATLADY_INET_ENTRY"]=="") set_result("FAILED","","No XML document");
else check_ipv4($_GLOBALS["FATLADY_INET_ENTRY"]."/ipv4", $_GLOBALS["FATLADY_INET_NEED_GW"]);
?>
