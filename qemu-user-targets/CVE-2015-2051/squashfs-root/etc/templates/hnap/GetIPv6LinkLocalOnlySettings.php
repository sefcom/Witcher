<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "etc/templates/hnap/GetIPv6Settings.php";

if($str_wantype == "Link-Local") $rlt = "True";
else $rlt = "False";

//TRACE_debug("  [GetIPv6 LLOnlySettings.php] WAN_Type = ".$str_wantype);
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<?
		if($ConnectionType=="IPv6_LinkLocalOnly")
		{
			echo '	<GetIPv6LinkLocalOnlySettingsResponse xmlns="http://purenetworks.com/HNAP1/">\n';
			echo "			<GetIPv6LinkLocalOnlySettingsResult>".$result."</GetIPv6LinkLocalOnlySettingsResult>\n";
			echo "			<IPv6_IsCurrentConnectionType>".$rlt."</IPv6_IsCurrentConnectionType>\n";
			echo "			<IPv6_LanUniqueLocalAddressStatus>".$en_ula."</IPv6_LanUniqueLocalAddressStatus>\n";
			echo "			<IPv6_LanUniqueLocalAddressDefaultPrefix>".$use_default_ula."</IPv6_LanUniqueLocalAddressDefaultPrefix>\n";
			echo "			<IPv6_LanUniqueLocalAddressPrefix>".$ula_prefix."</IPv6_LanUniqueLocalAddressPrefix>\n";
			echo "			<IPv6_LanUniqueLocalAddressPrefixLength>".$ula_prelen."</IPv6_LanUniqueLocalAddressPrefixLength>\n";
			echo "			<IPv6_LanUniqueLocalAddress>".$ula_addr."</IPv6_LanUniqueLocalAddress>\n";
			echo '	</GetIPv6LinkLocalOnlySettingsResponse>\n';
		}
	?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>