<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

function result($res, $node, $msg)
{
	$_GLOBALS["FATLADY_result"] = $res;
	$_GLOBALS["FATLADY_node"] = $node;
	$_GLOBALS["FATLADY_message"] = $msg;
	return $res;
}

function valid_mac($mac)
{
	if ($mac=="") return 0;
	$num = cut_count($mac, ":");
	if ($num!=6) return 0;
	$num--;
	while ($num>=0)
	{
		$tmp = cut($mac, $num, ':');
		if (isxdigit($tmp)==0) return 0;
		$num--;
	}
	return 1;
}

function verify_staticleases($b)
{
	anchor($b);
	/* Can we trust this max. value here ?
	 * This value might be fake from the attack, so we limit the value to 128.
	 * by David Hsieh */
	$max = query("staticleases/max");
	if ($max>128) $max=128;

	$seqno = query("staticleases/seqno");
	$count = query("staticleases/count");
	if ($count>$max) $count=$max;
	foreach("staticleases/entry")
	{
		if ($InDeX>$count) break;
		$curr = $b."/staticleases/entry:".$InDeX;

		$uid = query("uid");
		/* This should be a new entry if the uid is empty. */
		if ($uid=="") { $uid="STIP-".$seqno; $seqno++; }
		/* Check duplicated UID */
		if ($$uid=="1") return result("FAILED", $curr."/uid", "Duplicated UID - ".$uid);
		/* Set and mark the UID */
		set("uid", $uid);
		$$uid = "1";
		/* Check empty hostname, macaddr & hostid */
		$hostname = query("hostname");
		$macaddr  = query("macaddr");
		$hostid   = query("hostid");
		if ($hostname=="" || isdomain($hostname)=="0")
			return result("FAILED", $curr."/hostname", i18n("Invalid host name."));
		if ($macaddr=="")
			return result("FAILED", $curr."/macaddr",  i18n("No MAC address value."));
		if (valid_mac($macaddr)!=1)
			return result("FAILED", $curr."/macaddr",  i18n("Invalid MAC address value."));
		if ($hostid==0)
			return result("FAILED", $curr."/hostid",   i18n("Invalid IP address."));
		/* Check duplicate MAC/Host ID */
		$i=1;
		while ($i<$InDeX)
		{
			$op2 = query($b."/staticleases/entry:".$i."/macaddr");
			if (tolower($macaddr)==tolower($op2))
				return result("FAILED",$curr."/macaddr", i18n("Duplicated MAC address."));
			$op2 = query($b."/staticleases/entry:".$i."/hostid");
			if ($hostid==$op2)
				return result("FAILED",$curr."/hostid", i18n("Duplicated IP address."));
			$i++;
		}
		/* make sure the MAC address is in the lower case. */
		set("macaddr", tolower($macaddr));
	}
	set("staticleases/seqno", $seqno);
	return result("OK","","");
}

function verify_dhcps4($path)
{
	anchor($path);
	$start = query("start");
	$end   = query("end");
	$domain = query("domain");
	$ltime	= query("leasetime");
	$router = query("router");

	TRACE_debug("FATLADY: DHCPS4.INF: ".$entry);
	TRACE_debug("FATLADY: DHCPS4.INF:start    = ".$start);
	TRACE_debug("FATLADY: DHCPS4.INF:end      = ".$end);
	TRACE_debug("FATLADY: DHCPS4.INF:domain   = ".$domain);
	TRACE_debug("FATLADY: DHCPS4.INF:leasetime= ".$ltime);
	TRACE_debug("FATLADY: DHCPS4.INF:router   = ".$router);

	/* check pool range */
	if (isdigit($start)==0)
		return result("FAILED", $path."/start",
				i18n("The range of the DHCP lease pool should be a decimal number."));

	if (isdigit($end)==0)
		return result("FAILED", $path."/end",
				i18n("The range of the DHCP lease pool should be a decimal number."));

	if ($start > $end)
		return result("FAILED", $path."/start",
				i18n("The start value of the DHCP lease pool should be equal to or less than the end value."));

	if ($start==0)
		return result("FAILED", $path."/start", i18n("The DHCP lease pool can not be zero."));

	/* Check domain name */
	if ($domain!="" && isdomain($domain)=="0")
		return result("FAILED", $path."/domain", i18n("Invalid domain name."));

	/* Check lease time */
	if (isdigit($ltime)==0)
		return result("FAILED", $path."/leasetime",
				i18n("The lease time should be a decimal number."));

	if ($ltime < 1)
		return result("FAILED", $path."/leasetime",
				i18n("The lease time should be greater than 1 minute."));

	/* Check router */
	if ($router!="" && ipv4networkid($router,32)=="")
		return result("FAILED", $path."/router", i18n("The router address is invalid."));

	/* check DNS */
	$cnt = query("dns/count");
	foreach("dns/entry")
	{
		if ($InDeX>$cnt) break;
		if (ipv4networkid($VaLuE,32)=="")
			return result("FAILED", $path."/dns/entry:".$InDeX, i18n("The DNS address is invalid."));
	}
	/* check WINS */
	$cnt = query("wins/count");
	foreach("wins/entry")
	{
		if ($InDeX>$cnt) break;
		if (ipv4networkid($VaLuE, 32)=="")
			return result("FAILED", $path."/wins/entry:".$InDeX, i18n("The WINS address invalid."));
	}

	/* check static lease(s) */
	return verify_staticleases($path);
}

//////////////////////////////////////////////////////////////////////////////

/* The default max value. */
$max = query("/dhcps4/max");
$count = query($FATLADY_prefix."/dhcps4/count");
$seqno = query($FATLADY_prefix."/dhcps4/seqno");
if ($count>$max) $count=$max;
foreach($FATLADY_prefix."/dhcps4/entry")
{
	if ($InDeX > $count) break;
	$entry = $FATLADY_prefix."/dhcps4/entry:".$InDeX;
	$ret = verify_dhcps4($entry);
	if ($ret!="OK") break;
	if (query($entry."/uid")=="")
	{
		set($entry."/uid", "DHCPS4-".$seqno);
		$seqno++;
		set($FATLADY_prefix."/dhcps4/seqno", $seqno);
	}
}
if ($ret=="OK") set($FATLADY_prefix."/valid", "1");
?>
