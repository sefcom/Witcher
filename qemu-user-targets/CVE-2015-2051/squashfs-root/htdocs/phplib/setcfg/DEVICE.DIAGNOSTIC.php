<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";

$inet_host = query($SETCFG_prefix."/device/diagnostic/chkconn/host/entry:4");
TRACE_debug("SETCFG/DEVICE.DIAGNOSTIC: internet host = ".$inet_host);
set("/device/diagnostic/chkconn/host/entry:4", $inet_host);

?>
