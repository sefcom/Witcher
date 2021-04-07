<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
if (query($FATLADY_prefix."/device/multicast/igmpproxy")!="1")
	set($FATLADY_prefix."/device/multicast/igmpproxy", "0");
if (query($FATLADY_prefix."/device/multicast/wifienhance")!="1")
	set($FATLADY_prefix."/device/multicast/wifienhance", "0");

if (query($FATLADY_prefix."/device/multicast/mldproxy")!="")
{	set($FATLADY_prefix."/device/multicast/mldproxy", query($FATLADY_prefix."/device/multicast/mldproxy"));}
if (query($FATLADY_prefix."/device/multicast/wifienhance6")!="")
{	set($FATLADY_prefix."/device/multicast/wifienhance6", query($FATLADY_prefix."/device/multicast/wifienhance6"));}

set($FATLADY_prefix."/valid", "1");
$_GLOBALS["FATLADY_result"]  = "OK";
$_GLOBALS["FATLADY_node"]    = "";
$_GLOBALS["FATLADY_message"] = "";
?>
