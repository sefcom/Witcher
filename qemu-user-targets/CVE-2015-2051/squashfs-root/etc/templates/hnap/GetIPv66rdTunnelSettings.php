<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype == "6RD") $rlt = "True";
else $rlt = "False";

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_6RD")
		{
			echo '	<GetIPv66rdTunnelSettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv66rdTunnelSettingsResult>".$result."</GetIPv66rdTunnelSettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";
			echo "			<IPv6_6rdHubSpokeMode>".$6Rd_Hub_Spoke."</IPv6_6rdHubSpokeMode>\n";			               
			echo "			<IPv6_6Rd_Configuration>".$6Rd_Configuration."</IPv6_6Rd_Configuration>\n";
			echo "			<IPv6_6Rd_IPv6Prefix>".$6Rd_IPv6Prefix."</IPv6_6Rd_IPv6Prefix>\n";
			echo "			<IPv6_6Rd_IPv6PrefixLength>".$6Rd_IPv6PrefixLength."</IPv6_6Rd_IPv6PrefixLength>\n";
			echo "			<IPv6_6Rd_MaskLength>".$6Rd_IPv4MaskLength."</IPv6_6Rd_MaskLength>\n";	
			echo "			<IPv6_6Rd_BorderRelayIPv4Address>".$6Rd_BorderRelayIPv4Address."</IPv6_6Rd_BorderRelayIPv4Address>\n";	
			echo "			<IPv6_6Rd_AssignedIPv6Prefix>".$IPv6_6rd_assigned_prefix."</IPv6_6Rd_AssignedIPv6Prefix>\n";
			echo "			<IPv6_PrimaryDNS>".$PrimaryDNS."</IPv6_PrimaryDNS>\n";
			echo "			<IPv6_SecondaryDNS>".$SecondaryDNS."</IPv6_SecondaryDNS>\n";
			echo "			<IPv6_LanAddress>".$6Rd_LanAddress."</IPv6_LanAddress>\n";	
			echo "			<IPv6_LanLinkLocalAddress>".$LanLinkLocalAddress."</IPv6_LanLinkLocalAddress>\n";		
			echo "			<IPv6_LanIPv6AddressAutoAssignment>".$LanIPv6AddressAutoAssignment."</IPv6_LanIPv6AddressAutoAssignment>\n";
			echo "			<IPv6_LanAutoConfigurationType>".$LanAutoConfigurationType."</IPv6_LanAutoConfigurationType>\n";
			echo "			<IPv6_LanIPv6AddressRangeStart>".$LanIPv6AddressRangeStart."</IPv6_LanIPv6AddressRangeStart>\n";
			echo "			<IPv6_LanIPv6AddressRangeEnd>".$LanIPv6AddressRangeEnd."</IPv6_LanIPv6AddressRangeEnd>\n";
			echo '	</GetIPv66rdTunnelSettingsResponse>\n';
		}	
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>