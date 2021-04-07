<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
$icmp = query($FATLADY_prefix."/inf/icmp");
if ($icmp!="ACCEPT" && $icmp!="DROP") set($FATLADY_prefix."/inf/icmp", "");
set($FATLADY_prefix."/valid", "1");
$_GLOBALS["FATLADY_result"]  = "OK";
$_GLOBALS["FATLADY_node"]    = "";
$_GLOBALS["FATLADY_message"] = "";
?>
