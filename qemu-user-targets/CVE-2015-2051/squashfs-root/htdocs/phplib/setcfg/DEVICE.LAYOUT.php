<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$layout = query($SETCFG_prefix."/device/layout");
if ($layout == "router")
{
	$bridge = "0"; 
	$mode = query($SETCFG_prefix."/device/router/mode");
	set("/device/router/mode", $mode);
}
else if ($layout == "bridge")
{
	$bridge = "1";
	$mode = query($SETCFG_prefix."/device/bridge/mode");
	set("/device/bridge/mode", $mode);
}
else $bridge = "1";

TRACE_debug("SETCFG/DEVICE.LAYOUT: layout [".$layout."], mode [".$mode."]");
set("/device/layout", $layout);
$i = 1;
while ($i>0)
{
	$ifname = "BRIDGE-".$i;
	$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($ifpath == "") { $i=0; break; }
	if ($bridge=="1")
	{	/* Only turn 'ON' the interface, never turn 'OFF'. */
		TRACE_debug("SETCFG/DEVICE.LAYOUT: ".$ifpath."/active = 1");
		set($ifpath."/active", "1");
	}
	$i++;
}
?>
