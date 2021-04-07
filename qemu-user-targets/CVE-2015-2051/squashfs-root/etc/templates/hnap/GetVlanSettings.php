<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$vlan_active = query("/device/vlan/active");
$interid_pppoe = query("/device/vlan/interid_pppoe");
$voipid_pppoe = query("/device/vlan/voipid_pppoe");
$iptvid_pppoe = query("/device/vlan/iptvid_pppoe");
$interid_dhcp = query("/device/vlan/interid_dhcp");
$voipid_dhcp = query("/device/vlan/voipid_dhcp");
$iptvid_dhcp = query("/device/vlan/iptvid_dhcp");
$lan1_pppoe = query("/device/vlan/lanport/lan1_pppoe");
$lan2_pppoe = query("/device/vlan/lanport/lan2_pppoe");
$lan3_pppoe = query("/device/vlan/lanport/lan3_pppoe");
$lan4_pppoe = query("/device/vlan/lanport/lan4_pppoe");
$lan1_dhcp = query("/device/vlan/lanport/lan1_dhcp");
$lan2_dhcp = query("/device/vlan/lanport/lan2_dhcp");
$lan3_dhcp = query("/device/vlan/lanport/lan3_dhcp");
$lan4_dhcp = query("/device/vlan/lanport/lan4_dhcp");
$wlan01_pppoe = query("/device/vlan/wlanport/wlan01_pppoe");
$wlan02_pppoe = query("/device/vlan/wlanport/wlan02_pppoe");
$wlan01_dhcp = query("/device/vlan/wlanport/wlan01_dhcp");
$wlan02_dhcp = query("/device/vlan/wlanport/wlan02_dhcp");

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetVlanSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetVlanSettingsResult>OK</GetVlanSettingsResult>
		<active><?=$vlan_active?></active>
		<interid_pppoe><?=$interid_pppoe?></interid_pppoe>
		<voipid_pppoe><?=$voipid_pppoe?></voipid_pppoe>
		<iptvid_pppoe><?=$iptvid_pppoe?></iptvid_pppoe>
		<interid_dhcp><?=$interid_dhcp?></interid_dhcp>
		<voipid_dhcp><?=$voipid_dhcp?></voipid_dhcp>
		<iptvid_dhcp><?=$iptvid_dhcp?></iptvid_dhcp>
		<lanport>
			<lan1_pppoe><?=$lan1_pppoe?></lan1_pppoe>
			<lan2_pppoe><?=$lan2_pppoe?></lan2_pppoe>
			<lan3_pppoe><?=$lan3_pppoe?></lan3_pppoe>
			<lan4_pppoe><?=$lan4_pppoe?></lan4_pppoe>
			<lan1_dhcp><?=$lan1_dhcp?></lan1_dhcp>
			<lan2_dhcp><?=$lan2_dhcp?></lan2_dhcp>
			<lan3_dhcp><?=$lan3_dhcp?></lan3_dhcp>
			<lan4_dhcp><?=$lan4_dhcp?></lan4_dhcp>
		</lanport>
		<wlanport>
			<wlan01_pppoe><?=$wlan01_pppoe?></wlan01_pppoe>
			<wlan02_pppoe><?=$wlan02_pppoe?></wlan02_pppoe>
			<wlan01_dhcp><?=$wlan01_dhcp?></wlan01_dhcp>
			<wlan02_dhcp><?=$wlan02_dhcp?></wlan02_dhcp>
		</wlanport>
	</GetVlanSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

