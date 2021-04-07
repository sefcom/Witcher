<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/inet.php";

function result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
	return $result;
}

function isvalid_port($port)
{
	if (isdigit($port)==0) return "NO";
	if ($port < 1 || $port>65535) return "NO";
	return "YES";
}

/* The portlist format is "##-##,##-##,##,##-##" */
function isvalid_portlist($port)
{
	$cnt = cut_count($port, ',');
	//TRACE_debug("isvalid_portlist(".$port.") = ".$cnt);
	$i = 0;
	while ($i<$cnt)
	{
		$token = cut($port, $i, ',');
		$portcnt = cut_count($token, '-');
		if ($portcnt < 1 || $portcnt > 2)	return "NO";

		$start = cut($token, 0, '-');
		if (isvalid_port($start)!="YES")	return "NO";
		if ($portcnt > 1)
		{
			$end = cut($token, 1, '-');
			if (isvalid_port($end)!="YES")	return "NO";
		}
		$i++;
	}
	return "YES";
}

/*******************************************************************/

function verify_portforward($path, $max)
{
	$count = query($path."/count"); if ($count=="") $count=0;
	$seqno = query($path."/seqno"); if ($seqno=="") $seqno=1;
	if ($count > $max) $count = $max;
	$count+=0; $seqno+=0;

	TRACE_debug("FATLADY: NAT-PFWD: max=".$max.", count=".$count.", seqno=".$seqno.", path=".$path);

	/* Delete the extra entries. */
	$num = query($path."/entry#");
	while ($num>$count) { del($path."/entry:".$num); $num--; }

	/* verify each entry */
	set($path."/count", $count);
	set($path."/seqno", $seqno);
	foreach ($path."/entry")
	{
		if ($InDex>$count) break;

		/* The current entry path. */
		$entry = $path."/entry:".$InDeX;

		/* Check empty UID */
		$uid = query("uid");
		if ($uid == "")
		{
			$uid = "PFWD-".$seqno;
			set("uid", $uid);
			$seqno++;
			set($path."/seqno", $seqno);
		}
		/* Check duplicated UID */
		if ($$uid == "1") return result("FAILED", $entry."/uid", "Duplicated UID - ".$uid);
		$$uid = "1";

		/* We are not checking the description here, let it be optional. */

		/* Check protocol */
		$val = query("protocol");
		if ($val!="TCP" && $val!="UDP" && $val!="TCP+UDP")
			return result("FAILED", $entry."/protocol", i18n("The protocol is invalid."));

		/* Check external starting port*/
		$start = query("external/start");
		if ($start=="")
			return result("FAILED", $entry."/external/start", i18n("Please input the public port."));
		if (isvalid_port($start)!="YES")
			return result("FAILED", $entry."/external/start",
				i18n("The value of the public port is invalid."));
		set("external/start", $start); /* validate external setting */

		/* Check external ending port */
		$end = query("external/end");
		if ($end!="")
		{
			if (isvalid_port($end)!="YES")
				return result("FAILED", $entry."/external/end",
					i18n("The value of the public port range is invalid."));
			if ($start > $end)
				return result("FAILED", $entry."/external/start",
					i18n("The value of the public port is invalid.")." ".
					i18n("The ending port should be greater than the beginning port."));
			set("external/end", $end); /* validate external setting */
		}

		/* Check internal */
		$inf = query("internal/inf");
		if ($inf=="")
			return result("FAILED", $entry."/internal/inf", i18n("No internal domain specified."));
		$hostid = query("internal/hostid");
		if ($hostid=="" || isdigit($hostid)==0)
			return result("FAILED", $entry."/internal/hostid", i18n("The internal host is invalid."));
		/* Remove the checking. The NAT/DMZ setting can be applied
		 * to any classes of the v4 IP address, there should be no restriction here.
		 * by David Hsieh <david_hsieh@alphanetworks.com>
		$inet = INF_getinfinfo($inf, "inet");
		if ($inet=="")
			return result("FAILED", $entry."/internal/inf", i18n("Illegal internal domain."));
		$mask = INET_getinetinfo($inet, "ipv4/mask");
		if ($hostid<1 || $hostid>=ipv4maxhost($mask))
			return result("FAILED", $entry."/internal/hostid", i18n("The host ID is out of the boundary."));
		*/

		$start = query("internal/start");
		if ($start!="")
		{
			$start+=0;
			if (isvalid_port($start)!="YES")
				return result("FAILED", $entry."/internal/start",
							i18n("The value of the private port is invalid."));
			if (query("external/end")!="")
			{
				$end = $start + query("external/end") - query("external/start");
				if ($start<1 || $start>65535 || $end>65535)
					return result("FAILED", $entry."/internal/start",
						i18n("The range of the private port is invalid."));
			}
			set("internal/start", $start);
		}
	}
	return "OK";
}

