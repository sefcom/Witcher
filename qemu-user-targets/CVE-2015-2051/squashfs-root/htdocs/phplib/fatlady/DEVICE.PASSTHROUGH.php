<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";

$val = query($FATLADY_prefix."/device/passthrough/ipv6");
if ($val != "1") set($FATLADY_prefix."/device/passthrough/ipv6", "0");

$val = query($FATLADY_prefix."/device/passthrough/pppoe");
if ($val != "1") set($FATLADY_prefix."/device/passthrough/pppoe", "0");

set($FATLADY_prefix."/valid", "1");
$_GLOBALS["FATLADY_result"]  = "OK";
$_GLOBALS["FATLADY_node"]    = "";
$_GLOBALS["FATLADY_message"] = "";
?>
