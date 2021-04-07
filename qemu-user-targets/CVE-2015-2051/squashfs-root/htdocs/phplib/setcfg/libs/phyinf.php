<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function phyinf_setcfg($prefix)
{
	$phy = query($prefix."/inf/phyinf");
	$phytmp = XNODE_getpathbytarget($prefix, "phyinf", "uid", $phy, 0);
	$phyinf = XNODE_getpathbytarget("", "phyinf", "uid", $phy, 0);
	if ($phy == "" || $phytmp == "" || $phyinf == "")
	{
		TRACE_error("SETCFG/PHYINF: no phyinf entry for [".$phy."] found !");
		return;
	}

	/* We only valid 'macaddr' & 'media' in 'fatlady',
	 * so only save these 2 nodes. */
	$macaddr = query($phytmp."/macaddr");
	set($phyinf."/macaddr", $macaddr);
	$type = query($phytmp."/type");
	if ($type == "eth")
	{
		$media = query($phytmp."/media/linktype");
		set($phyinf."/media/linktype", $media);
	}
}
?>
