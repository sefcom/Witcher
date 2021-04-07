<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/setcfg/libs/inet.php";
include "/htdocs/phplib/xnode.php";
foreach ($SETCFG_prefix."/inf")
{
	$inf = query("uid");
	$infp = XNODE_getpathbytarget($SETCFG_prefix, "inf", "uid", $inf, "0");
	inet_setcfg($SETCFG_prefix, $infp);
}
/* The inet setting of WAN is changed, flush the resolved
 * DNS cache for domain routing function. */
event("DNSCACHE.FLUSH");
?>
