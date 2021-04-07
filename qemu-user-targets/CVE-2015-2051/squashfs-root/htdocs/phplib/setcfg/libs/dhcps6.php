<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function copy_dhcps6($from, $to)
{
	set($to."/network",		query($from."/network"));
	set($to."/prefix",		query($from."/prefix"));
	set($to."/start",		query($from."/start"));
	set($to."/count",		query($from."/count"));
	set($to."/mode",		query($from."/mode"));
	set($to."/domain",		query($from."/domain"));
	$cnt = query($from."/dns/count");
	set($to."/dns/count",   $cnt);
	if ($cnt > 0) copy_entry($from."/dns", $to."/dns", $cnt);

	set($to."/pd/enable",		query($from."/pd/enable"));
	set($to."/pd/mode",		query($from."/pd/mode"));
	set($to."/pd/network",		query($from."/pd/network"));
	set($to."/pd/prefix",		query($from."/pd/prefix"));
	set($to."/pd/slalen",		query($from."/pd/slalen"));
	set($to."/pd/start",		query($from."/pd/start"));
	set($to."/pd/count",		query($from."/pd/count"));
	set($to."/pd/preferlft",	query($from."/pd/preferlft"));
	set($to."/pd/validlft",		query($from."/pd/validlft"));
}

TRACE_debug("SETCFG: dhcps6from=[".$_GLOBALS["SETCFG_DHCPS6_SRC_PATH"]."],dhcps6to=[".$_GLOBALS["SETCFG_DHCPS6_DST_PATH"]."]");
copy_dhcps6($_GLOBALS["SETCFG_DHCPS6_SRC_PATH"],$_GLOBALS["SETCFG_DHCPS6_DST_PATH"]);
?>
