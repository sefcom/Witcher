<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

$inet_host = query($FATLADY_prefix."/device/diagnostic/chkconn/host/entry:4");
TRACE_debug("FATLADY: DEVICE.DIAGNOSTIC: FATLADY_prefix=".$FATLADY_prefix);
TRACE_debug("FATLADY: DEVICE.DIAGNOSTIC: internet host=".$inet_host);

if (isdomain($inet_host)!="0"||$inet_host=="")
{
	set($FATLADY_prefix."/valid", "1");
	$_GLOBALS["FATLADY_result"]  = "OK";
	$_GLOBALS["FATLADY_node"]    = "";
	$_GLOBALS["FATLADY_message"] = "";
}
else
{
	$_GLOBALS["FATLADY_result"]  = "FAILED";
	$_GLOBALS["FATLADY_node"]    = $FATLADY_prefix."/device/diagnostic/chkconn/host/entry:4";
	$_GLOBALS["FATLADY_message"] = i18n("Invalid Internet Host");
}

?>
