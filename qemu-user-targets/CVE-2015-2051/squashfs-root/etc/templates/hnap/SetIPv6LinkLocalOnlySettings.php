HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetIPv6LinkLocalOnlySettings/";

$ConnectionType = get("",$nodebase."IPv6_ConnectionType");

$LanUniqueLocalAddressStatus        = get("",$nodebase."IPv6_LanUniqueLocalAddressStatus");
$LanUniqueLocalAddressDefaultPrefix = get("",$nodebase."IPv6_LanUniqueLocalAddressDefaultPrefix");
$LanUniqueLocalAddressPrefix        = get("",$nodebase."IPv6_LanUniqueLocalAddressPrefix");


if($ConnectionType == "IPv6_LinkLocalOnly")
{
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";		
}

?>
