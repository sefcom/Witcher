<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

function check_pfwd_config($prefix, $nat, $svc)
{
	set_result("FAILED","","");
	$rlt = "0";

	$db = XNODE_getpathbytarget("/nat", "entry", "uid", $nat, 0);
	if ($db=="")	set_result("FAILED", "", "Can't Find ".$db);

	if ($svc=="PFWD")
	{
		$oth_db = $db."/virtualserver";
		$db     = $db."/portforward";
		$base   = $prefix."/nat/entry/portforward";
		$overlap_str = i18n("Overlapping with a VIRTUAL SERVER rule.");
	}
	else if ($svc=="VSVR")
	{
		$oth_db = $db."/portforward";
		$db     = $db."/virtualserver";
		$base   = $prefix."/nat/entry/virtualserver";
		$overlap_str = i18n("Overlapping with a Port Forwarding rule.");
	}

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

		if (query("protocol")=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/protocol",i18n("Please select the protocol."));
			$rlt = "-1";
			break;
		}
		else if (query("protocol")!="TCP"&&query("protocol")!="UDP"&&query("protocol")!="TCP+UDP")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/protocol",i18n("The protocol is invalid."));
			$rlt = "-1";
			break;
		}

		$ex_start	= query("external/start");
		$ex_end		= query("external/end");
		if ($ex_end=="") $ex_end = $ex_start;
		if ($ex_start=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("Please input the public port."));
			$rlt = "-1";
			break;
		}

		if (isdigit($ex_start)=="0" || isdigit($ex_end)=="0")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port is invalid.")
						." ".i18n("Wrong value."));
			$rlt = "-1";
			break;
		}

		/* convert to integer */
		$ex_start += 0;
		$ex_end += 0;
		set("external/start", $ex_start);
		set("external/end", $ex_end);

		if ($ex_start > $ex_end)
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port is invalid.")
						." ".i18n("End port should be bigger than start port."));
			$rlt = "-1";
			break;
		}
		
		if ($ex_start<"1"||$ex_start>"65535"||$ex_end<"1"||$ex_end>"65535")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port is invalid.")
						." ".i18n("The port is out of the boundary."));
			$rlt = "-1";
			break;
		}
		
		// Check overlapping of the public port with the previous.
		$Cur = 1;
		while ($Cur <$InDeX)
		{
			$CurBase = $base."/entry:".$Cur;
			if ( query($CurBase."/protocol")=="TCP+UDP" ||
				query("protocol")=="TCP+UDP" ||
				query($CurBase."/protocol")==query("protocol") )  // Need to check the ranges.
			{
				if( $ex_start>query($CurBase."/external/end") || $ex_end<query($CurBase."/external/start") )
				{
				}
				else
				{
					set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port is invalid:")
								." ".i18n("Overlapping between rules.") );
					$rlt = "-1";
					break;					
				}
			}
			$Cur++;
		}
		if( $rlt == "-1" ) { break; }		
		
		// Check overlapping of the public port with the other service, PFWD or VSVR.
		$oth_count = query($oth_db."/entry#");
		if ( $oth_count!=0 )
		{
			TRACE_debug("FATLADY: check ".$svc.".".$nat." with ".$oth_db." count:".$oth_count);

			$Cur = 1;
			while ($Cur <= $oth_count)
			{
				$CurBase = $oth_db."/entry:".$Cur;
				if ( query($CurBase."/protocol")=="TCP+UDP" ||
					query("protocol")=="TCP+UDP" ||
					query($CurBase."/protocol")==query("protocol") )  // Need to check the ranges.
				{
					if( $ex_start>query($CurBase."/external/end") || $ex_end<query($CurBase."/external/start") )
					{
					}
					else
					{
						set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port is invalid:")
									." ".i18n($overlap_str) );
						$rlt = "-1";
						break;					
					}
				}
				$Cur++;
			}
			
			if( $rlt == "-1" ) { break; }
		}
		
		// Check the port range with HTTP service.
		if ( query("protocol")=="TCP+UDP" || query("protocol")=="TCP" )
		{
			TRACE_debug("FATLADY: check ".$svc.".".$nat." with HTTP service");
			
			$Cur  = 1;
			$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-".$Cur, 0);
			while ( $infp != "" )
			{								
				if( query($infp."/web") >= $ex_start && query($infp."/web") <= $ex_end )
				{
					if ($svc=="VSVR") { set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port is used by Remote Management.") ); }
					else              { set_result("FAILED",$base."/entry:".$InDeX."/external/start",i18n("The public port range conflicts with Remote Management.") ); }
					
					$rlt = "-1";
					break;					
				}
				
				$Cur++;
				$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-".$Cur, 0);
			}
			
			if( $rlt == "-1" ) { break; }
		}
		
		$inet	= INF_getinfinfo(query("internal/inf"), "inet");
		$mask	= INET_getinetinfo($inet, "ipv4/mask");
		$hostid	= query("internal/hostid");
		if ($hostid=="")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/internal/hostid",i18n("The IP Address could not be blank."));
			$rlt = "-1";
			break;
		}
		if (isdigit($hostid)=="0")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/internal/hostid",i18n("The IP Address is invalid."));
			$rlt = "-1";
			break;
		}
		if ($hostid<1 || $hostid>=ipv4maxhost($mask))
		{
			set_result("FAILED",$base."/entry:".$InDeX."/internal/hostid",i18n("The IP Address is invalid."));
			$rlt = "-1";
			break;
		}
		
		$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
		$path_lan1_inet = XNODE_getpathbytarget("/inet", "entry", "uid", query($path_inf_lan1."/inet"), 0);
		$hostid_lan1 = ipv4hostid(query($path_lan1_inet."/ipv4/ipaddr"), query($path_lan1_inet."/ipv4/mask"));
		if ($hostid == $hostid_lan1)
		{
			set_result("FAILED",$base."/entry:".$InDeX."/internal/hostid",i18n("The IP Address could not be the same as LAN IP Address."));
			$rlt = "-1";
			break;
		}

		$in_start	= query("internal/start");
		if ($in_start=="") $in_start = $ex_start;
		$in_end		= $in_start + $ex_end - $ex_start;
		if ($in_start=="")
		{
			
			set_result("FAILED",$base."/entry:".$InDeX."/internal/start",i18n("Please input the private port."));
			$rlt = "-1";
			break;
		}

		if (isdigit($in_start)=="0")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/internal/start",i18n("The private port is invalid.")
						." ".i18n("Wrong value."));
			$rlt = "-1";
			break;
		}

		/* convert to integer */
		$in_start += 0;
		set("internal/start", $in_start);

		if ($in_start<"1"||$in_start>"65535"||$in_end>"65535")
		{
			set_result("FAILED",$base."/entry:".$InDeX."/internal/start",i18n("The private port is invalid.")
						." ".i18n("The port is out of the boundary."));
			$rlt = "-1";
			break;
		}
	}

	if ($rlt=="0")
	{
		set($prefix."/valid", "1");
		set_result("OK", "", "");
	}
}

function fatlady_pfwd($prefix, $nat, $svc)
{
	set_result("FAILED","","");
	if ($prefix=="")	set_result("FAILED","","No XML document");
	else				check_pfwd_config($prefix, $nat, $svc);
}

?>
