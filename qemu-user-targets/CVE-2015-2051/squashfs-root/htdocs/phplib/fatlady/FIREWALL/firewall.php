<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inf.php";

function seterr($err, $node)
{
    $_GLOBALS["FATLADY_result"] = "FAILED";
    $_GLOBALS["FATLADY_node"]   = $node;
    $_GLOBALS["FATLADY_message"]= $err;
	return "FAILED";
}
function setok()
{
    $_GLOBALS["FATLADY_result"] = "OK";
    $_GLOBALS["FATLADY_node"]   = "";
    $_GLOBALS["FATLADY_message"]= "";
	return "OK";
}

function verify_schedule($uid)
{
	if ($uid == "") return "";
	$path = XNODE_getpathbytarget("/schedule", "entry", "uid", $uid, 0);
	if ($path!="") return "";
	return i18n("Incorrect schedule setting.");
}

function verify_interface($uid)
{
	if ($uid=="") return "";
	$path = XNODE_getpathbytarget("", "inf", "uid", $uid, 0);
	if ($path!="") return "";
	return i18n("Incorrect interface !")." (".$uid.")";
}

function verify_ip_range($ipaddr1, $ipaddr2, $inf)
{
	if ($ipaddr1=="" && $ipaddr2=="")	return "";
	if($ipaddr1!="")
	{
		if (INET_validv4addr($ipaddr1)!=1)	return i18n("Invalid format of the start IP address.");
	}
	if ($ipaddr2!="")
	{
		if (INET_validv4addr($ipaddr2)!=1)	return i18n("Invalid format of the end IP address.");
		/* We only allow the range for 255 hosts. */
		$hostid1 = ipv4hostid($ipaddr1, 0);
		$hostid2 = ipv4hostid($ipaddr2, 0);
		$delta = $hostid2-$hostid1;
		if ($delta < 0)				return i18n("The end IP address should be greater than the start address.");
		if ($delta > 255)				return i18n("The IP address range is too large.");
	}
	$inf0 = cut($inf, 0, "-");
	if ($inf0 == "LAN")
	{
		$_INET  = INF_getinfinfo($inf, "inet");
		$lanip  = INET_getinetinfo($_INET, "ipv4/ipaddr");
		$lanmask   = INET_getinetinfo($_INET, "ipv4/mask");
		if($ipaddr1==$lanip)	return i18n("The start IP address can't be the same as device IP.");
		if(INET_validv4host($ipaddr1, $lanmask)==0)	return i18n("The start IP address is invalid.");
		if(INET_validv4network($ipaddr1, $lanip, $lanmask)==0) 
			return i18n("The start IP address and router address should be in the same network subnet.");
		if($ipaddr2!="")
		{
			if($ipaddr2==$lanip)	return i18n("The end IP address can't be the same as device IP.");
			if(INET_validv4host($ipaddr2, $lanmask)==0)	return i18n("The end IP address is invalid.");
			if(INET_validv4network($ipaddr2, $lanip, $lanmask)==0) 
				return i18n("The end IP address and router address should be in the same network subnet.");
		}
	}
	return "";
}

function verify_port_range($port1, $port2)
{
	if ($port1=="" && $port2=="")	return "";
	if ($port1=="")					return i18n("Require start port for port range.");
	if (isdigit($port1)!="1")		return i18n("Invalid start port value.");
	if ($port1<1 || $port1>65535)	return i18n("The port is out of the boundary.");
	if ($port2!="")
	{
		if (isdigit($port2)!="1")	return i18n("Invalid end port value.");
		if ($port2 > 65535)			return i18n("The end port value is too large.");
		if ($port2 < $port1)		return i18n("The end port value should be greater than the start port value.");
	}
	return "";
}

function verify_setting($path)
{
	/* Delete the extra entries. */
	$cnt = query($path."/count");
	$num = query($path."/entry#");
	while ($num > $cnt)
	{
		del($path."/entry:".$num);
		$num = query($path."/entry#");
	}

	setok();

	/* Firewall default policy */
	$policy = query($path."/policy");
	$seqno = query($path."/seqno");
	if ($policy!="DROP" && $policy!="ACCEPT") set($path."/policy", "DISABLE");

	/* Walk through each entry */
	foreach ($path."/entry")
	{
		/* 1. Check the UID of this entry, it should not be empty, and must be unigue. */
		$uid = query("uid");
		if ($uid == "") { $uid = "FWL-".$seqno; set("uid", $uid); $seqno++; }
		if ($$uid=="1") { seterr("Duplicated UID - ".$uid, $path."/entry:".$InDeX."/uid" ); break; }
		/* 2. enable should be '1' or '0' */
		if (query("enable")!="1") set("enable", "0");
		/* 3. policy should be 'DROP' or 'ACCEPT' */
		if (query("policy")!="ACCEPT") set("policy", "DROP");
		/* 4. schedule */
		$err = verify_schedule(query("schedule"));
		if ($err!="")	{ seterr($err, $path."/entry:".$InDeX."/schedule"); break; }
		/* 5. protocol */
		$prot = query("protocol");
		if ($prot!="ALL" && $prot!="TCP" && $prot!="UDP" && $prot!="TCP+UDP" && $prot!="ICMP")
		{
			seterr(i18n("Unsupported protocol type."), $path."/entry:".$InDeX."/protocol");
			break;
		}

		$srcinf = query("src/inf");
		$srcip1 = query("src/host/start");
		$srcip2 = query("src/host/end");
		$dstinf = query("dst/inf");
		$dstip1	= query("dst/host/start");
		$dstip2 = query("dst/host/end");

		/* 6. source interface */
		$err = verify_interface($srcinf);
		if ($err!="")	{ seterr($err, $path."/entry:".$InDeX."/src/inf"); break; }
		/* 7. destination interface */
		$err = verify_interface($dstinf);
		if ($err!="")	{ seterr($err, $path."/entry:".$InDeX."/dst/inf"); break; }
		/* 7-1. source & destination interface should not be the same. */
		if ($srcinf!="" && $srcinf==$dstinf)
		{
			seterr(
				i18n("The source and destination interfaces should not be the same."),
				$path."/entry:".$InDeX."/src/inf"
				);
			break;
		}
		/* 8. source IP */
		$err = verify_ip_range($srcip1, $srcip2, $srcinf);
		if ($err!="")
		{
			$err = i18n("Incorrect source IP address.")." ".$err;
			seterr($err, $path."/entry:".$InDeX."/src/host");
			break;
		}
		/* 9. destination IP */
		$err = verify_ip_range($dstip1, $dstip2, $dstinf);
		if ($err!="")
		{
			$err = i18n("Incorrect destination IP address.")." ".$err;
			seterr($err, $path."/entry:".$InDeX."/dst/host");
			break;
		}
		/* 9-1. The source & destination IP address should not be the same. */
		if ($srcip1!="" && $srcip1==$dstip1)
		{
			seterr(
				i18n("The source and destination interfaces should not be the same."),
				$path."/entry:".$InDeX."/src/host"
				);
			break;

		}
		/* 10. destination ports */
		if ($prot=="TCP" || $prot=="UDP" || $prot=="TCP+UDP")
		{
			$err = verify_port_range(query("dst/port/start"), query("dst/port/end"));
			if ($err!="") { seterr($err, $path."/entry:".$InDeX."/dst/port"); break; }
		}
	}
	set($path."/seqno", $seqno);
    return $_GLOBALS["FATLADY_result"];
}
?>
