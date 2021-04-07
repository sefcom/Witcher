<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

$layout = query($FATLADY_prefix."/device/layout");
TRACE_debug("FATLADY: DEVICE.LAYOUT: layout=".$layout);

if ($layout=="router" || $layout=="bridge" || $layout=="auto")
{
	set($FATLADY_prefix."/valid", "1");
	$_GLOBALS["FATLADY_result"]  = "OK";
	$_GLOBALS["FATLADY_node"]    = "";
	$_GLOBALS["FATLADY_message"] = "";
}
else
{
	$_GLOBALS["FATLADY_result"]  = "FAILED";
	$_GLOBALS["FATLADY_node"]    = $FATLADY_prefix."/device/layout";
	$_GLOBALS["FATLADY_message"] = "unknown layout value";	/* internal error, no i18n. */
}
?>
