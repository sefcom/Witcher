<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

$hostname = query($FATLADY_prefix."/device/hostname");
TRACE_debug("FATLADY: DEVICE.HOSTNAME: hostname=".$hostname);

if($hostname==""||isdomain($hostname)=="0"||isdigit($hostname)=="1"||strchr($hostname,".")!="")
{
	$_GLOBALS["FATLADY_result"]  = "FAILED";
	$_GLOBALS["FATLADY_node"]    = $FATLADY_prefix."/device/hostname";
	$_GLOBALS["FATLADY_message"] = i18n("Invalid host name");	/* internal error, no i18n. */
}
else
{
	set($FATLADY_prefix."/valid", "1");
	$_GLOBALS["FATLADY_result"]  = "OK";
	$_GLOBALS["FATLADY_node"]    = "";
	$_GLOBALS["FATLADY_message"] = "";
}

?>
