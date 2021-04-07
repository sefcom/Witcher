<? /* vi: set sw=4 ts=4: */
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function isport($no)
{
	if (isdigit($no)=="0")  return "-1";
	if ($no<1 || $no>65535) return "-1";
	return "0";
}

function check_portlist($port)
{
	if (cut_count($port,"-") > 1)
	{
		$start = cut($port,0,"-");
		$end = cut($port,1,"-");
		if (isport($start)=="-1" || isport($end)=="-1")
			return "-1";
		else if ($start > $end)
			return "-1";
		else
			return "0";
	}
	else
	{
		return isport($port);
	}
}

function check_portt_config($prefix, $nat)
{
	set_result("FAILED","","");
	$rlt = "0";

	$db = XNODE_getpathbytarget("/nat", "entry", "uid", $nat, 0);
	if ($db=="")
	{
		set_result("FAILED", "", "Can't Find ".$nat);
		$rlt = "-1";
		return;
	}

	$db = $db."/porttrigger";
	$base = $prefix."/nat/entry/porttrigger";

	if (query($base."/entry#") > query($db."/max"))
	{
		set_result("FAILED", $base."/max", i18n("The rules exceed maximum."));
		$rlt = "-1";
		return;
	}

	foreach($base."/entry")
	{
		if (query("description")=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/description",i18n("Please input the rule name."));
			$rlt = "-1";
			break;
		}

		$protocol = query("trigger/protocol");
		if ($protocol=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/protocol",i18n("Please select the protocol."));
			$rlt = "-1";
			break;
		}
		else if ($protocol!="TCP"&&$protocol!="UDP"&&$protocol!="TCP+UDP"&&$protocol!="ICMP"&&$protocol!="ALL")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/protocol",i18n("The protocol is invalid."));
			$rlt = "-1";
			break;
		}

		$pt_start	= query("trigger/start");
		$pt_end		= query("trigger/end");
		if ($pt_end=="") $pt_end = $pt_start;
		if ($pt_start=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/start",i18n("Please input the trigger port range."));
			$rlt = "-1";
			break;
		}
		if (isdigit($pt_start)=="0")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/start",i18n("The trigger port range is invalid.")
						." ".i18n("Wrong value."));
			$rlt = "-1";
			break;
		}
		if (isdigit($pt_end)=="0")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/end",i18n("The trigger port range is invalid.")
						." ".i18n("Wrong value."));
			$rlt = "-1";
			break;
		}
		if ($pt_start > $pt_end)
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/start",i18n("The trigger port range is invalid.")
						." ".i18n("End port should be bigger than start port."));
			$rlt = "-1";
			break;
		}
		if ($pt_start<"1"||$pt_start>"65535")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/start",i18n("The trigger port range is invalid.")
						." ".i18n("The port range is out of the boundary."));
			$rlt = "-1";
			break;
		}
		if ($pt_end<"1"||$pt_end>"65535")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/trigger/end",i18n("The trigger port range is invalid.")
						." ".i18n("The port range is out of the boundary."));
			$rlt = "-1";
			break;
		}

		$protocol = query("external/protocol");
		if ($protocol=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/protocol",i18n("Please select the protocol."));
			$rlt = "-1";
			break;
		}
		else if ($protocol!="TCP"&&$protocol!="UDP"&&$protocol!="TCP+UDP"&&$protocol!="ICMP"&&$protocol!="ALL")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/protocol",i18n("The protocol is invalid."));
			$rlt = "-1";
			break;
		}

		$pt_list = query("external/portlist");
		if ($pt_list=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/portlist",i18n("Please input the port range of firewall."));
			$rlt = "-1";
			break;
		}
		$cnt = cut_count($pt_list, ",");
		$idx = 0;
		while ($idx < $cnt)
		{
			if (check_portlist(cut($pt_list,$idx,","))=="-1")
			{
				set_result("FAILED",$base."/entry:".$InDeX."/external/portlist",i18n("The port range of the firewall is invalid."));
				$rlt = "-1";
				break;
			}
			$idx++;
		}
	}

	if ($rlt=="0")
	{
		set($prefix."/valid", "1");
		set_result("OK", "", "");
	}
}

set_result("FAILED","","");
if ($FATLADY_prefix=="")	set_result("FAILED","","No XML document");
else						check_portt_config($FATLADY_prefix, "NAT-1");
?>
