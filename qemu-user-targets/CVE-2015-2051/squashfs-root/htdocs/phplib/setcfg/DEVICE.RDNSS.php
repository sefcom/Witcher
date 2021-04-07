<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$rdnss = query($SETCFG_prefix."/device/rdnss");
TRACE_debug("SETCFG/DEVICE.RDNSS: rdnss [".$rdnss."]");
set("/device/rdnss", $rdnss);
$changemode = query($SETCFG_prefix."/device/v6modechange");
TRACE_debug("SETCFG/DEVICE.RDNSS: v6modechange [".$changemode."]");
set("/device/v6modechange", $changemode);
?>
