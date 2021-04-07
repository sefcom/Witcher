<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype == "6IN4") $rlt = "True";
else $rlt = "False";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_IPv6InIPv4Tunnel")
		{
			echo '	<GetIPv66in4TunnelSettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv66in4TunnelSettingsResult>".$result."</GetIPv66in4TunnelSettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";
			echo "			<IPv6_6In4RemoteIPv4Address>".$6In4RemoteIPv4Address."</IPv6_6In4RemoteIPv4Address>\n";
			echo "			<IPv6_6In4RemoteIPv6Address>".$6In4RemoteIPv6Address."</IPv6_6In4RemoteIPv6Address>\n";
			echo "			<IPv6_6In4LocalIPv4Address>".$6In4LocalIPv4Address."</IPv6_6In4LocalIPv4Address>\n";
			echo "			<IPv6_6In4LocalIPv6Address>".$6In4LocalIPv6Address."</IPv6_6In4LocalIPv6Address>\n";
			echo "			<IPv6_SubnetPrefixLength>".$6In4SubnetPrefixLength."</IPv6_SubnetPrefixLength>\n";			
			echo "			<IPv6_ObtainDNS>".$ObtainDNS."</IPv6_ObtainDNS>\n";
			echo "			<IPv6_PrimaryDNS>".$PrimaryDNS."</IPv6_PrimaryDNS>\n";
			echo "			<IPv6_SecondaryDNS>".$SecondaryDNS."</IPv6_SecondaryDNS>\n";
			echo "			<IPv6_DhcpPd>".$DhcpPd."</IPv6_DhcpPd>\n";
			echo "			<IPv6_LanAddress>".$LanAddress."</IPv6_LanAddress>\n";			
			echo "			<IPv6_LanLinkLocalAddress>".$LanLinkLocalAddress."</IPv6_LanLinkLocalAddress>\n";			
			echo "			<IPv6_LanIPv6AddressAutoAssignment>".$LanIPv6AddressAutoAssignment."</IPv6_LanIPv6AddressAutoAssignment>\n";
			echo "			<IPv6_LanAutomaticDhcpPd>".$LanAutomaticDhcpPd."</IPv6_LanAutomaticDhcpPd>\n";
			echo "			<IPv6_LanAutoConfigurationType>".$LanAutoConfigurationType."</IPv6_LanAutoConfigurationType>\n";
			echo "			<IPv6_LanRouterAdvertisementLifeTime>".$LanRouterAdvertisementLifeTime."</IPv6_LanRouterAdvertisementLifeTime>\n";
			echo "			<IPv6_LanIPv6AddressRangeStart>".$LanIPv6AddressRangeStart."</IPv6_LanIPv6AddressRangeStart>\n";
			echo "			<IPv6_LanIPv6AddressRangeEnd>".$LanIPv6AddressRangeEnd."</IPv6_LanIPv6AddressRangeEnd>\n";
			echo "			<IPv6_LanDhcpLifeTime>".$LanDhcpLifeTime."</IPv6_LanDhcpLifeTime>\n";
			echo '	</GetIPv66in4TunnelSettingsResponse>\n';
		}	
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>