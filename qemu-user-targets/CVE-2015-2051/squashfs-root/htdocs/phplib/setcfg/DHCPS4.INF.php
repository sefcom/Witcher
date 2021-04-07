<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";

movc($SETCFG_prefix."/dhcps4", "/dhcps4");
foreach($SETCFG_prefix."/inf")
{
	$uid = query("uid");
	$b = XNODE_getpathbytarget("", "inf", "uid", $uid, 0);
	if ($b!="") set($b."/dhcps4", query("dhcps4"));
}
?>
