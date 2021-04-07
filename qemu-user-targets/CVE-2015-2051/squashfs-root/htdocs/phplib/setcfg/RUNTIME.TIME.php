<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$date = query($SETCFG_prefix."/runtime/device/date");
$time = query($SETCFG_prefix."/runtime/device/time");

TRACE_debug("SETCFG/RUNTIME.TIME: ".$date."  ".$time);

set("/runtime/device/tmp_date", $date);
set("/runtime/device/tmp_time", $time);
//set("/runtime/device/date", $date);
//set("/runtime/device/time", $time);
set("/runtime/device/ntp/state", "RUNNING");
set("/runtime/device/timestate", "RUNNING");
?>
