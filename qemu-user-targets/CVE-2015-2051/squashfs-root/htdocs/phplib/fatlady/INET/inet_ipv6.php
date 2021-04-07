<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inet6.php";

function result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

function check_static($path, $needgw)
{
	anchor($path);

	$ip = query("ipaddr");
	$prefix = query("prefix");
	TRACE_debug("fatlady: inet_ipv6: ip/prefix = ".$ip."/".$prefix);
	
	if (INET_validv6addr($ip) == 0)
		return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 address."));
		
	$type = INET_v6addrtype($ip);
	TRACE_debug("FATLADY: INET_IPV6: ipv6 type = ".$type);
	//if($type=="") return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 address."));
	//if($type!="UNICAST" && $type!="LINKLOCAL" && $type!="SITELOCAL" && $type!="COMPATv4" && $type!="MAPPED" && $type!="RESERVED")

	if($needgw=="1")//WAN
	{
		if($type=="ANY" || $type=="MULTICAST" || $type=="LOOPBACK")
			//return result("FAILED", $path."/ipaddr", i18n("Not a global/linklocal/sitelocal IPv6 address"));
			return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 address."));
	}
	else //LAN
	{
		if($type=="ANY" || $type=="MULTICAST" || $type=="LOOPBACK" || $type=="LINKLOCAL" || $type=="SITELOCAL")
			return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 address."));
	}

	if ($prefix=="")
		return result("FAILED", $path."/prefix", i18n("No prefix value"));

	if (isdigit($prefix)=="0")
		return result("FAILED", $path."/prefix", i18n("Prefix value must be digit number"));
	if ($prefix<=0 || $prefix>128)
		return result("FAILED", $path."/prefix", i18n("Invalid prefix value."));

	$routerlft = query("routerlft");
	TRACE_debug("FATLADY: INET_IPV6: routerlft = ".$routerlft);
	if ($routerlft!="")
	{
		if (isdigit($routerlft)=="0")
			return result("FAILED", $path."/routerlft", i18n("Lifetime value must be digit number"));
		if ($routerlft < 12 || $routerlft > 5400)
			return result("FAILED", $path."/routerlft", i18n("Invalid router lifetime value"));
	}

	$preferlft = query("preferlft");
	TRACE_debug("FATLADY: INET_IPV6: preferlft = ".$preferlft);
	if ($preferlft!="")
	{
		if (isdigit($preferlft)=="0")
			return result("FAILED", $path."/preferlft", i18n("Lifetime value must be digit number"));
		if ($preferlft < 0)
			return result("FAILED", $path."/preferlft", i18n("Invalid preferred lifetime value"));
	}

	$validlft = query("validlft");
	TRACE_debug("FATLADY: INET_IPV6: validlft = ".$validlft);
	if ($validlft!="")
	{
		if (isdigit($validlft)=="0")
			return result("FAILED", $path."/validlft", i18n("Lifetime value must be digit number"));
		if ($validlft < 0)
			return result("FAILED", $path."/validlft", i18n("Invalid lifetime value."));
	}

	if ($preferlft!="" && $validlft=="")
		return result("FAILED", $path."/validlft", i18n("Don't leave the valid lifetime value blank."));
	if ($preferlft=="" && $validlft!="")
		return result("FAILED", $path."/preferlft", i18n("Don't leave preferred lifetime blank"));
	if ($preferlft!="" && $validlft!="")
	{
		if ($preferlft > $validlft)
			return result("FAILED", $path."/preferlft",
				i18n("Preferred lifetime has larger than valid lifetime"));
	}

	$gw = query("gateway");
	TRACE_debug("FATLADY: INET_IPV6: gw=".$gw);
	if($gw=="")
	{
		if ($_GLOBALS["FATLADY_INET_NEED_GW"]==1)
			return result("FAILED", $path."/gateway", i18n("No default gateway IPv6 address"));
	}
	else
	{
		if (INET_validv6addr($gw) == 0)
				return result("FAILED", $path."/gateway", i18n("Invalid IPv6 gateway address."));

		if (INET_v6addrtype($gw) != "LINKLOCAL")
		{
				if(ipv6networkid($gw, $prefix)!=ipv6networkid($ip, $prefix))
					return result("FAILED", $path."/gateway", i18n("The default gateway should be in the same network."));
		}
	}
	return "OK";
}

