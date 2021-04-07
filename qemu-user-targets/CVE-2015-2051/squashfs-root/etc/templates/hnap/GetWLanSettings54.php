<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

anchor("/wireless11a");

$channel=query("/wireless11a/channel");
if(query("enable")=="1" && query("autochannel")=="1")
{
		//update channel value when autochannel setup for HNAP Spec.
		//$channel=query("/runtime/stats/wireless11a/channel");
		$channel="0";
}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetWLanSettings54Response xmlns="http://purenetworks.com/HNAP1/">
      <GetWLanSettings54Result>OK</GetWLanSettings54Result>
      <Enabled><?map("enable", "1", "true", "*", "false");?></Enabled>
      <MacAddress><?query("/runtime/devdata/lanmac");?></MacAddress>
      <SSID><?get("x","ssid");?></SSID>
      <SSIDBroadcast><?map("ssidHidden", "1", "false", "*", "true");?></SSIDBroadcast>
      <Channel><?=$channel?></Channel>
    </GetWLanSettings54Response>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
