<?
include "/htdocs/phplib/xnode.php";
/* set the preferred ssid for WPS registrar. (self-config) */
$lanmac = query("/runtime/devdata/lanmac");
$n5 = cut($lanmac, 4, ":");
$n6 = cut($lanmac, 5, ":");
$ssid = "dlink".$n5.$n6;
$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "WLAN-1", 0);
if ($p != "")
{
	set($p."/media/wps/registrar/preferred/ssid", $ssid);
}
?>
