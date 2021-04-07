<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
function set_result($result, $node, $message)
{
    $_GLOBALS["FATLADY_result"] = $result;
    $_GLOBALS["FATLADY_node"]   = $node;
    $_GLOBALS["FATLADY_message"]= $message;
}
function check_qos_setting($path)
{
	$enable = query($path."/device/qos/enable");
	$auto = query($path."/device/qos/autobandwidth");
	if($enable == "1")
	{
		if($auto == "0")
		{
			if ( isdigit(query($path."/inf/bandwidth/upstream")) == "0" ||
				query($path."/inf/bandwidth/upstream")>102400 ||
				query($path."/inf/bandwidth/upstream")<1 )
			{
				set_result("FAILED", $path."/inf/bandwidth/upstream", i18n("The input uplink speed is invalid."));
				return "FAILED";
			}
			else
			{
				// Remove the leading zeros.
				$upstream_dec = strtoul(query($path."/inf/bandwidth/upstream"), 10);
				set($path."/inf/bandwidth/upstream", $upstream_dec);
			}
		}
		else	set($path."/device/qos/autobandwidth", "1");

		$type = query($path."/inf/bandwidth/type");
                if( $type == "AUTO" || $type == "ADSL" || $type == "CABLE"){}
                else
                {
                        set_result("FAILED", $path."/inf/bandwidth/type", i18n("Unsupported Connection type be assigned."));
                        return "FAILED";
                }
	}
	else	set($path."/device/qos/enable", "0");
	return "OK";
}

set_result("FAILED", "", "");
if(check_qos_setting($FATLADY_prefix)=="OK")
{
	set($FATLADY_prefix."/valid", "1");
	set_result("OK", "", "");
}
?>
