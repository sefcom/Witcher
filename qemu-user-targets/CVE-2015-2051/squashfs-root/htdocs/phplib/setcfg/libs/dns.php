<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function dns4_setcfg($prefix, $inf)
{
	/* find interface path */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	$profile = query($prefix."/inf/dns4");
	set($infp."/dns4", $profile);

	/* move the dns profile. */
	if ($profile != "")
	{
		$dst = XNODE_getpathbytarget("/dns4", "entry", "uid", $profile, 0);
		if ($dst=="") $dst = XNODE_add_entry("/dns4", "DNS4");
		$src = XNODE_getpathbytarget($prefix."/dns4", "entry", "uid", $profile, 0);
		if ($src!="" || $dst!="") movc($src, $dst);
	}
}
?>
