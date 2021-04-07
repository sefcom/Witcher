<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function fatlady_dhcps($prefix, $svc)
{
	$service = cut($svc, 0, ".");
	$version = scut($service, 0, "DHCPS");
	XNODE_set_var("FATLADY_DHCPS_PATH", $prefix);
	XNODE_set_var("SERVICE_NAME", $svc);
	$b = "/htdocs/phplib/fatlady/DHCPS";
	if		($version == 4) dophp("load", $b."/dhcps4.php");
	else if	($version == 6) dophp("load", $b."/dhcps6.php");
	else
	{
		$_GLOBALS["FATLADY_result"]  = "FAILED";
		$_GLOBALS["FATLADY_node"]    = "";
		$_GLOBALS["FATLADY_message"] = "Unsupported DHCP service : ".$svc; /* internal error, no i18n(). */
	}
	XNODE_del_var("FATLADY_DHCPS_PATH");
	XNODE_del_var("SERVICE_NAME");
	if ($_GLOBALS["FATLADY_result"]=="OK") set($prefix."/valid", 1);
}

?>
