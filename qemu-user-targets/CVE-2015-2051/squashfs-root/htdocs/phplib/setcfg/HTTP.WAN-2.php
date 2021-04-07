<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";
$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-2", 0);
if ($infp!="")
{
	set($infp."/web", query($SETCFG_prefix."/inf/web"));
	set($infp."/weballow/hostv4ip", query($SETCFG_prefix."/inf/weballow/hostv4ip"));
}
?>
