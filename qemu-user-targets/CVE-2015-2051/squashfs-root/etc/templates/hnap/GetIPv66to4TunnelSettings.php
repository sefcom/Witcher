<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype == "6TO4") $rlt = "True";
else $rlt = "False";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_6To4")
		{
			echo '	<GetIPv66to4TunnelSettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv66to4TunnelSettingsResult>".$result."</GetIPv66to4TunnelSettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";
			echo "			<IPv6_6To4Address>".$6To4Address."</IPv6_6To4Address>\n";			                 
			echo "			<IPv6_6To4Relay>".$6To4Relay."</IPv6_6To4Relay>\n";
			echo "			<IPv6_PrimaryDNS>".$PrimaryDNS."</IPv6_PrimaryDNS>\n";
			echo "			<IPv6_SecondaryDNS>".$SecondaryDNS."</IPv6_SecondaryDNS>\n";
			echo "			<IPv6_6to4LanAddress>".$slaid."</IPv6_6to4LanAddress>\n";
			echo "			<IPv6_LanLinkLocalAddress>".$LanLinkLocalAddress."</IPv6_LanLinkLocalAddress>\n";		
			echo "			<IPv6_LanIPv6AddressAutoAssignment>".$LanIPv6AddressAutoAssignment."</IPv6_LanIPv6AddressAutoAssignment>\n";
			echo "			<IPv6_LanAutoConfigurationType>".$LanAutoConfigurationType."</IPv6_LanAutoConfigurationType>\n";
			echo "			<IPv6_LanIPv6AddressRangeStart>".$LanIPv6AddressRangeStart."</IPv6_LanIPv6AddressRangeStart>\n";
			echo "			<IPv6_LanIPv6AddressRangeEnd>".$LanIPv6AddressRangeEnd."</IPv6_LanIPv6AddressRangeEnd>\n";
			echo "			<IPv6_LanDhcpLifeTime>".$LanDhcpLifeTime."</IPv6_LanDhcpLifeTime>\n";		
			echo "			<IPv6_LanRouterAdvertisementLifeTime>".$LanRouterAdvertisementLifeTime."</IPv6_LanRouterAdvertisementLifeTime>\n";
			echo '	</GetIPv66to4TunnelSettingsResponse>\n';
		}	
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>