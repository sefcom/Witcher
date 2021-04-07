HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

include "/htdocs/phplib/encrypt.php";
$nodebase = "/runtime/hnap/SetIPv6PppoeSettings/";

$ConnectionType                 = get("",$nodebase."IPv6_ConnectionType");

$PppoeNewSession                = get("",$nodebase."IPv6_PppoeNewSession");
$Pppoetype                      = get("",$nodebase."IPv6_PppoeType");
$Address                        = get("",$nodebase."IPv6_PppoeStaticIp");
$PppoeUsername                  = get("",$nodebase."IPv6_PppoeUsername");
$PppoePassword                  = get("",$nodebase."IPv6_PppoePassword");
$PppoePassword                  = AES_Decrypt128($PppoePassword);
$PppoeReconnectMode             = get("",$nodebase."IPv6_PppoeReconnectMode");
$PppoeMaxIdleTime               = get("",$nodebase."IPv6_PppoeMaxIdleTime");
$PppoeMTU                       = get("",$nodebase."IPv6_PppoeMTU");
$PppoeServiceName               = get("",$nodebase."IPv6_PppoeServiceName");
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


if($ConnectionType == "IPv6_PPPoE")
{
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";
	
	//TRACE_info("==[Set IPv6 Pppoe Setting.php] === END");
}

?>
