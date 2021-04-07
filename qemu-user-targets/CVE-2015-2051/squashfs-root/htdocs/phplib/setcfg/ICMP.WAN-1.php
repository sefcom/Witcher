<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";
$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
if ($infp!="") set($infp."/icmp", query($SETCFG_prefix."/inf/icmp"));
?>
