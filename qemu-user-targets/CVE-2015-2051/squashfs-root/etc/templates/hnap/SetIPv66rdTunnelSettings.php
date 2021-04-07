HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetIPv66rdTunnelSettings/";

$ConnectionType               = get("",$nodebase."IPv6_ConnectionType");
$PrimaryDNS                   = get("",$nodebase."IPv6_PrimaryDNS");
$SecondaryDNS                 = get("",$nodebase."IPv6_SecondaryDNS");
$hub_spoke                    = get("",$nodebase."IPv6_6rdHubSpokeMode");
$6Rd_Configuration            = get("",$nodebase."IPv6_6Rd_Configuration");
//$6Rd_IPv4Address            = query($nodebase."IPv6_6Rd_IPv4Address");
$6Rd_IPv6Prefix               = get("",$nodebase."IPv6_6Rd_IPv6Prefix");
$6Rd_IPv6PrefixLength         = get("",$nodebase."IPv6_6Rd_IPv6PrefixLength");
$6Rd_IPv4MaskLength           = get("",$nodebase."IPv6_6Rd_MaskLength");
$6Rd_BorderRelayIPv4Address   = get("",$nodebase."IPv6_6Rd_BorderRelayIPv4Address");
$LanIPv6AddressAutoAssignment = get("",$nodebase."IPv6_LanIPv6AddressAutoAssignment");
$LanAutoConfigurationType     = get("",$nodebase."IPv6_LanAutoConfigurationType");
$LanIPv6AddressRangeStart     = get("",$nodebase."IPv6_LanIPv6AddressRangeStart");
$LanIPv6AddressRangeEnd       = get("",$nodebase."IPv6_LanIPv6AddressRangeEnd");


if($ConnectionType == "IPv6_6RD")
{	
	$result = "OK";
	include "etc/templates/hnap/SetIPv6Settings.php";
	
	//TRACE_info("==[Set IPv6 6rd Setting.php] === END");
}

?>
