<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
/*Get mdnsresponder*/
function setup_mdns($uid,$port,$srvname,$srvcfg)
{
	$dirty=0;
	$stsp = XNODE_getpathbytarget("/runtime/services/mdnsresponder", "server", "uid", $uid, 0);
	if ($stsp=="")
	{
		if ($port!="0")
		{
			$dirty++;
			$stsp = XNODE_getpathbytarget("/runtime/services/mdnsresponder", "server", "uid", $uid, 1);
			set($stsp."/srvname",$srvname);
			set($stsp."/port",	$port);
			set($stsp."/srvcfg",	$srvcfg);
		}
	}
	else
	{
		if ($port=="0") { $dirty++; del($stsp); }
		else
		{
			if (query($stsp."/srvname")!=$srvname)	{ $dirty++; set($stsp."/srvname", $srvname); }
			if (query($stsp."/port")!=$port)		{ $dirty++; set($stsp."/port", $port); }
			if (query($stsp."/srvcfg")!=$srvcfg)	{ $dirty++; set($stsp."/srvcfg", $srvcfg); }
		}
	}
	return $dirty;
}
function setup_mdns_txt($uid, $port, $srvname, $srvcfg, $txt)
{
	$dirty=0;
	$stsp = XNODE_getpathbytarget("/runtime/services/mdnsresponder", "server", "uid", $uid, 0);
	if ($stsp=="")
	{
		if ($port!="0")
		{
			$dirty++;
			$stsp = XNODE_getpathbytarget("/runtime/services/mdnsresponder", "server", "uid", $uid, 1);
			set($stsp."/srvname",$srvname);
			set($stsp."/port",	$port);
			set($stsp."/srvcfg",	$srvcfg);
			set($stsp."/txt",	$txt);
		}
	}
	else
	{
		if ($port=="0") { $dirty++; del($stsp); }
		else
		{
			if (query($stsp."/srvname")!=$srvname)	{ $dirty++; set($stsp."/srvname", $srvname); }
			if (query($stsp."/port")!=$port)		{ $dirty++; set($stsp."/port", $port); }
			if (query($stsp."/srvcfg")!=$srvcfg)	{ $dirty++; set($stsp."/srvcfg", $srvcfg); }
			if (query($stsp."/txt")!=$txt)	{ $dirty++; set($stsp."/txt", $txt); }
		}
	}
	return $dirty;
}
?>