function verify_porttrigger($path, $max)
{
	$count = query($path."/count"); if ($count=="") $count=0;
	$seqno = query($path."/seqno"); if ($seqno=="") $seqno=1;
	if ($count > $max) $count = $max;
	$count+=0; $seqno+=0;

	TRACE_debug("FATLADY: NAT-PORTT: max=".$max.", count=".$count.", seqno=".$seqno.", path=".$path);

	/* Delete the extra entries. */
	$num = query($path."/entry#");
	while ($num>$count) { del($path."/entry:".$num); $num--; }

	/* verify each entry */
	set($path."/count", $count);
	set($path."/seqno", $seqno);
	foreach ($path."/entry")
	{
		if ($InDeX>$count) break;

		/* The current entry path. */
		$entry = $path."/entry:".$InDeX;

		/* Check empty UID */
		$uid = query("uid");
		if ($uid == "")
		{
			$uid = "PORTT-".$seqno;
			set("uid", $uid);
			$seqno++;
			set($path."/seqno", $seqno);
		}
		/* Check duplicated UID */
		if ($$uid == "1") return result("FAILED", $entry."/uid", "Duplicated UID - ".$uid);
		$$uid = "1";

		/* We are not checking the description here, let it be optional. */

		/* Check the trigger protocol/ports */
		$val = query("trigger/protocol");
		if ($val!="TCP" && $val!="UDP" && $val!="TCP+UDP")
			return result("FAILED", $entry."/trigger/protocol", i18n("The application protocol is invalid."));

		/* Check the trigger starting port. */
		$start = query("trigger/start");
		if ($start=="")
			return result("FAILED", $entry."/trigger/start", i18n("Please input the application's port number."));
		if (isvalid_port($start)!="YES")
			return result("FAILED", $entry."/trigger/start",
				i18n("The value of the application port is invalid."));
		set("trigger/start", $start); /* validate the port setting */

		/* Check the trigger ending port */
		$end = query("trigger/end");
		if ($end!="")
		{
			if (isvalid_port($end)!="YES")
				return result("FAILED", $entry."/trigger/end",
					i18n("The value of the application port range is invalid."));
			if ($start > $end)
				return result("FAILED", $entry."/trigger/start",
					i18n("The value of the application port is invalid.")." ".
					i18n("The ending port should be greater than the beginning port."));
			set("trigger/end", $end); /* validate the port setting */
		}

		/* Check external */
		$val = query("external/protocol");
		if ($val!="TCP" && $val!="UDP" && $val!="TCP+UDP")
			return result("FAILED", $entry."/external/protocol",
				i18n("The external protocol is invalid."));

		$port = query("external/portlist");
		if (isvalid_portlist($port)!="YES")
			return result("FAILED", $entry."/external/portlist",
				i18n("The external port list is invalid."));
	}
	return "OK";
}

function verify_dmz($path)
{
	$enable = query($path."/enable");
	if ($enable=="1")
	{
		anchor($path);
		$inf	= query("inf");
		$hostid	= query("hostid");
		
		if ($inf=="")
			return result("FAILED", $entry."/inf", i18n("No internal domain specified for DMZ."));
		if ($hostid=="" || isdigit($hostid)==0)
			return result("FAILED", $entry."/hostid", i18n("The internal DMZ host is invalid."));

		/* Remove the checking. The NAT/DMZ setting can be applied
		 * to any classes of the v4 IP address, there should be no restriction here.
		 * by David Hsieh <david_hsieh@alphanetworks.com>
		$inet = INF_getinfinfo($inf, "inet");
		if ($inet=="") return result("FAILED", $entry."/inf", i18n("Illegal internal domain."));
		$mask = INET_getinetinfo($inet, "ipv4/mask");
		if ($hostid<1 || $hostid>=ipv4maxhost($mask))
			return result("FAILED", $entry."/hostid", i18n("The host ID is out of the boundary."));
		*/
	}
	else set($path."/enable", "0");
	return "OK";
}

function verify_nat($path, $max)
{
	$count = query($path."/count"); if ($count=="") $count=0;
	$seqno = query($path."/seqno");
	if ($seqno=="") { set($path."/seqno", "1"); $seqno=1; }
	if ($count > $max) $count = $max;

	TRACE_debug("FATLADY: NAT: max=".$max.", count=".$count.", seqno=".$seqno);

	/* Delete the extra entries. */
	$num = query($path."/entry#");
	while ($num>$count) { del($path."/entry:".$num); $num--; }

	/* verify each entry */
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
			$uid = "NAT-".$seqno;
			set("uid", $uid);
			$seqno++;
			set($path."/seqno", $seqno);
		}
		/* Check duplicated UID */
		if ($$uid == "1")
			return result("FAILED", $entry."/uid", "Duplicated UID - ".$uid);

		$$uid = "1";

		/* Check NAT type, default is PORTRESTRICTED */
		$type = query("type");
		if ($type != "MASQUERADE" && $type != "SYMMETRIC" &&
			$type != "PORTRESTRICTED" && $type != "RESTRICTED" && $type != "FULL")
		{
			$type = "PORTRESTRICTED";
			set("type", $type);
		}

		/* Check Port Forwarding */
		$max = query($entry."/portforward/max");
		if ($max=="" || $max>128) { $max=128; set($entry."/portforward/max", $max); }
		$ret = verify_portforward($entry."/portforward", $max);
		if ($ret!="OK") return $ret;

		/* Check Virtual Server */
		$max = query($entry."/virtualserver/max");
		if ($max=="" || $max>128) { $max=128; set($entry."/virtualserver/max", $max); }
		$ret = verify_portforward($entry."/virtualserver", $max);
		if ($ret!="OK") return $ret;

		/* Check Port Trigger */
		$max = query($entry."/porttrigger/max");
		if ($max=="" || $max>32) { $max=32; set($entry."/porttrigger/max", $max); }
		$ret = verify_porttrigger($entry."/porttrigger", $max);
		if ($ret!="OK") return $ret;

		/* Check DMZ */
		$ret = verify_dmz($entry."/dmz");
		if ($ret!="OK") return $ret;
	}
	return "OK";
}

/*******************************************************************/
/* Main flow */
$max = query("/nat/max"); if ($max=="") $max=1;
if (verify_nat($FATLADY_prefix."/nat", $max)=="OK")
{
	set($FATLADY_prefix."/valid", "1");
	result("OK", "", "");
}
?>
