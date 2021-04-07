<? /* vi: set sw=4 ts=4: */
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be breaked for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

function verify_setting($path, $max)
{
	$count = query($path."/count"); if ($count=="") $count=0;
	$seqno = query($path."/seqno");
	if ($count > $max) $count = $max;

	TRACE_debug("FATLADY: ROUTE.DESTNET: max=".$max.", count=".$count.", seqno=".$seqno);

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
			$uid = "DRT-".$seqno;
			set("uid", $uid);
			$seqno++;
			set($path."/seqno", $seqno);
		}
		/* Check duplicated UID */
		if ($$uid == "1")
			return set_result("FAILED", $entry."/uid",
						"Duplicated UID - ".$uid);

		$$uid = "1";

		$mask = query("mask");
		if (isdigit($mask)!="1" || $mask > 30 || $mask < 0)
			return set_result("FAILED", $entry."/mask",
						i18n("Invalid subnet mask"));

		$netid = query("network");
		if (ipv4networkid($netid, $mask) != $netid)
			return set_result("FAILED", $entry."/network",
						i18n("Invalid network ID"));

		$inf = query("inf");
		$infp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
		if ($infp=="")
			return set_result("FAILED", $entry."/inf",
						i18n("Invalid interface."));

		$i = 1;
		while ($i < $InDeX)
		{
			$n = query($path."/entry:".$i."/network");
			$m = query($path."/entry:".$i."/mask");
			if (ipv4networkid($n, $m) == $netid)
				return set_result("FAILED", $path."/entry:".$i."/network",
						i18n("Duplicated network ID"));
			$i++;
		}
	}
	return "OK";
}

$max = query("/route/destination/max"); if ($max=="") $max=32;
if (verify_setting($FATLADY_prefix."/route/destination", $max) == "OK")
{
	set($FATLADY_prefix."/valid", "1");
	set_result("OK","","");
}

?>
