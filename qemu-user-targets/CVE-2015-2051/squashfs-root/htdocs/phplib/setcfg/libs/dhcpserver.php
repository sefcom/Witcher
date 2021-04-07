<?
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function copy_entry($from, $to, $cnt)
{
	$i = 0;
	while ($i < $cnt)
	{
		$i++;
		set($to."/entry:".$i, query($from."/entry:".$i));
	}
}

function copy_dhcps4($from, $to)
{
	set($to."/start",		query($from."/start"));
	set($to."/end",			query($from."/end"));
	set($to."/domain",		query($from."/domain"));
	set($to."/leasetime",	query($from."/leasetime"));
	set($to."/router",		query($from."/router"));
	del($to."/dns");
	del($to."/wins");
	del($to."/staticleases");
	$cnt = query($from."/dns/count");
	set($to."/dns/count",	$cnt);
	if ($cnt > 0) copy_entry($from."/dns", $to."/dns", $cnt);
	$cnt = query($from."/wins/count");
	set($to."/wins/count",	$cnt);
	if ($cnt > 0) copy_entry($from."/wins", $to."/wins", $cnt);
	mov($from."/staticleases",  $to);
}

function dhcps_setcfg($prefix, $svc)
{
	/* set dhcpX of inf */
	$inf = cut($svc, 1, ".");
	$svc = tolower(cut($svc, 0, "."));
	$base = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	$dhcps_uid = query($prefix."/inf/".$svc);
	set($base."/".$svc, $dhcps_uid);

	/* copy the dhcp profile. */
	$uid = INF_getinfinfo($inf, $svc);
	$spath = XNODE_getpathbytarget($prefix."/".$svc, "entry", "uid", $dhcps_uid, 0);
	$dhcps = XNODE_getpathbytarget("/".$svc, "entry", "uid", $uid, 0);
	if ($dhcps!="")
	{
		if		($svc == "dhcps4") copy_dhcps4($spath, $dhcps);
		/*else if	($svc == "dhcps6") copy_dhcps6($spath, $dhcps);*/
		else if	($svc == "dhcps6")
		{
			$_GLOBALS["SETCFG_DHCPS6_SRC_PATH"] = $spath;
			$_GLOBALS["SETCFG_DHCPS6_DST_PATH"] = $dhcps;
			$b = "/htdocs/phplib/setcfg/libs";
			dophp("load", $b."/dhcps6.php");
		}
	}
	else TRACE_error("SETCFG/DHCPS: no dhcps entry for [".$uid."] found!");
}

?>
