<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

$rdnss = query($FATLADY_prefix."/device/rdnss");
TRACE_debug("FATLADY: DEVICE.RDNSS: rdnss=".$rdnss);

if ($rdnss=="1" || $rdnss=="0")
{
	set($FATLADY_prefix."/valid", "1");
	$_GLOBALS["FATLADY_result"]  = "OK";
	$_GLOBALS["FATLADY_node"]    = "";
	$_GLOBALS["FATLADY_message"] = "";
}
else
{
	$_GLOBALS["FATLADY_result"]  = "FAILED";
	$_GLOBALS["FATLADY_node"]    = $FATLADY_prefix."/device/rdnss";
	$_GLOBALS["FATLADY_message"] = "unknown rdnss value";	/* internal error, no i18n. */
}
?>
