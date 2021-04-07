HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetIPv66in4TunnelSettings/";

$ConnectionType                 = get("",$nodebase."IPv6_ConnectionType");
$6In4RemoteIPv4Address          = get("",$nodebase."IPv6_6In4RemoteIPv4Address");
$6In4RemoteIPv6Address          = get("",$nodebase."IPv6_6In4RemoteIPv6Address");
//$6In4LocalIPv4Address         = get("",$nodebase."IPv6_6In4LocalIPv4Address");
$6In4LocalIPv6Address           = get("",$nodebase."IPv6_6In4LocalIPv6Address");
$SubnetPrefixLength             = get("",$nodebase."IPv6_SubnetPrefixLength");
$ObtainDNS                      = get("",$nodebase."IPv6_ObtainDNS");
$PrimaryDNS                     = get("",$nodebase."IPv6_PrimaryDNS");
$SecondaryDNS                   = get("",$nodebase."IPv6_SecondaryDNS");
$DhcpPd                         = get("",$nodebase."IPv6_DhcpPd");
$LanAddress                     = get("",$nodebase."IPv6_LanAddress");
$LanAddressPrefixLength         = "64";
$LanIPv6AddressAutoAssignment   = get("",$nodebase."IPv6_LanIPv6AddressAutoAssignment");
$LanAutomaticDhcpPd             = get("",$nodebase."IPv6_LanAutomaticDhcpPd");
$LanAutoConfigurationType       = get("",$nodebase."IPv6_LanAutoConfigurationType");
$LanRouterAdvertisementLifeTime = get("",$nodebase."IPv6_LanRouterAdvertisementLifeTime");
$LanIPv6AddressRangeStart       = get("",$nodebase."IPv6_LanIPv6AddressRangeStart");
$LanIPv6AddressRangeEnd         = get("",$nodebase."IPv6_LanIPv6AddressRangeEnd");
$LanDhcpLifeTime                = get("",$nodebase."IPv6_LanDhcpLifeTime");


if($ConnectionType == "IPv6_IPv6InIPv4Tunnel")
{
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";
	
	//TRACE_info("==[Set IPv6 6in4 Setting.php] === END");
}

?>
