<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
}
function check_ntp($prefix)
{
	$ntp = query($prefix."/enable");
	if ($ntp != "1") set($prefix."/enable", "0");
	else
	{
		/* TODO: need to validate the server. */
		$server = query($prefix."/server");
		TRACE_debug("FATLADY: server=".$server);
		if ($server=="")
		{
			set_result("FAILED", $prefix."/server", i18n("Invalid NTP server"));
			return;
		}
		$period = query($prefix."/period");
		TRACE_debug("FATLADY: period=".$period);
		if (isdigit($period)!=1)
		{
			set_result("FAILED", $prefix."/period", i18n("Invalid Update period"));
			return;
		}
		if ($period==0)
		{
			set_result("FAILED", $prefix."/period", i18n("Invalid Update period"));
			return;
		}
	}
	set_result("OK", "", "");
}

function check_tz_dst($prefix)
{
	$maxtz = query("/runtime/services/timezone/zone#");
	$tz = query($prefix."/timezone");
	if ($tz > $maxtz || $tz <= 0)
	{
		set_result("FAILED", $prefix."/timezone", i18n("Invalid timezone setting."));
		return;
	}
	if (query("device/time/dst")!="1") set("device/time/dst", "0");
	TRACE_debug("FATLADY: DEVICE.TIME: timezone=".$tz.", dst=".query("device/time/dst"));
	set_result("OK", "", "");
}

check_ntp($FATLADY_prefix."/device/time/ntp");
if ($_GLOBALS["FATLADY_result"]=="OK") check_tz_dst($FATLADY_prefix."/device/time");

if ($_GLOBALS["FATLADY_result"]=="OK") set($FATLADY_prefix."/valid", 1);
?>
