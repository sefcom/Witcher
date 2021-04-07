<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

set_result("FAILED","","");
$rlt = "0";

$val = query($FATLADY_prefix."/callmgr/voice_service:1/phone/analog:1/enable");
if ($val != "0") set($FATLADY_prefix."/callmgr/voice_service:1/phone/analog:1/enable", "1");

$val = query($FATLADY_prefix."/callmgr/voice_service:1/phone/analog:1/callerid_display");
if ($val != "0") set($FATLADY_prefix."/callmgr/voice_service:1/phone/analog:1/callid_display", "1");

$service_state = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/service_state");
TRACE_debug("FATLADY: service_state".$service_state);
if($service_state != 0 && $servcie_state == "")
{

    /*
     * The following check "Caller ID Delivery", "Call waiting"  and all "Call forwarding"
     */
    $val = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/callerid_delivery");
    if ($val != "0") set($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/callerid_delivery", "1");

    $val = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/callwaiting");
    if ($val != "0") set($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/callwaiting", "1");

    if(query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/unconditional") == 1)
   {
	$number = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/unconditional_number");
//TRACE_debug("FATLADY: unconditional_number".$number);
	if($number == "")
	{
		set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/unconditional_number", i18n("The Unconditional Number cannot be empty."));
		$rlt = "-1";
	}
	else
	{
        	if(substr($number,0,1)=="+")
                {
                	$len = strlen($number);
                	$string = substr($number,1,$len-1);
			if(isdigit($string)=="0")
			{
				set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/unconditional_number", i18n("Invalid value for the Unconditional Number."));
				$rlt = "-1";
			}
		}
		else
		{
                        if(isdigit($number)=="0")
                        {
                                set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/unconditional_number", i18n("Invalid value for the Unconditional Number."));
                                $rlt = "-1";
                        }
		}
	}
    }
    else
    {
	if(query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/busy") == 1)
	{
		$number = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/busy_number");
//TRACE_debug("FATLADY: busy_number".$number);
		if($number == "")
		{
			set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/busy_number", i18n("The Busy Number parameter cannot be empty."));
			$rlt = "-1";
		}
		else
		{
                        if(substr($number,0,1)=="+")
                        {
                                $len = strlen($number);
                                $string = substr($number,1,$len-1);
				if(isdigit($string)=="0")
				{
					set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/busy_number", i18n("Invalid value for the Busy Number."));
					$rlt = "-1";
				}
			}
			else
			{
                                if(isdigit($number)=="0")
                                {
                                        set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/busy_number", i18n("Invalid value for the Busy Number."));
                                        $rlt = "-1";
                                }
			}
		}
	}

	if(query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply") == 1)
	{
		$number = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_number");
//TRACE_debug("FATLADY: no_reply_number".$number);
		if($number == "")
		{
			set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_number", i18n("The No Answer Number cannot be empty."));
			$rlt = "-1";
		}
		else
		{
                        if(substr($number,0,1)=="+")
                        {
                                $len = strlen($number);
                                $string = substr($number,1,$len-1);
				if(isdigit($string)=="0")
				{
					set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_number", i18n("The No Answer Number parameter value is invalid."));
					$rlt = "-1";
				}
			}
			else
			{
                                if(isdigit($number)=="0")
                                {
                                        set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_number", i18n("The No Answer Number parameter value is invalid."));
                                        $rlt = "-1";
                                }
			}
		}

		$time = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_time");
		if($time == "")
		{
			set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_time", i18n("The No Answe Timer value cannot be empty."));
			$rlt = "-1";
		}
		else
		{
			if(isdigit($time)=="0")
			{
				set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/no_reply_time", i18n("Invalid value for the No Answer Time."));
				$rlt = "-1";
			}
		}
	}

	if(query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/not_reachable") == 1)
	{
		$number = query($FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/not_reachable_number");
//TRACE_debug("FATLADY: not_reachable_number".$number);
		if($number == "")
		{
			set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/not_reachable_number", i18n("The Not Reachable Number cannot be empty."));
			$rlt = "-1";
		}
		else
		{
			if(substr($number,0,1)=="+")
			{
				$len = strlen($number);
				$string = substr($number,1,$len-1);
				if(isdigit($string)=="0")
				{
					set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/not_reachable_number", i18n("Invalid value for the Not Reachable Number."));
					$rlt = "-1";
				}
			}
			else
			{
                                if(isdigit($number)=="0")
                                {
                                        set_result("FAILED", $FATLADY_prefix."/runtime/callmgr/voice_service:1/mobile/call_forward/not_reachable_number", i18n("Invalid value for the Not Reachable Number."));
                                        $rlt = "-1";
                                }				

			}
		}
	}

    }
}

if ($rlt=="0")
{
	set($FATLADY_prefix."/valid", "1");
	set_result("OK", "", "");
}
?>
