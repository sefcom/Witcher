<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";

set("/device/qos/enable",		query($SETCFG_prefix."/device/qos/enable"));
set("/device/qos/autobandwidth",	query($SETCFG_prefix."/device/qos/autobandwidth"));

$curwan = query($SETCFG_prefix."/runtime/device/curwan");
$infp = XNODE_getpathbytarget("", "inf", "uid", $curwan, 0);
if ($infp!="")
{
	set($infp."/bandwidth/upstream",	query($SETCFG_prefix."/inf/bandwidth/upstream"));
	set($infp."/bandwidth/type",		query($SETCFG_prefix."/inf/bandwidth/type"));
}

set("/runtime/device/qos/bwup",		query($SETCFG_prefix."/runtime/device/qos/bwup"));
set("/runtime/device/qos/monitor",     query($SETCFG_prefix."/runtime/device/qos/monitor"));
?>
