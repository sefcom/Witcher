<?
include "/htdocs/phplib/xnode.php";

function dmz_setcfg($prefix, $svc)
{
	$nat = cut($svc, 1, ".");
	$base = XNODE_getpathbytarget("/nat", "entry", "uid", $nat);

	$dmzenable	= query($prefix."/nat/entry/dmz/enable");
	$dmzinf		= query($prefix."/nat/entry/dmz/inf");
	$dmzhostid	= query($prefix."/nat/entry/dmz/hostid");
	$dmzsch		= query($prefix."/nat/entry/dmz/schedule");

	set($base."/dmz/enable",	$dmzenable);
	set($base."/dmz/hostid",	$dmzhostid);
	set($base."/dmz/inf",		$dmzinf);
	set($base."/dmz/schedule",	$dmzsch);
}

?>
