HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetIPv6AutoDetectionSettings/";

$ConnectionType                 = get("",$nodebase."IPv6_ConnectionType");
$ObtainDNS                      = get("",$nodebase."IPv6_ObtainDNS");
$PrimaryDNS                     = get("",$nodebase."IPv6_PrimaryDNS");
$SecondaryDNS                   = get("",$nodebase."IPv6_SecondaryDNS");
$DhcpPd                         = get("",$nodebase."IPv6_DhcpPd");
$LanAddress                     = get("",$nodebase."IPv6_LanAddress");
$LanIPv6AddressAutoAssignment   = get("",$nodebase."IPv6_LanIPv6AddressAutoAssignment");
$LanAutomaticDhcpPd             = get("",$nodebase."IPv6_LanAutomaticDhcpPd");
$LanAutoConfigurationType       = get("",$nodebase."IPv6_LanAutoConfigurationType");
$LanRouterAdvertisementLifeTime = get("",$nodebase."IPv6_LanRouterAdvertisementLifeTime");
$LanIPv6AddressRangeStart       = get("",$nodebase."IPv6_LanIPv6AddressRangeStart");
$LanIPv6AddressRangeEnd         = get("",$nodebase."IPv6_LanIPv6AddressRangeEnd");
$LanDhcpLifeTime                = get("",$nodebase."IPv6_LanDhcpLifeTime");


if($ConnectionType == "IPv6_AutoDetection")
{
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";
	
	//TRACE_info("==[Set IPv6 AutoDetection Setting.php] === END");
}

?>
