<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inf.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}


function check_dhcp_config($path, $inetp)
{
	anchor($path);
	$mode = query("mode");
	$network = query("network");
	$prefix = query("prefix");
	$start = query("start");
	$count = query("count");
	$domain = query("domain");
	TRACE_debug("FATLADY: DHCPS6: ".$path);
	TRACE_debug("FATLADY: DHCPS6: mode = ".$mode);
	TRACE_debug("FATLADY: DHCPS6: network = ".$network);
	TRACE_debug("FATLADY: DHCPS6: prefix = ".$prefix);
	TRACE_debug("FATLADY: DHCPS6: start = ".$start);
	TRACE_debug("FATLADY: DHCPS6: count = ".$count);
	TRACE_debug("FATLADY: DHCPS6: domain = ".$domain);

	/* check network */
	if ($network!="")
	{
		if (ipv6checkip($network)=="")
			return result("FAILED", $path."/network",
				i18n("Invalid Network ID."));

		/* check prefix */
		if (isdigit($prefix)==0)
			return result("FAILED", $path."/prefix",
					i18n("The prefix should be a decimal number."));
	}

	/* check pool range */
	if (ipv6checkip($start)=="")
		return result("FAILED", $path."/start",
				i18n("Invalid start address."));

	if (isdigit($count)==0 || $count==0)
		return result("FAILED", $path."/count",
				i18n("The range of the DHCP lease pool should be a decimal number."));

	if ($count > 256)
		return result("FAILED", $path."/count",
				i18n("The count limit is 256."));

	/* Check domain name */
	if ($domain!="" && isdomain($domain)=="0")
		return result("FAILED", $path."/domain", i18n("Invalid domain name."));

	/* check DNS */
	$cnt = query("dns/count");
	foreach("dns/entry")
	{
		if ($InDeX>$cnt) break;
		if (ipv6checkip($VaLuE)=="")
			return result("FAILED", $path."/dns/entry:".$InDeX, i18n("The DNS address is invalid."));
	}

	/* check static lease(s) */
	//return verify_staticleases($path);
	
	/* check pd */
	$enablepd = query("pd/enable");
	if($enablepd == "1")
	{
		$pdmode = query("pd/mode");
		$pdnetwork = query("pd/network");
		$pdprefix = query("pd/prefix");
		$pdslalen = query("pd/slalen");
		$pdstart = query("pd/start");
		$pdcount = query("pd/count");
		$pdplft = query("pd/preferlft");
		$pdvlft = query("pd/validlft");
		TRACE_debug("FATLADY: DHCPS6-PD: mode = ".$pdmode);
		TRACE_debug("FATLADY: DHCPS6-PD: network = ".$pdnetwork);
		TRACE_debug("FATLADY: DHCPS6-PD: prefix = ".$pdprefix);
		TRACE_debug("FATLADY: DHCPS6-PD: slalen = ".$pdslalen);
		TRACE_debug("FATLADY: DHCPS6-PD: start = ".$pdstart);
		TRACE_debug("FATLADY: DHCPS6-PD: count = ".$pdcount);
		TRACE_debug("FATLADY: DHCPS6-PD: preferlft = ".$pdplft);
		TRACE_debug("FATLADY: DHCPS6-PD: validlft = ".$pdvlft);

		if($pdmode=="0") /* generic */
		{
			/* check pd network */
			if ($pdnetwork!="")
			{
				if (ipv6checkip($pdnetwork)=="")
					return result("FAILED", $path."/pd/network",
						i18n("Invalid pd Network ID."));

				/* check prefix */
				if (isdigit($pdprefix)==0)
				return result("FAILED", $path."/pd/prefix",
						i18n("The PD prefix value should be a decimal number."));
			}

			/* check pd sla length */
			if($pdslalen<0 || $pdslalen >32)
				return result("FAILED", $path."/pd/slalen",
						i18n("The pd sla length should be between 0 and 32."));
			if($pdprefix!="")
			{
				$total = $pdprefix+$pdslalen;
				if($total<0 || $total >128)
					return result("FAILED", $path."/pd/slalen",
						i18n("The sum of prefix and sla length should be between 0 and 128"));
			}

			/* check pd pool range */
			if (isdigit($pdstart)==0)
				return result("FAILED", $path."/pd/start",
					i18n("The start value of the DHCP-PD lease pool should be a decimal number."));
			if (isdigit($pdcount)==0)
				return result("FAILED", $path."/pd/count",
					i18n("The DHCP-PD lease pool count value should be a decimal number."));
			//if ($pdcount >= 4294967296)
			if ($pdcount >= 256)
				return result("FAILED", $path."/pd/count",
					i18n("The maximum value of the DHCP-PD count parameter is 255."));
			
			if($pdstart!="" && $pdslalen!="")
			{
				$exp = $pdslalen+1-1;
				$i=0;
				$sum=1;
				while($i<$exp)
				{
					$sum = $sum*2;
					$i++;
				}	
				$cntmax = $sum-1;	
				if($pdstart<0 || $pdstart>$cntmax)
					return result("FAILED", $path."/pd/start",
						i18n("The start of DHCP-PD lease pool should not exceed the limit."));
				$pdstop = $pdstart+$pdcount;
				$limit = $pdstart+$cntmax;
				if($pdstart>$pdstop || $pdstop>$limit)
					return result("FAILED", $path."/pd/stop",
						i18n("The stop of DHCP-PD lease pool should not exceed the limit."));
			}		
			return result("OK","","");
		}
		else if($pdmode=="1") /* dlink */
			return result("OK","","");
	}
	else
		return result("OK","","");
}

set_result("FAILED","","");

$path = XNODE_get_var("FATLADY_DHCPS_PATH");
if ($path=="")
	set_result("FAILED","","No XML document");
else
{
	$max  = query("/dhcps6/max");
	$dhcp = query($path."/inf/dhcps6");
	if ($dhcp!="")
	{
		$count = query($path."/dhcps6/count");
		$seqno = query($path."/dhcps6/seqno");
		if($count>$max) $count = $max;
		$dhcpentry = XNODE_getpathbytarget($path."/dhcps6", "entry", "uid", $dhcp, 0);
		if ($dhcpentry!="") check_dhcp_config($dhcpentry, $path."/inet/entry");
	}
	else
	{
		set_result("OK","","");
	}
}
?>
