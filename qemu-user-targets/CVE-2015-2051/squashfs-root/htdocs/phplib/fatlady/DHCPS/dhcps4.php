<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/phyinf.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function check_dhcp_config($path, $inetp)
{
	/* get the info of related IP address and subnet mask */
	$_SVC	= XNODE_get_var("SERVICE_NAME");
	$_UID	= cut($_SVC, 1, ".");
	$ip		= query($inetp."/ipv4/ipaddr");
	$mask	= query($inetp."/ipv4/mask");

	anchor($path);
	$start_ip = query("start");
	$end_ip = query("end");
	TRACE_debug("FATLADY: DHCPS4: ip = ".$ip);
	TRACE_debug("FATLADY: DHCPS4: mask = ".$mask);
	TRACE_debug("FATLADY: DHCPS4: lease pool from ".$start_ip." to ".$end_ip);
	/* check the range of lease pool is decimal */
	if (isdigit($start_ip) == 0)
	{
		set_result("FAILED",$path."/start",i18n("The input range of host id is not digital."));
		return;
	}
	if (isdigit($end_ip) == 0)
	{
		set_result("FAILED",$path."/end",i18n("The input range of host id is not digital."));
		return;
	}

	/* check start ip is smaller then end ip */
	if ($start_ip > $end_ip)
	{
		set_result("FAILED",$path."/start",i18n("The start host id of lease pool should be smaller than the end host id."));
		return;
	}

	/* check lease range is not out of the boundary */
	if ($start_ip == 0)
	{
		set_result("FAILED",$path."/start",i18n("The input range of the host ID is out of the boundary range."));
		return;
	}
	if ($end_ip >= ipv4maxhost($mask))
	{
		set_result("FAILED",$path."/end",i18n("The input range of the host ID is out of the boundary range."));
		return;
	}

	/* check lease range is not include the LAN IP */
	$lan_id = ipv4hostid($ip, $mask);
	if ($start_ip<=$lan_id && $end_ip>=$lan_id)
	{
		set_result("FAILED",$path."/start",i18n("The input range of host id is include the LAN IP address."));
		return;
	}

	/* check domain name */
	$domain = query("domain");
	if ($domain!="" && isdomain($domain)=="0")
	{
		set_result("FAILED",$path."/domain",i18n("Invalid domain name."));
		return;
	}

	/* check lease time */
	if (isdigit(query("leasetime"))==0)
	{
		set_result("FAILED",$path."/leasetime",i18n("The input lease time is not digital."));
		return;
	}

    /* check lease time */
    if (query("leasetime") < 1)
	{
		set_result("FAILED",$path."/leasetime",i18n("The input lease time could not be smaller than 1 minute ."));
		return;
	}


	/* check router */
	$router = query("router");
	if (ipv4networkid($router,32)=="" && $router!="")
	{
		set_result("FAILED",$path."/router",i18n("The input router address is invalid."));
		return;
	}

	/* check dns server */
	$cnt = query("dns/count");
	$i = 0;
	while ($i < $cnt)
	{
		$i++;
		if (ipv4networkid(query("dns/entry:".$i),32)=="")
		{
			set_result("FAILED",$path."/dns/entry:".$i,i18n("The input DNS server address is invalid."));
			return;
		}
	}

	/* check wins server */
	$cnt = query("wins/count");
	$i = 0;
	while ($i < $cnt)
	{
		$i++;
		if (ipv4networkid(query("wins/entry:".$i),32)=="")
		{
			set_result("FAILED",$path."/wins/entry:".$i,i18n("The input WINS server address is invalid."));
			return;
		}
	}

	/* check staticleases */
	$_LANID   = cut($_UID, 1, "-");
	$p = XNODE_get_var("FATLADY_DHCPS_PATH");
	$dhcpp = XNODE_getpathbytarget($p."/dhcps4", "entry", "uid", "DHCPS4-".$_LANID, 0);

	$seqno = query($dhcpp."/staticleases/seqno");
	$p = $dhcpp."/staticleases/entry";
	$cnt = query($dhcpp."/staticleases/count");
	$i = 0;

	foreach ($p)
	{
		if ($InDeX > $cnt) break;
		$uid = query("uid");
		/* Check empty UID */
		if ($uid == "")
		{
			$uid = "STIP-".$seqno;
			set("uid", $uid);
			$seqno++;
		}
		/* Check duplicated UID */
		if ($$uid == "1")
		{
			set_result("FAILED", $p.":".$InDeX."/uid", "Duplicated UID - ".$uid);
	       	return;
    	}
    	$$uid = "1";

		/* Check empty hostname*/
   		$hostname = query("hostname");
	    if ($hostname == "" || isdomain($hostname)=="0")
		{
	        set_result("FAILED", $p.":".$InDeX."/hostname", i18n("Invalid host name."));
    		return;
    	}

		$mac = query("macaddr");
	    if ($mac == "")
		{
	        set_result("FAILED", $p.":".$InDeX."/macaddr", i18n("No MAC address value."));
			return;
	   	}

	    if (PHYINF_validmacaddr($mac) != 1)
	    {
	        set_result("FAILED", $p.":".$InDeX."/macaddr", i18n("Invalid MAC address value."));
	        return;
		}

		/* Check duplicate mac */
		$i2 = $InDeX;
		//TRACE_debug("DHCPS4[".$i2."]");
		while ($i2 < $cnt)
		{
			$i2++;
			$m2 = query($p.":".$i2."/macaddr");
			//TRACE_debug("DHCPS4:".$i2."-".$m2);
			if (tolower($mac) == tolower($m2))
			{
				set_result("FAILED", $p.":".$InDeX."/macaddr", i18n("Duplicate MAC addresses."));
				return;
			}
		}

		/* check hostid is not out of the boundary */
		$hostid = query("hostid");
	    if ($hostid == 0 || $hostid >= ipv4maxhost($mask) || $hostid == $lan_id)
	    {
			set_result("FAILED", $p.":".$InDeX."/hostid", i18n("Invalid IP address."));
	        return;
	    }

		/* repeat check */
		$rlt = 0;
	    $i = $InDeX + 1;
   		while ($i <= $cnt)
    	{
       		if (tolower($mac) == query($dhcpp."/staticleases/entry:".$i."/macaddr"))
        	{
           		set_result("FAILED", $dhcpp."/staticleases/entry:".$i."/macaddr", i18n("Duplicate MAC addresses."));
            	$rlt = "-1";
            	break;
        	}

			if ($hostid == query($dhcpp."/staticleases/entry:".$i."/hostid"))
        	{
           		set_result("FAILED", $dhcpp."/staticleases/entry:".$i."/hostid", i18n("Duplicate IP addresses."));
            	$rlt = "-1";
            	break;
        	}
        	$i++;
    	}
    	if ($rlt != "0") return;
    	set($p.":".$InDeX."/macaddr", tolower($mac));
	}
	set($dhcpp."/staticleases/seqno", $seqno);

	set_result("OK", "", "");
}

set_result("FAILED","","");

$path = XNODE_get_var("FATLADY_DHCPS_PATH");
if ($path=="")
	set_result("FAILED","","No XML document");
else
{
	$dhcp = query($path."/inf/dhcps4");
	if ($dhcp!="")
	{
		$dhcpentry = XNODE_getpathbytarget($path."/dhcps4", "entry", "uid", $dhcp, 0);
		if ($dhcpentry!="") check_dhcp_config($dhcpentry, $path."/inet/entry");
	}
	else
	{
		set_result("OK","","");
	}
}
?>
