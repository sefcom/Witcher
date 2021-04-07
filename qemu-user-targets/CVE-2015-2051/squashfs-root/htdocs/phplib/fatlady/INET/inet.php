<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function check_inf($entry)
{
	anchor($entry);
	/* Builder, 2009/12/30, No more check defaultroute. Original defaultroute will be kept. */
	/* check lowerlayer */
	$value = query("lowerlayer");
	TRACE_debug("FATLADY: INF: lowerlayer=".$value);
	if ($value!="" && XNODE_getpathbytarget("", "inf", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/lowerlayer", "lowerlayer=".$value." doesn't exit!");
		return 9;
	}
	/* check upperlayer */
	$value = query("upperlayer");
	TRACE_debug("FATLADY: INF: upperlayer=".$value);
	if ($value!="" && XNODE_getpathbytarget("", "inf", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/upperlayer", "upperlayer=".$value." doesn't exit!");
		return 9;
	}
	/* check schedule */
	$value = query("schedule");
	TRACE_debug("FATLADY: INF: schedule=".$value);
	if ($value!="" && XNODE_getpathbytarget("/schedule", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/schedule", "schedule=".$value." doesn't exit!");
		return 9;
	}
	/* check dhcps4 */
	$value = query("dhcps4");
	TRACE_debug("FATLADY: INF: dhcps4=".$value);
	if ($value!="" && XNODE_getpathbytarget("/dhcps4", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/dhcps4", "dhcps4=".$value." doesn't exit!");
		return 9;
	}
	/* check dhcps6 */
	$value = query("dhcps6");
	TRACE_debug("FATLADY: INF: dhcps6=".$value);
	if ($value!="" && XNODE_getpathbytarget("/dhcps6", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/dhcps6", "dhcps6=".$value." doesn't exit!");
		return 9;
	}
	/* check ddns4 */
	$value = query("ddns4");
	TRACE_debug("FATLADY: INF: ddns4=".$value);
	if ($value!="" && XNODE_getpathbytarget("/ddns4", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/ddns4", "ddns4=".$value." doesn't exit!");
		return 9;
	}
	/* check ddns6 */
	$value = query("ddns6");
	TRACE_debug("FATLADY: INF: ddns6=".$value);
	if ($value!="" && XNODE_getpathbytarget("/ddns6", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/ddns6", "ddns6=".$value." doesn't exit!");
		return 9;
	}
	/* check dns4 */
	$value = query("dns4");
	TRACE_debug("FATLADY: INF: dns4=".$value);
	if ($value!="" && XNODE_getpathbytarget("/dns4", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/dns4", "dns4=".$value." doesn't exit!");
		return 9;
	}
	/* check dns6 */
	$value = query("dns6");
	TRACE_debug("FATLADY: INF: dns6=".$value);
	if ($value!="" && XNODE_getpathbytarget("/dns6", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/dns6", "dns6=".$value." doesn't exit!");
		return 9;
	}
	/* check nat */
	$value = query("nat");
	TRACE_debug("FATLADY: INF: nat=".$value);
	if ($value!="" && XNODE_getpathbytarget("/nat", "entry", "uid", $value, 0)=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $entry."/nat", "nat=".$value." doesn't exit!");
		return 9;
	}
	/* check web */
	$value = query("web");
	TRACE_debug("FATLADY: INF: web=".$value);
	if ($value!="")
	{
		if ($value < 0 || $value > 65535)
		{
			/* internal error, no i18n(). */
			set_result("FAILED", $entry."/web", "web=".$value);
			return 9;
		}

		/* check weballow */
		if ($value > 0)
		{
			$value = query("weballow/hostv4ip");
			if ($value != "" && INET_validv4addr($value)==0)
			{
				set_result("FAILED", $entry."/weballow/hostv4ip", i18n("Invalid IP address."));
				return 9;
			}
		}
	}
	/* check icmp */
	$value = query("icmp");
	TRACE_debug("FATLADY: INF: icmp=".$value);
	if ($value != "ACCEPT" && $value != "DROP") del("icmp");

	/* TODO: We should validate the UID. */
	TRACE_debug("FATLADY: INF: infprevious=".query("infprevious"));
	TRACE_debug("FATLADY: INF: infnext=".query("infnext"));
	TRACE_debug("FATLADY: INF: infnext:2=".query("infnext:2"));
	TRACE_debug("FATLADY: INF: child=".query("child"));
	TRACE_debug("FATLADY: INF: childgz=".query("childgz"));
	return 0;
}
function check_networkid($inetp, $inf, $prefix)
{
	if (query($inetp."/ipv4/static")==1)
	{
		/* check if the network id conflict */
		if (substr($inf, 0, 6)=="BRIDGE")
				$type="birdge";
		else	$type="router";

		if (query($prefix."/inf#") > 1)
				$infp = $prefix."/inf";	/* use service INET.INF */
		else	$infp = "/inf";			/* use service INET.XXXX */

		foreach ($infp)
		{
			$v_inf = query("uid");
			if ($v_inf == $inf)	continue;

			if (substr($v_inf, 0, 6)=="BRIDGE")
					$v_type = "bridge";
			else	$v_type = "router";

			if ($v_type != $type) continue;

			if ($infp=="/inf")	/* use service INET.XXXX */
			{
				$v_inetp = XNODE_getpathbytarget($_GLOBALS["FATLADY_base"], "module", "service", "INET.".$v_inf, 0);
				/* if inf also in teh postxml, use the data in the postxml instead of the one in xmldb.*/
				if ($v_inetp != "")
				{
					$v_inet	= query($v_inetp."/inf/inet");
					$v_inetp= XNODE_getpathbytarget($v_inetp."/inet", "entry", "uid", $v_inet, 0);
				}
				else
				{
					$v_inet	= query("inet");
					$v_inetp= XNODE_getpathbytarget("/inet", "entry", "uid", $v_inet, 0);
				}
			}
			else				/* use service INET.INF */
			{
				$v_inet = query("inet");
				$v_inetp= XNODE_getpathbytarget($prefix."/inet", "entry", "uid", $v_inet, 0);
			}
			if (query("active")!=1 ||
				query($v_inetp."/addrtype")!="ipv4" ||
				query($v_inetp."/ipv4/static")!=1) continue;

			/* The value of mask should be 1~32. */
			TRACE_debug("FATLADY: MASK-".query($inetp."/ipv4/mask"));
			TRACE_debug("FATLADY: MASK-".query($v_inetp."/ipv4/mask"));
			if (query($inetp."/ipv4/mask") <= 0 || query($inetp."/ipv4/mask") > 32)
				return set_result("FAILED", $inetp."/ipv4/mask", i18n("Invalid Subnet Mask value"));
			if (query($v_inetp."/ipv4/mask") <= 0 || query($v_inetp."/ipv4/mask") > 32)
				return set_result("FAILED", $v_inetp."/ipv4/mask", i18n("Invalid Subnet Mask value"));

			if (query($inetp."/ipv4/mask") < query($v_inetp."/ipv4/mask"))
					$mask = query($inetp."/ipv4/mask");
			else	$mask = query($v_inetp."/ipv4/mask");

			if (ipv4networkid(query($v_inetp."/ipv4/ipaddr"), $mask) ==
				ipv4networkid(query($inetp."/ipv4/ipaddr"), $mask))
			{
				return set_result("FAILED", $inetp."/ipaddr",
					i18n("The network id of $1 is the same with $2.", $inf, $v_inf));
			}
		}
	}
}
function fatlady_inet($prefix, $inf, $needgw)
{
	/* Check the interface setting */
	$infp = XNODE_getpathbytarget($prefix, "inf", "uid", $inf, 0);
	if ($infp=="")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $prefix."/inf/uid", "INF UID mismatch");
		return;
	}

	/* Check INET uid */
	$inet = query($infp."/inet");
	if ($inet == "")
	{
		/* We allow the inet to be disabled. */
		if (check_inf($infp) == "0") set_result("OK", "", "");
		if ($_GLOBALS["FATLADY_result"]=="OK")
			set($prefix."/valid", 1);
		else	set($prefix."/valid", 0);
		return;
	}
	$inetp = XNODE_getpathbytarget($prefix."/inet", "entry", "uid", $inet, 0);
	if ($inetp == "")
	{
		/* internal error, no i18n(). */
		set_result("FAILED", $infp."/inet", "Invalid inet uid");
		return;
	}

	if (query($infp."/active") == "1")
	{
		if (check_inf($infp) != "0") return;
		/* if this inf has lower layer, check lower layer. */
		$lower = query($infp."/lowerlayer");
		if ($lower!="")
		{
			$lowerp = XNODE_getpathbytarget($prefix, "inf", "uid", $lower, 0);
			/* lower layer must be atived. */
			set($lowerp."/active", 1);
			$upper_of_lower = query($lowerp."/upperlayer");
			if ($upper_of_lower == "")
			{
				set($lowerp."/upperlayer", $inf);
			}
			else if ($upper_of_lower != $inf) 
			{
				set_result("FAILED", $lowerp."/upperlayer", "upperlayer of ".$lower." should be [".$inf."] not [".$upper_of_lower."] !");
				return;
			}
			if (check_inf($lowerp) != "0") return;
		}

		$addrtype = query($inetp."/addrtype");
		$_GLOBALS["FATLADY_INF_UID"] = $inf;
		$_GLOBALS["FATLADY_INET_ENTRY"] = $inetp;
		$_GLOBALS["FATLADY_INET_NEED_GW"] = $needgw;
		$b = "/htdocs/phplib/fatlady/INET";
		if ($addrtype == "ipv4")
		{
			dophp("load", $b."/inet_ipv4.php");
			check_networkid($inetp, $inf, $prefix);
		}
		else if	($addrtype == "ipv6") dophp("load", $b."/inet_ipv6.php");
		else if	($addrtype == "ppp4") dophp("load", $b."/inet_ppp4.php");
		else if	($addrtype == "ppp6"
		||	 $addrtype == "ppp10") dophp("load", $b."/inet_ppp6.php");
		else set_result("FAILED", $inetp."/addrtype",
				"Unsupported address type : ".$addrtype); /* internal error, no i18n(). */
	}
	else
	{
		/* The valid value for active should be 1 or 0. */
		set($infp."/active", "0");
		set_result("OK", "", "");
	}
	if ($_GLOBALS["FATLADY_result"]=="OK")
			set($prefix."/valid", 1);
	else	set($prefix."/valid", 0);
}

?>
