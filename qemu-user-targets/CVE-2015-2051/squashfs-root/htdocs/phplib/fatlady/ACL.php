<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
if (query($FATLADY_prefix."/acl/dos/enable")!="1")	set($FATLADY_prefix."/acl/dos/enable", "0");
if (query($FATLADY_prefix."/acl/spi/enable")!="1")	set($FATLADY_prefix."/acl/spi/enable", "0");
if (query($FATLADY_prefix."/acl/applications/qq/action")!="DENY")   set($FATLADY_prefix."/acl/applications/qq/action", "ALLOW");
if (query($FATLADY_prefix."/acl/applications/msn/action")!="DENY")   set($FATLADY_prefix."/acl/applications/msn/action", "ALLOW");
if (query($FATLADY_prefix."/acl/applications/kaixin/action")!="DENY")   set($FATLADY_prefix."/acl/applications/kaixin/action", "ALLOW");

set($FATLADY_prefix."/valid", "1");
$_GLOBALS["FATLADY_result"]  = "OK";
$_GLOBALS["FATLADY_node"]    = "";
$_GLOBALS["FATLADY_message"] = "";
?>
