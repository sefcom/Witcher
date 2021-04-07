<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
set($path_run_inf_wan1."/ddns4", "");
movc($SETCFG_prefix."/runtime/inf/ddns4", $path_run_inf_wan1."/ddns4");
?>