function check_dhcp($path)
{
	anchor($path);

	$gw = query("gateway");
	TRACE_debug("FATLADY: INET_IPV6: gateway=".$gw);
	if ($gw!="" && INET_validv6addr($gw)==0)
		return result("FAILED", $path."/gateway", i18n("Invalid IPv6 gateway address."));

	$opt = query("dhcpopt");
	TRACE_debug("FATLADY: INET_IPV6: dhcpopt=".$opt);
	if ($opt=="" || $opt=="IA-NA" || $opt=="IA-PD" || $opt=="IA-NA+IA-PD") return "OK";
	return result("FAILED", $path."/dhcpopt", i18n("Invalid DHCP option."));
}

function check_6in4($path)
{
	anchor($path);

	$ipaddr = query("ipaddr");
	$prefix = query("prefix");
	$remote = query($path."/ipv6in4/remote");

	TRACE_debug("FATLADY: INET_IPV6: ipaddr/prefix = ".$ipaddr."/".$prefix);
	TRACE_debug("FATLADY: INET_IPV6: remote = ".$remote);

	if (INET_validv6addr($ipaddr) == 0)
		return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 address."));
	if (INET_validv4addr($remote) == 0)
		return result("FAILED", $path."/ipv6in4/remote", i18n("Invalid IPv4 address."));
	if ($prefix=="")
		return result("FAILED", $path."/prefix", i18n("No prefix value."));
	if (isdigit($prefix)=="0")
		return result("FAILED", $path."/prefix", i18n("Prefix value must be digit number."));
	if ($prefix <= 0 || $prefix > 128)
		return result("FAILED", $path."/prefix", i18n("Invalid prefix value."));

	$gw = query("gateway");
	TRACE_debug("FATLADY: INET_IPV6: gateway=".$gw);
	if ($gw ==""  || INET_validv6addr($gw)==0)
		return result("FAILED", $path."/gateway", i18n("Invalid Remote IPv6 Address."));

	return "OK";
}
function check_6to4($path)
{
	anchor($path);

	$relay = query("ipv6in4/relay");
	$slaid = query("ipv6in4/ipv6to4/slaid");

	TRACE_debug("FATLADY: INET_IPV6: 6to4: relay [".$relay."]");
	TRACE_debug("FATLADY: INET_IPV6: 6to4: slaid [".$slaid."]");

	if ($relay!="" && INET_validv4addr($relay)==0)
		return result("FAILED", $path."/ipv6in4/relay", i18n("Invalid IPv4 relay router address."));
	if ($slaid!="" && isxdigit($slaid)==0)
		return result("FAILED", $path."/ipv6in4/ipv6to4/slaid", i18n("Invalid IPv6 SLA address."));

	return "OK";
}
function check_6rd($path)
{
	anchor($path);

	$ipaddr = query("ipv6in4/rd/ipaddr");
	$prefix = query("ipv6in4/rd/prefix");
	$v4mask = query("ipv6in4/rd/v4mask");
	$relay	= query("ipv6in4/relay");

	TRACE_debug("FATLADY: INET_IPV6: 6RD ipaddr/prefix = ".$ipaddr."/".$prefix);
	TRACE_debug("FATLADY: INET_IPV6: 6RD v4mask/relay = ".$v4mask."/".$relay);

	if($ipaddr=="") return "OK";
	if (INET_validv6addr($ipaddr)==0)
		return result("FAILED", $path."/ipv6in4/rd/ipaddr", i18n("Invalid IPv6 prefix."));

	if ($prefix=="")
		return result("FAILED", $path."/ipv6in4/rd/prefix", i18n("Invalid IPv6 prefix length."));
	if (isdigit($prefix)==0)
		return result("FAILED", $path."/ipv6in4/rd/prefix", i18n("The prefix length must be a number."));
	if ($prefix<=0 || $prefix>128)
		return result("FAILED", $path."/ipv6in4/rd/prefix", i18n("Invalid IPv6 prefix length."));

	if ($v4mask=="")
		return result("FAILED", $path."/ipv6in4/rd/v4mask", i18n("Invalid IPv4 mask length."));
	if (isdigit($v4mask)==0)
		return result("FAILED", $path."/ipv6in4/rd/v4mask", i18n("The IPv4 address mask must be a number."));
	if ($v4mask<0 || $v4mask>32)
		return result("FAILED", $path."/ipv6in4/rd/v4mask", i18n("Invalid IPv4 mask length."));

	if ($relay!="" && INET_validv4addr($relay)==0)
		return result("FAILED", $path."/ipv6in4/relay", i18n("Invalid IPv4 relay router address."));

	return "OK";
}
function check_tsp($path)
{
	anchor($path);
	
	$remote   = query("ipv6in4/remote");
	$username = query("ipv6in4/tsp/username");
	$password = query("ipv6in4/tsp/password");
	$prefix   = query("ipv6in4/tsp/prefix");

	TRACE_debug("FATLADY: INET_IPV6: tspc remote = ".$remote);
	TRACE_debug("FATLADY: INET_IPV6: tspc username/password = ".$username."/".$password);
	TRACE_debug("FATLADY: INET_IPV6: tspc prefix = ".$prefix);

	if (INET_validv4addr($remote) == 0)
		return result("FAILED", $path."/ipv6in4/remote", i18n("Invalid IPv4 address."));

	return "OK";
}

