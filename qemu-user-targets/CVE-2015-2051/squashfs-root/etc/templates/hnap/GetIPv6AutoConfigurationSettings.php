<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype=="AUTO" || $str_wantype=="DHCPv6" || $str_wantype=="SLAAC" || $str_wantype=="Autoconfiguration")
	$rlt = "True";
else $rlt = "False";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_AutoConfiguration")
		{
			echo '	<GetIPv6AutoConfigurationSettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv6AutoConfigurationSettingsResult>".$result."</GetIPv6AutoConfigurationSettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";
			echo "			<IPv6_ObtainDNS>".$ObtainDNS."</IPv6_ObtainDNS>\n";
			echo "			<IPv6_PrimaryDNS>".$PrimaryDNS."</IPv6_PrimaryDNS>\n";
			echo "			<IPv6_SecondaryDNS>".$SecondaryDNS."</IPv6_SecondaryDNS>\n";
			echo "			<IPv6_DhcpPd>".$DhcpPd."</IPv6_DhcpPd>\n";
			echo "			<IPv6_LanAddress>".$LanAddress."</IPv6_LanAddress>\n";
			echo "			<IPv6_LanIPv6AddressAutoAssignment>".$LanIPv6AddressAutoAssignment."</IPv6_LanIPv6AddressAutoAssignment>\n";
			echo "			<IPv6_LanAutomaticDhcpPd>".$LanAutomaticDhcpPd."</IPv6_LanAutomaticDhcpPd>\n";
			echo "			<IPv6_LanAutoConfigurationType>".$LanAutoConfigurationType."</IPv6_LanAutoConfigurationType>\n";
			echo "			<IPv6_LanRouterAdvertisementLifeTime>".$LanRouterAdvertisementLifeTime."</IPv6_LanRouterAdvertisementLifeTime>\n";
			echo "			<IPv6_LanIPv6AddressRangeStart>".$LanIPv6AddressRangeStart."</IPv6_LanIPv6AddressRangeStart>\n";
			echo "			<IPv6_LanIPv6AddressRangeEnd>".$LanIPv6AddressRangeEnd."</IPv6_LanIPv6AddressRangeEnd>\n";
			echo "			<IPv6_LanDhcpLifeTime>".$LanDhcpLifeTime."</IPv6_LanDhcpLifeTime>\n";
			echo "			<IPv6_LanLinkLocalAddress>".$LanLinkLocalAddress."</IPv6_LanLinkLocalAddress>\n";
			echo '	</GetIPv6AutoConfigurationSettingsResponse>\n';
		}	
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>