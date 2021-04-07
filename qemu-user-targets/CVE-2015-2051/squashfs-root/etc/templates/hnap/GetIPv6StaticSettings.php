<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype == "Static") $rlt = "True";
else $rlt = "False";

//TRACE_debug("  [GetIPv6 StaticSettings.php] WAN_Type = ".$str_wantype);
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_Static")
		{
			echo '	<GetIPv6StaticSettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv6StaticSettingsResult>".$result."</GetIPv6StaticSettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";		
			echo "			<IPv6_UseLinkLocalAddress>".$UseLinkLocalAddress."</IPv6_UseLinkLocalAddress>\n";
			echo "			<IPv6_Address>".$Address."</IPv6_Address>\n";
			echo "			<IPv6_SubnetPrefixLength>".$SubnetPrefixLength."</IPv6_SubnetPrefixLength>\n";
			echo "			<IPv6_DefaultGateway>".$DefaultGateway."</IPv6_DefaultGateway>\n";
			echo "			<IPv6_PrimaryDNS>".$PrimaryDNS."</IPv6_PrimaryDNS>\n";
			echo "			<IPv6_SecondaryDNS>".$SecondaryDNS."</IPv6_SecondaryDNS>\n";
			echo "			<IPv6_LanAddress>".$LanAddress."</IPv6_LanAddress>\n";
			echo "			<IPv6_LanLinkLocalAddress>".$LanLinkLocalAddress."</IPv6_LanLinkLocalAddress>\n";	
			echo "			<IPv6_LanIPv6AddressAutoAssignment>".$LanIPv6AddressAutoAssignment."</IPv6_LanIPv6AddressAutoAssignment>\n";
			echo "			<IPv6_LanAutoConfigurationType>".$LanAutoConfigurationType."</IPv6_LanAutoConfigurationType>\n";
			echo "			<IPv6_LanRouterAdvertisementLifeTime>".$LanRouterAdvertisementLifeTime."</IPv6_LanRouterAdvertisementLifeTime>\n";
			echo "			<IPv6_LanIPv6AddressRangeStart>".$LanIPv6AddressRangeStart."</IPv6_LanIPv6AddressRangeStart>\n";
			echo "			<IPv6_LanIPv6AddressRangeEnd>".$LanIPv6AddressRangeEnd."</IPv6_LanIPv6AddressRangeEnd>\n";
			echo "			<IPv6_LanDhcpLifeTime>".$LanDhcpLifeTime."</IPv6_LanDhcpLifeTime>\n";		
			echo '	</GetIPv6StaticSettingsResponse>\n';
		}	
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>