function check_ul($path)
{
	anchor($path);
	$ip = query("ipaddr");
	$prefix = query("prefix");
	TRACE_debug("FATLADY: INET_IPV6: prefix/prefix len = ".$ip."/".$prefix);
	
	if($ip=="") return "OK";

	if (INET_validv6addr($ip)==0)
		return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 prefix."));

	if (ipv6networkid($ip, 8)!="fd00::")
		return result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 prefix."));

	if ($prefix=="")
		return result("FAILED", $path."/prefix", i18n("Invalid IPv6 prefix length."));

	if (isdigit($prefix)==0)
		return result("FAILED", $path."/prefix", i18n("The prefix length must be a number."));

	if ($prefix!=64)
		return result("FAILED", $path."/prefix", i18n("Invalid IPv6 prefix length."));

	return "OK";
}

function check_ipv6($path, $needgw)
{
	anchor($path);
	$mode = query("mode");
	TRACE_debug("FATLADY: INET_IPV6: mode = ".$mode);

	if		($mode=="LL")		return result("OK","","");
	else if	($mode=="AUTO"
		||	 $mode=="PPPDHCP")	$ret = check_dhcp($path);
	else if	($mode=="STATIC")	$ret = check_static($path, $needgw);
	else if ($mode=="6IN4")		$ret = check_6in4($path);
	else if	($mode=="6TO4")		$ret = check_6to4($path);
	else if	($mode=="6RD")		$ret = check_6rd($path);
	else if ($mode=="TSP")		$ret = check_tsp($path);
	else if ($mode=="AUTODETECT")		$ret = "OK";
	else if ($mode=="UL")		$ret = check_ul($path);
	else return result("FAILED", $path."/mode", i18n("Unknown IPv6 addressing type."));

	if ($ret!="OK") return $ret;

	/* Check hint */
	$pdhint_enable = query("pdhint/enable");
	if($pdhint_enable=="1")
	{
		$pdhint_network  = query("pdhint/network");
		$pdhint_prefix  = query("pdhint/prefix");
		$pdhint_plft    = query("pdhint/preferlft");
		$pdhint_vlft    = query("pdhint/validlft");
		TRACE_debug("FATLADY: INET_IPV6: PD_HINT prefix: ".$pdhint_network."/".$pdhint_prefix);
		if (INET_validv6addr($pdhint_network)==0)
			return result("FAILED", $path."/pdhint/network", i18n("Invalid IPv6 address."));
		$type = INET_v6addrtype($pdhint_network);
		if($type=="ANY" || $type=="MULTICAST" || $type=="LOOPBACK" || $type=="LINKLOCAL" || $type=="SITELOCAL")
			return result("FAILED", $path."/pdhint/network", i18n("Invalid IPv6 address."));
			
		if ($pdhint_prefix=="")
			return result("FAILED", $path."/pdhint/prefix", i18n("No prefix value"));
		if (isdigit($pdhint_prefix)=="0")
			return result("FAILED", $path."/pdhint/prefix", i18n("Prefix value must be digit number"));
		if ($pdhint_prefix<=0 || $pdhint_prefix>128)
			return result("FAILED", $path."/pdhint/prefix", i18n("Invalid prefix value."));
	
		TRACE_debug("FATLADY: INET_IPV6: PD_HINT preferlft = ".$pdhint_plft);
		if ($pdhint_plft!="")
		{
			if (isdigit($pdhint_plft)=="0")
				return result("FAILED", $path."/pdhint/preferlft", i18n("Lifetime value must be digit number"));
			if ($pdhint_plft < 0)
				return result("FAILED", $path."/pdhint/preferlft", i18n("Invalid preferred lifetime value"));
		}
		else
		{
			return result("FAILED", $path."/pdhint/preferlft", i18n("No prefer lifetime value"));
		}

		if ($pdhint_vlft!="")
		{
			TRACE_debug("FATLADY: INET_IPV6: PD_HINT validlft = ".$pdhint_vlft);
			if (isdigit($pdhint_vlft)=="0")
				return result("FAILED", $path."/pdhint/validlft", i18n("Lifetime value must be digit number"));
			if ($pdhint_vlft < 0)
				return result("FAILED", $path."/validlft", i18n("Invalid lifetime value."));
		}
		else
		{
			return result("FAILED", $path."/validlft", i18n("Invalid lifetime value."));
		}
	}

	/* Check DNS */
	$cnt = query("dns/count");
	foreach ("dns/entry")
	{
		if ($InDeX>$cnt) break;
		TRACE_debug("FATLADY: INET_IPV6: dns".$InDeX."=".$VaLuE);
		if (INET_validv6addr($VaLuE)==0)
			return result("FAILED", $path."/dns/entry:".$InDeX, i18n("Invalid IPv6 DNS address."));
		$type = INET_v6addrtype($VaLuE);
		if($type=="ANY" || $type=="MULTICAST" || $type=="LOOPBACK" || $type=="LINKLOCAL" || $type=="SITELOCAL")
			return result("FAILED", $path."/dns/entry:".$InDeX, i18n("Invalid IPv6 DNS address."));
	}
	$dns1 = query("dns/entry:1");
	$dns2 = query("dns/entry:2");
	if($cnt =="2"){
		if($dns1==$dns2)
			return result("FAILED", $path."/dns/entry:2", i18n("The Secondary DNS Server  can not be the same with the Primary DNS Server."));
	}
	$mtu = query("mtu");
	TRACE_debug("FATLADY: INET_IPV6: mtu=".$mtu);
	if ($mtu!="")
	{
		if (isdigit($mtu)=="0") return result("FAILED", $path."/mtu", i18n("The MTU value is invalid."));
		/* RFC 2460 */
		if ($mtu<1280)
			return result("FAILED", $path."/mtu",
				i18n("The MTU value is too small, the valid value is 1280 ~ 1500."));
		if ($mtu>1500)
			return result("FAILED", $path."/mtu",
				i18n("The MTU value is too large, the valid value is 1280 ~ 1500."));
	}

	return result("OK","","");
}

/* Main entry ***************************************************************/
TRACE_debug("FATLADY: INET: inetentry=[".$_GLOBALS["FATLADY_INET_ENTRY"]."]");
result("FAILED","","");
if ($_GLOBALS["FATLADY_INET_ENTRY"]=="") result("FAILED","","No XML document");
else check_ipv6($_GLOBALS["FATLADY_INET_ENTRY"]."/ipv6", $_GLOBALS["FATLADY_INET_NEED_GW"]);
?>
