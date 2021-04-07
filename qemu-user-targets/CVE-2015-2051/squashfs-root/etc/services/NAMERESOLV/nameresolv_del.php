<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

$stsp = XNODE_getpathbytarget("/runtime/services/nameresolv", "ifname", "uid", $IFNAME, 0);
if ($stsp!="")
{
	/* set dirty flag for this interface */
	set($stsp."/dirty",	1);
}	
?>
