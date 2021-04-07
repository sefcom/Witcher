<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]	= $result;
	$_GLOBALS["FATLADY_node"]	= $node;
	$_GLOBALS["FATLADY_message"]= $message;
}



function fatlady_phyinf($prefix, $inf)
{
	/* Check the interface setting */
	if (query($prefix."/inf/uid") != $inf)
	{
		/* internet error, no i18n(). */
		set_result("FAILED", $prefix."/inf/uid", "INF UID mismatch");
		return;
	}

	/* Check PHYINF */
	$phy = query($prefix."/inf/phyinf");
	$phyp = XNODE_getpathbytarget($prefix, "phyinf", "uid", $phy, 0);
	if ($phy == "" || $phyp == "")
	{
		/* internet error, no i18n(). */
		set_result("FAILED", $prefix."/inf/phyinf", "Invalid phyinf");
		return;
	}

	/* Check MACADDR */
	$macaddr = query($phyp."/macaddr");
	if ($macaddr != "" && PHYINF_validmacaddr($macaddr) != "1")
	{
		set_result("FAILED", $phyp."/macaddr", i18n("Invalid MAC address"));
		return;
	}

	$type = query($phyp."/type");
	if ($type == "eth")
	{
		$media = query($phyp."/media/linktype");
		if ($media != "" && $media!="AUTO" && $media!="100F" &&
			$media!="100H" && $media!="10F" && $media!="10H")
		{
			set_result("FAILED", $phyp."/media/linktype", i18n("Invalid media type"));
			return;
		}
	}

	/* We only validate the 'macaddr' & 'media' here,
	 * so be sure to save 'macaddr' & 'media' only at 'setcfg' */
	set($prefix."/valid", 1);
	set_result("OK", "", "");
}

?>
