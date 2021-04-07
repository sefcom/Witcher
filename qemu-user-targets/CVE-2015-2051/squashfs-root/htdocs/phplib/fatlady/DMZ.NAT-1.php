<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inf.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function check_dmz_setting($path, $addrtype, $lan_ip, $mask)
{
	if (query($path."/enable") == "1")
	{
		anchor($path);
		$hostid = query("hostid");
		if ($hostid == "")
		{
			set_result("FAILED", $path."/hostid", i18n("The DMZ IP Address cannot be empty."));
			return "FAILED";
		}
		if ($hostid <=0)
		{
			set_result("FAILED", $path."/hostid", i18n("DMZ IP Address is not a valid IP Address."));
			return "FAILED";
		}
		if ($addrtype == "ipv4")
		{
			$dmzip = ipv4ip($lan_ip, $mask, $hostid);
			if (INET_validv4host($dmzip, $mask)==0)
			{
				set_result("FAILED",$path."/hostid",i18n("DMZ IP Address is not a valid IP Address."));
				return "FAILED";
			}
		}
	}
	else set($path."/enable", "0");
	return "OK";
}

$infp = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0);
$addrtype = query($infp."/inet/addrtype");
if ($addrtype == "ipv4")
{
	$lan_ip = query($infp."/inet/ipv4/ipaddr");
	$mask = query($infp."/inet/ipv4/mask");
}
//TRACE_debug("[Fatlady] infp:".$infp." lan ip:".$lan_ip." mask:".$mask);

set_result("FAILED","","");
if (check_dmz_setting($FATLADY_prefix."/nat/entry:".$InDeX."/dmz", $addrtype, $lan_ip, $mask)=="OK")
{
	set($FATLADY_prefix."/valid", "1");
	set_result("OK", "", "");
}

?>
