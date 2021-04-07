<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

/*
 * Internet Usage Meter.
 */
TRACE_debug("enable==============".query($SETCFG_prefix."/callmgr/mobile/flowmeter/enable"));
movc($SETCFG_prefix."/callmgr/mobile/flowmeter","/callmgr/mobile/flowmeter");
TRACE_debug("enable==============".query("/callmgr/mobile/flowmeter/enable"));

?>
