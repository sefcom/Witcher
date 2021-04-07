<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/inet.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]  = $result;
	$_GLOBALS["FATLADY_node"]    = $node;
	$_GLOBALS["FATLADY_message"] = $message;
}

function check_remote($entry)
{
	$port = query($entry."/inf/web");
	if ($port != "")
	{
		if (isdigit($port)!="1")
		{
			set_result("FAILED", $entry."/inf/web", i18n("Invalid port number"));
			return 0;
		}
		if ($port<1 || $port>65535)
		{
			set_result("FAILED", $entry."/inf/web", i18n("Invalid port range"));
			return 0;
		}
		
		// Check with VSVR and PFWD. Currently only with NAT-1;
		$nat = XNODE_getpathbytarget("/nat", "entry", "uid", "NAT-1");
		if ($nat!="")
		{
			$i=1;
			while ( $i<=2 )
			{
				if ( $i==1 ) { $target = "portforward";    $svr_str = i18n("PORT FORWARDING"); }
				else         { $target = "virtualserver";  $svr_str = i18n("VIRTUAL SERVER"); }
				
				$count = query($nat."/".$target."/entry#");
				TRACE_debug("FATLADY: check HTTP.WAN with ".$nat."/".$target." count:".$count);

				$j = 1;
				while ($j <= $count)
				{
					$CurBase = $nat."/".$target."/entry:".$j;
					if ( query($CurBase."/protocol")=="TCP+UDP" || query($CurBase."/protocol")=="TCP" )  // Need to check the ranges.
					{
						if( $port>=query($CurBase."/external/start") && $port<=query($CurBase."/external/end") )
						{
							set_result("FAILED", $entry."/inf/web", i18n("The port number is used by")." ".i18n($svr_str)."." );
							return 0;
						}
					}
					$j++;
				}
				
				$i++;
			}
			
		}

	}

	$host = query($entry."/inf/weballow/hostv4ip");
	if ($host != "")
	{
		if (INET_validv4addr($host)!="1")
		{
			set_result("FAILED", $entry."/inf/weballow/hostv4ip", i18n("Invalid host IP address"));
			return 0;
		}
	}

	set_result("OK", "", "");
	return 1;
}

if (check_remote($FATLADY_prefix)=="1") set($FATLADY_prefix."/valid", "1");
?>
