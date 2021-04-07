<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet6.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function check_ppp6($path)
{
	anchor($path);

	/* IP address */
	$static = query("static");
	if ($static == "1")
	{
		$ipaddr = query("ipaddr");
		if (INET_validv6addr($ipaddr)==0)
		{
			set_result("FAILED",$path."/ipaddr",i18n("Invalid IP Address"));
			return;
		}
		$type = INET_v6addrtype($ipaddr);
		TRACE_debug("FATLADY: INET_PPP6: ipv6 type = ".$type);
		if($type=="ANY" || $type=="MULTICAST" || $type=="LOOPBACK" || $type=="LINKLOCAL" || $type=="SITELOCAL")
		{
			set_result("FAILED", $path."/ipaddr", i18n("Invalid IPv6 address."));
			return;
		}
	}
	else
	{
		/* if static is not 1, it should be 0. */
		set("static", "0");
		del("ipaddr");
	}

	/* DNS */
	$cnt = query("dns/count");
	$i = 0;
	while ($i < $cnt)
	{
		$i++;
		$value = query("dns/entry:".$i);
		if (INET_validv6addr($value)==0)
		{
			set_result("FAILED",$path."/dns:".$i, i18n("Invalid DNS address"));
			return;
		}
	}

	/* MTU/MRU */
	$mtu = query("mtu");
	if ($mtu != "")
	{
		if (isdigit($mtu)=="0")
		{
			set_result("FAILED",$path."/mtu",
				i18n("The MTU value is invalid."));
			return;
		}
		if ($mtu < 1280)
		{
			set_result("FAILED",$path."/mtu",
				i18n("The MTU value is too small, the valid value mustn't be smaller than 1280."));
			return;
		}
		if ($mtu > 1492)
		{
			set_result("FAILED",$path."/mtu",
				i18n("The MTU value is not within the required range. Enter a value between 1280 and 1492."));
			return;
		}
		$mtu = $mtu + 1 - 1; /* convert to number */
		set("mtu", $mtu);
	}
	$mru = query("mru");
	if ($mru != "")
	{
		if (isdigit($mru)=="0")
		{
			set_result("FAILED",$path."/mtu",
				i18n("The MRU value is invalid."));
			return;
		}
		if ($mru < 576)
		{
			set_result("FAILED",$path."/mru",
				i18n("The MRU value is too small, the valid value is 576 ~ 1492."));
			return;
		}
		if ($mru > 1492)
		{
			set_result("FAILED",$path."/mru",
				i18n("The MRU value is too large, the valid value is 576 ~ 1492."));
			return;
		}
		$mru = $mru + 1 - 1; /* convert to number */
		set("mru", $mru);
	}

	/* User Name & Password */
	if (query("username")=="")
	{
		set_result("FAILED",$path."/username",i18n("The user name can not be empty"));
		return;
	}

	/* dialup */
	$mode = query("dialup/mode");
	if ($mode != "auto" && $mode != "manual" && $mode != "ondemand")
	{
		/* no i18n */
		set_result("FAILED",$path."/dialup/mode","Invalid value for dial up mode - ".$mode);
		return;
	}
	$tout = query("dialup/idletimeout");
	if ($tout != "")
	{
		if (isdigit($tout)=="0" || $tout < 0 || $tout > 10000)
		{
			set_result("FAILED",$path."/dialup/mode",
				i18n("Invalid value for idle timeout."));
			return;
		}
	}
	set_result("OK","","");
}

TRACE_debug("FATLADY: INET: inetentry=[".$_GLOBALS["FATLADY_INET_ENTRY"]."]");
set_result("FAILED","","");
if ($_GLOBALS["FATLADY_INET_ENTRY"]=="") set_result("FAILED","","No XML document");
else check_ppp6($_GLOBALS["FATLADY_INET_ENTRY"]."/ppp6");
?>
