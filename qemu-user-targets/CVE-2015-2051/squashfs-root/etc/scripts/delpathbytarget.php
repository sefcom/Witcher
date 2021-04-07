<?
include "/htdocs/phplib/xnode.php";

$stsp = XNODE_getpathbytarget($BASE, $NODE, $TARGET, $VALUE, 0);
if ($stsp != "")
{
	if ($POSTFIX == "")	del($stsp);
	else				del($stsp.'/'.$POSTFIX);
}
?>
