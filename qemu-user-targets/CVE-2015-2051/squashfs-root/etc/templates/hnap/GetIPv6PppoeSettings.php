<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/encrypt.php";
include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype == "PPPoE") $rlt = "True";
else $rlt = "False";

//TRACE_debug("  [GetIPv6 PppoeSettings.php] WAN_Type = ".$str_wantype);
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_PPPoE")
		{
			echo '	<GetIPv6PppoeSettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv6PppoeSettingsResult>".$result."</GetIPv6PppoeSettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";
			echo "			<IPv6_PppoeNewSession>".$PppoeNewSession."</IPv6_PppoeNewSession>\n";
			echo "			<IPv6_PppoeType>".$Pppoetype."</IPv6_PppoeType>\n";
			echo "			<IPv6_PppoeStaticIp>".$Address."</IPv6_PppoeStaticIp>\n";
			echo "			<IPv6_PppoeUsername>".$PppoeUsername."</IPv6_PppoeUsername>\n";
			echo "			<IPv6_PppoePassword>".AES_Encrypt128($PppoePassword)."</IPv6_PppoePassword>\n";
			echo "			<IPv6_PppoeReconnectMode>".$PppoeReconnectMode."</IPv6_PppoeReconnectMode>\n";
			echo "			<IPv6_PppoeMaxIdleTime>".$PppoeMaxIdleTime."</IPv6_PppoeMaxIdleTime>\n";
			echo "			<IPv6_PppoeMTU>".$PppoeMTU."</IPv6_PppoeMTU>\n";
			echo "			<IPv6_PppoeServiceName>".$PppoeServiceName."</IPv6_PppoeServiceName>\n";
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
			echo '	</GetIPv6PppoeSettingsResponse>\n';
		}	
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>