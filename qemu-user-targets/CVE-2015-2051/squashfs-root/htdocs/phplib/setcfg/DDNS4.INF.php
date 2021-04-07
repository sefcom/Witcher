<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

TRACE_debug("SETCFG: DDNS4");

movc($SETCFG_prefix."/ddns4", "/ddns4");
foreach ($SETCFG_prefix."/inf")
{
	$uid = query("uid");
	$ddns4 = query("ddns4");
	$infp = XNODE_getpathbytarget("", "inf", "uid", $uid, 0);
	if ($infp != "")
	{
		set($infp."/ddns4", $ddns4);
		TRACE_debug("SETCFG: ".$uid." [".$infp."], DDNS4 [".$ddns4."]");
	}
}
?>
