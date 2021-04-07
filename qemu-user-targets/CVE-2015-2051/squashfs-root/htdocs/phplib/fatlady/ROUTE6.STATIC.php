<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be breaked for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/inet6.php";

function result($result, $node, $msg)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $msg;
	return $result;
}

function verify_setting($path, $max)
{
	$count = query($path."/count"); if ($count=="") $count=0;
	$seqno = query($path."/seqno");
	if ($count > $max) $count = $max;

	TRACE_debug("FATLADY: ROUTE6.STATIC: max=".$max.", count=".$count.", seqno=".$seqno);

	/* Delete the extra entries. */
	$num = query($path."/entry#");
	while ($num>$count) { del($path."/entry:".$num); $num--; }

	/* verify each entries */
	set($path."/count", $count);
	foreach ($path."/entry")
	{
		if ($InDeX>$count) break;

		/* The current entry path. */
		$entry = $path."/entry:".$InDeX;

		/* Check empty UID */
		$uid = query("uid");
		if ($uid=="")
		{
			$uid = "SRT-".$seqno;
			set("uid", $uid);
			$seqno++;
			set($path."/seqno", $seqno);
		}
		/* Check duplicated UID */
		if ($$uid=="1") return result("FAILED", $entry."/uid", "Duplicated UID - ".$uid);
		$$uid = "1";

		$desc = query("description");
		if($desc!="")	set("description", $desc);

		$metric = query("metric");
		if($metric!="")	set("metric", $metric);

		$prefix = query("prefix");
		if (isdigit($prefix)!="1" || $prefix>128 || $prefix<0)
			return result("FAILED", $entry."/prefix", i18n("Invalid prefix length."));
		$netid = tolower(query("network"));
		TRACE_debug("should be = ".ipv6networkid($netid, $prefix));
		TRACE_debug("  we have = ".$netid);
		if (ipv6networkid($netid, $prefix) != $netid)
			return result("FAILED", $entry."/network", i18n("Invalid IPv6 prefix."));

		$inf = query("inf");
		TRACE_debug("inf = ".$inf);
		$inf1 = cut($inf, 0, "-");
		if($inf1=="WAN" || $inf1=="LAN")
		{
			$infp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
			if ($infp=="") return result("FAILED", $entry."/inf", i18n("Invalid interface."));
		}

		/* The gateway should be in the same network of the interface. */
		$gw = query("via");
		if (INET_validv6addr($gw)!=1 && $inf!= "PD")
			return result("FAILED", $entry."/via", i18n("Invalid IPv6 gateway address."));

		/* Check duplicated network */
		$i = 1;
		while ($i < $InDeX)
		{
			$n = query($path."/entry:".$i."/network");
			$p = query($path."/entry:".$i."/prefix");
			if (ipv6networkid($n, $p) == $netid)
				return result("FAILED", $path."/entry:".$i."/network",
							i18n("Duplicated IPv6 prefix."));
			$i++;
		}
	}
	return "OK";
}

/*************************************************/
$max = query("/route6/static/max"); if ($max=="") $max=64;
if (verify_setting($FATLADY_prefix."/route6/static", $max)=="OK")
{
	set($FATLADY_prefix."/valid", 1);
	result("OK", "", "");
}

?>
