<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

/* get upnp device node path by device type */
function UPNP_getdevpathbytype($inf, $type)
{
	/* check this interface supports the upnp device.*/
	$inf_path = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	if ($inf_path == "") return "";
	$count = query($inf_path."/upnp/count");
	$i = 0;
	while ($i < $count)
	{
		$i++;
		if (query($inf_path."/upnp/entry:".$i) == $type)
			return XNODE_getpathbytarget("/runtime/upnp", "dev", "deviceType", $type, 0);
	}
	return "";
}

function UPNP_getudnbytype($inf, $type)
{
	$p = UPNP_getdevpathbytype($inf, $type);
	if ($p == "") return "";
	return query($p."/UDN");
}
?>
