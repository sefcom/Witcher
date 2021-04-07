<NewDefaultConnectionService><?
include "/htdocs/phplib/xnode";
include "/htdocs/upnpinc/gvar.php";

$nodebase = XNODE_getpathbytarget("/runtime/upnp", "dev", "deviceType", $G_IGD, 0);
echo query($nodebase."/UDN").":WANConnectionDevice:1,";
echo query($nodebase."/devdesc/device/serviceList/service:2/serviceId");

?></NewDefaultConnectionService>
