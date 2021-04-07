<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/xnode.php";

$dtype = "urn:schemas-wifialliance-org:device:WFADevice:1";
$dpath = XNODE_getpathbytarget("/runtime/upnp", "dev", "deviceType", $dtype, 0);
if ($dpath == "") exit;

anchor($dpath);
$ipaddr	= INF_getcurripaddr($INF);
$port	= query("port");
set("devdesc/URLBase", 					"http://".$ipaddr.":".$port);
set("devdesc/device/presentationURL",	"http://".$ipaddr);

echo "<\?xml version=\"1.0\" encoding=\"utf-8\"?\>\n";
echo "<root xmlns=\"urn:schemas-upnp-org:device-1-0\">\n";
echo dump(1, "devdesc");
echo "</root>\n";
?>
