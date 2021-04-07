<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

function check_filter($path)
{
	$cnt	= query($path."/count");
	$max	= query($path."/max");
	$num	= query($path."/entry#");
	$seqnop	= $path."/seqno";
	$seqno	= query($seqnop);
	if ($seqno == "")	$seqno = 1;
	if ($cnt > $max)	$cnt = $max;
	/* delete the exceeded FILTER entries */
	while ($num > $cnt)
	{
		del($path."/entry:".$num);
		$num = query($path."/entry#");
	}

	foreach ($path."/entry")
	{
		$uid = query("uid");
		if ($uid == "")
		{
			$uid = "FILTER-".$seqno;
			$seqno++;
			set("uid",	$uid);
			set($seqnop,$seqno);
		}
		if ($$uid=="1")
			return set_result(
					"FAILED",
					$path."/entry:".$InDeX."/uid",
					"Duplicated UID - ".$uid
					);
		$$uid = 1;

		if (query("enable")!="1") set("enable", "0");
		$domain = query("string");
		if ($domain == "")
			return set_result(
					"FAILED",
					$path."/entry:".$InDeX."/string",
					i18n("Please input the domain name.")
					);

		if (charcodeat($domain, 0)=='.') $test = "www".$domain;
		else $test = $domain;
		if (isdomain($test) != 1)
			return set_result(
					"FAILED",
					$path."/entry:".$InDeX."/string",
					i18n("Invalid domain name.")
					);

		$i = 1;
		while ($i < $InDeX)
		{
			if (query($path."/entry:".$i."/string")==$domain)
				return set_result(
					"FAILED",
					$path."/entry:".$InDeX."/string",
					i18n("Duplicated domain name")
					);
			$i++;
		}
	}

	return "OK";
}

function check_dns4_entry($base)
{
	$max	= query($base."/max");
	$cnt	= query($base."/count");
	$num	= query($base."/entry#");
	$seqnop	= $base."/seqno";
	$seqno	= query($seqnop);
	if ($seqno == "")	$seqno = 1;
	if ($cnt > $max)	$cnt = $max;
	/* delete the exceeded SVR entries */
	while ($num > $cnt)
	{
		del($base."/entry:".$num);
		$num = query($base."/entry#");
	}

	/* walk through all SVR-# */
	foreach ($base."/entry")
	{
		/* Check the UID */
		$uid = query("uid");
		/* fill in the uid for new dns server.*/
		if ($uid=="")
		{
			$uid = "SVR-".$seqno;
			$seqno++;
			set("uid",	$uid);
			set($seqnop,$seqno);
		}
		if ($$uid == 1)
			return set_result(
					"FAILED",
					$base."/entry:".$InDeX."/uid",
					"Duplicated UID - ".$uid
					);

		$$uid = 1;
		/* Check type */
		$type = query("type");
		if ($type == "inf")
		{
			$inf = query("inf");
			$infp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
			if ($infp == "")
				return set_result(
					"FAILED",
					$base."/entry:".$InDeX."/inf",
					"Invalid interface - ".$inf
					);
		}
		else if ($type=="static")
		{
			$ipaddr = query("ipaddr");
			if (INET_validv4addr($ipaddr)==0)
				return set_result(
					"FAILED",
					$base."/entry:".$InDeX."/ipaddr",
					"Invalid IP address - ".$ipaddr
					);
		}
		else if ($type!="local")
		{
			return set_result(
					"FAILED",
					$base."/entry:".$InDeX."/type",
					"Unsupported type - ".$type
					);
		}

		/* Check filter */
		if (check_filter($base."/entry:".$InDeX."/filter")!="OK")
			return "FAILED";

	}

	return "OK";
}

function check_dns4($path, $uid)
{
	$base = XNODE_getpathbytarget($path."/dns4", "entry", "uid", $uid, 0);
	if ($base == "")
		return set_result(
					"FAILED",
					$path."/inf/dns4",
					"Invalid profile - ".$uid
					);
	return check_dns4_entry($base);
}

function fatlady_dns4($prefix, $inf)
{	
	set_result("FAILED", "", "");

	$infp = XNODE_getpathbytarget($prefix, "inf", "uid", $inf, 0);
	if ($infp == "") return;
	$ProfileUID = query($infp."/dns4");

	if (check_dns4($prefix, $ProfileUID)=="OK")
	{
		set($prefix."/valid", 1);
		set_result("OK","","");
	}
}
function fatlady_gothrough_all_dns4($prefix)
{
	set_result("FAILED", "", "");
	$max	= query("/dns4/max");
	$count	= query($prefix."/dns4/count");
	$seqno	= query($prefix."/dns4/seqno");
	$num	= query($prefix."/dns4/entry#");

	TRACE_debug("DNS4.INF: max=".$max.", count=".$count.", seqno=".$seqno.", number=".$num);
	if ($count>$max) $count=$max;
	/* delete the exceeded DNS entries */
	while ($num>$count) {del($prefix."/dns4/entry:".$num); $num--;}
	foreach ($prefix."/dns4/entry")
	{
		if ($InDeX > $count) break;
		$ret = check_dns4_entry($prefix."/dns4/entry:".$InDeX);
		if ($ret!="OK") break;
		if (query("uid")=="")
		{
			set("uid", "DNS4-".$seqno);
			$seqno++;
			set($prefix."/dns4/seqno", $seqno);
		}
	}
	if ($ret=="OK")
	{
		set($prefix."/valid", 1);
		set_result("OK","","");
	}
	return $ret;
}
?>
