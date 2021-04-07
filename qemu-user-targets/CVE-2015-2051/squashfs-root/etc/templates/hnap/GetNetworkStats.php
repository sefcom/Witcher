<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_inf_lan1 = XNODE_getpathbytarget("", "inf", "uid", $LAN1, 0);
$path_run_lan1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", query($path_inf_lan1."/phyinf"));
$path_phy_wifi1 = XNODE_getpathbytarget("", "phyinf", "wifi", "WIFI-1",0);
$path_run_wifi1 = XNODE_getpathbytarget("/runtime", "phyinf", "uid", query($path_phy_wifi1."/uid"));
?>

<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetNetworkStatsResponse xmlns="http://purenetworks.com/HNAP1/">
    <GetNetworkStatsResult>OK</GetNetworkStatsResult>
      <Stats>
<?
anchor($path_run_lan1."/stats");
echo "        <NetworkStats>\n";
echo "          <PortName>LAN</PortName>\n";
echo "          <PacketsReceived>".query("rx/packets")."</PacketsReceived>\n";
echo "          <PacketsSent>".query("tx/packets")."</PacketsSent>\n";
echo "          <BytesReceived>".query("rx/bytes")."</BytesReceived>\n";
echo "          <BytesSent>".query("tx/bytes")."</BytesSent>\n";
echo "        </NetworkStats>\n";

anchor($path_run_wifi1."/stats");
echo "        <NetworkStats>\n";
echo "          <PortName>WLAN 802.11</PortName>\n";
echo "          <PacketsReceived>".query("rx/packets")."</PacketsReceived>\n";
echo "          <PacketsSent>".query("tx/packets")."</PacketsSent>\n";
echo "          <BytesReceived>".query("rx/bytes")."</BytesReceived>\n";
echo "          <BytesSent>".query("tx/bytes")."</BytesSent>\n";
echo "        </NetworkStats>\n";
?>      </Stats>
    </GetNetworkStatsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>