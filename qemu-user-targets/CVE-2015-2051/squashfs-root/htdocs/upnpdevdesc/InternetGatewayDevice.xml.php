<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/xnode.php";

$ipaddr = INF_getcurripaddr($INF);

/* check ipv6, sam_pan */
$inf_path = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$inet     = query($inf_path."/inet");
$inetp    = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
$addrtype = query($inetp."/addrtype");

if($addrtype == "ipv6") 
{ 
	$ipaddr = "[".$ipaddr."]"; 
	$dtype = "urn:schemas-upnp-org:device:InternetGatewayDevice:2";
}
else
{
	$dtype = "urn:schemas-upnp-org:device:InternetGatewayDevice:1";
}	


$dpath = XNODE_getpathbytarget("/runtime/upnp", "dev", "deviceType", $dtype, 0);
if ($dpath == "") exit;

anchor($dpath);
$port	  = query("port");
set("devdesc/URLBase",					"http://".$ipaddr.":".$port);
set("devdesc/device/presentationURL",	"http://".$ipaddr);

echo "\<\?xml version=\"1.0\"\?\>\n";
echo "<root xmlns=\"urn:schemas-upnp-org:device-1-0\">\n";
echo dump(1, "devdesc");
echo "</root>\n";
?>
