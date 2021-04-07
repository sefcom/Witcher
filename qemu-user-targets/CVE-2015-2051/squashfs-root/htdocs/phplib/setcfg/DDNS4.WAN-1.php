<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$infuid = query($SETCFG_prefix."/inf/uid");
$ddnsuid = query($SETCFG_prefix."/inf/ddns4");

$infp = XNODE_getpathbytarget("", "inf", "uid", $infuid, 0);
if ($infp != "")
{
	if ($ddnsuid != "")	set($infp."/ddns4", $ddnsuid);
	else			set($infp."/ddns4", "");

	movc($SETCFG_prefix."/ddns4", "/ddns4");
}
?>
