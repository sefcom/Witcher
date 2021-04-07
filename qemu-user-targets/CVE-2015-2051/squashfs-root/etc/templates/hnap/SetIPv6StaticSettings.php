HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetIPv6StaticSettings/";

$ConnectionType                 = get("",$nodebase."IPv6_ConnectionType");
$UseLinkLocalAddress            = get("",$nodebase."IPv6_UseLinkLocalAddress");
$Address                        = get("",$nodebase."IPv6_Address");
$SubnetPrefixLength             = get("",$nodebase."IPv6_SubnetPrefixLength");
$DefaultGateway                 = get("",$nodebase."IPv6_DefaultGateway");
$PrimaryDNS                     = get("",$nodebase."IPv6_PrimaryDNS");
$SecondaryDNS                   = get("",$nodebase."IPv6_SecondaryDNS");
$LanAddress                     = get("",$nodebase."IPv6_LanAddress");
$LanAddressPrefixLength         = "64";	//+++ HuanYao Kang. The GUI has fixed this value as 64.
$LanIPv6AddressAutoAssignment   = get("",$nodebase."IPv6_LanIPv6AddressAutoAssignment");
$LanAutoConfigurationType       = get("",$nodebase."IPv6_LanAutoConfigurationType");
$LanRouterAdvertisementLifeTime = get("",$nodebase."IPv6_LanRouterAdvertisementLifeTime");
$LanIPv6AddressRangeStart       = get("",$nodebase."IPv6_LanIPv6AddressRangeStart");
$LanIPv6AddressRangeEnd         = get("",$nodebase."IPv6_LanIPv6AddressRangeEnd");
$LanDhcpLifeTime                = get("",$nodebase."IPv6_LanDhcpLifeTime");


if($ConnectionType == "IPv6_Static")
{
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";
	
	//TRACE_info("==[Set IPv6 Static Setting.php] === END");
}

?>
