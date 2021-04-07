HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetIPv6AutoConfigurationSettings/";

$ConnectionType                 = get("",$nodebase."IPv6_ConnectionType");
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


if($ConnectionType == "IPv6_AutoConfiguration")
{
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetIPv6AutoConfigurationSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetIPv6AutoConfigurationSettingsResult><?=$result?></SetIPv6AutoConfigurationSettingsResult>
		</SetIPv6AutoConfigurationSettingsResponse>
	</soap:Body>
</soap:Envelope>