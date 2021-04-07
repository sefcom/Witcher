<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$wifiverify = get("","/runtime/devdata/wifiverify");
$encr_check_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
$encr_check_wlan2 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2, 0);
$encr_check_wlan3 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN3, 0);
$encr_wifi_wlan1 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($encr_check_wlan1."/wifi"), 0);
$encr_wifi_wlan2 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($encr_check_wlan2."/wifi"), 0);
$encr_wifi_wlan3 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($encr_check_wlan3."/wifi"), 0);
//WPS Enable
if(query($encr_wifi_wlan1."/wps/enable")==1)	{$enable="Enable";}
else	{$enable="Disable";}

//WPS Configured
if(query($encr_wifi_wlan1."/wps/configured")==1 ||
   query($encr_wifi_wlan2."/wps/configured")==1 ||
   query($encr_wifi_wlan3."/wps/configured")==1)	
			{$configured="Configured";}
else	{$configured="Unconfigured";}

//Get Device PIN
$pin = query($encr_wifi_wlan1."/wps/pin");
if ($pin == "")
{
	$pin = query("/runtime/devdata/pin");
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetWiFiVerifyAlphaResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetWPSSettingResult><?=$result?></GetWPSSettingResult>
			<Wifiverify><?=$wifiverify?></Wifiverify>
			<WPS>
				<Enable><?=$enable?></Enable>
				<Configured><?=$configured?></Configured>
				<PIN><?=$pin?></PIN>
			</WPS>
		</GetWiFiVerifyAlphaResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
