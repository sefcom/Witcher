<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

function INF_getinfpath($UID)
{
	return XNODE_getpathbytarget("", "inf", "uid", $UID, "0");
}

function INF_getinfinfo($UID, $info)
{
	$infp = XNODE_getpathbytarget("", "inf", "uid", $UID, "0");
	if ($infp != "") return query($infp."/".$info);
	return "";
}

function INF_getcfgipaddr($UID)
{
	$INET = INF_getinfinfo($UID, "inet");
	$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $INET, 0);
	if ($inetp == "") return "";

	$addrtype = query($inetp."/addrtype");
	anchor($inetp."/".$addrtype);
	if ($addrtype == "ipv4" || $addrtype == "ipv6")
	{
		if (query("static")==1) return query("ipaddr");
	}
	return "";
}

function INF_getcfgmask($UID)
{
	$INET = INF_getinfinfo($UID, "inet");
	$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $INET, 0);
	if ($inetp == "") return "";

	$addrtype = query($inetp."/addrtype");
	anchor($inetp."/".$addrtype);
	if ($addrtype == "ipv4" || $addrtype == "ipv6")
	{
		if (query("static")==1) return query("mask");
	}
	return "";
}

function INF_getcurripaddr($UID)
{
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $UID, "0");
	if ($infp == "") return "";

	$addrtype = query($infp."/inet/addrtype");
	anchor($infp."/inet/".$addrtype);
	if ($addrtype == "ipv4" || $addrtype == "ipv6")	return query("ipaddr");
	if ($addrtype == "ppp4" || $addrtype == "ppp6")	return query("local");
	return "";
}

function INF_getcurrmask($UID)
{
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $UID, "0");
	if ($infp == "") return "";

	$addrtype = query($infp."/inet/addrtype");
	anchor($infp."/inet/".$addrtype);
	if ($addrtype == "ipv4" || $addrtype == "ipv6") return query("mask");
	if ($addrtype == "ppp4" || $addrtype == "ppp6") return "32";
	return "";
}

function INF_getcurrgateway($UID)
{
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $UID, "0");
	if ($infp == "") return "";

	$addrtype = query($infp."/inet/addrtype");
	anchor($infp."/inet/".$addrtype);
	if ($addrtype == "ipv4" || $addrtype == "ipv6") return query("gateway");
	if ($addrtype == "ppp4" || $addrtype == "ppp6") return query("remote");
	return "";
}

function INF_getcurrdns($UID)
{
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $UID, "0");
	if ($infp == "") return "";

	$addrtype = query($infp."/inet/addrtype");
	if ($addrtype != "ipv4" && $addrtype != "ipv6" && $addrtype != "ppp4" && $addrtype != "ppp6")
		return "";
	anchor($infp."/inet/".$addrtype);
	if (query("dns#") > 1)	return query("dns:1")." ".query("dns:2");
	else					return query("dns");
}


function INF_getcurraddrtype($UID)
{
	$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $UID, "0");
	if ($infp == "") return "";
	return query($infp."/inet/addrtype");
}
?>
