<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
$path_wlan1_wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", $path_phyinf_wlan1."/wifi", 0);
$channel=query($path_phyinf_wlan1."/media/channel");
if(query($path_phyinf_wlan1."/active")=="1" && query($path_phyinf_wlan1."/media/channel")=="0")
{
		//update channel value when autochannel setup for HNAP Spec.
		//$channel=query("/runtime/stats/wireless/channel");
		$channel="0";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetWLanSettings24Response xmlns="http://purenetworks.com/HNAP1/">
      <GetWLanSettings24Result>OK</GetWLanSettings24Result>
      <Enabled><?echo map($path_phyinf_wlan1."/active", "1", "true", "*", "false");?></Enabled>
      <MacAddress><?echo query("/runtime/devdata/lanmac");?></MacAddress>
      <SSID><?echo get("x",$path_wlan1_wifi."/ssid");?></SSID>
      <SSIDBroadcast><?echo map($path_wlan1_wifi."/ssidHidden", "1", "false", "*", "true");?></SSIDBroadcast>
      <Channel><?=$channel?></Channel>
    </GetWLanSettings24Response>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
