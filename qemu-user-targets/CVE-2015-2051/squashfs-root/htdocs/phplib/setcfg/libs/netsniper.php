<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function netsniper_setcfg($prefix, $nat)
{
	$nat_base = XNODE_getpathbytarget("/nat", "entry", "uid", $nat, 0);
	$prefix_base = XNODE_getpathbytarget($prefix."/nat", "entry", "uid", $nat, 0);
	set($nat_base."/netsniper/enable", query($prefix_base."/netsniper/enable"));
}
?>
