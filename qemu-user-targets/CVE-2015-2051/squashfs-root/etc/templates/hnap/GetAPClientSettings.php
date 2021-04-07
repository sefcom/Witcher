<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/encrypt.php";

$radioID = get("","/runtime/hnap/GetAPClientSettings/RadioID");
$result = "OK";

if($radioID == "RADIO_2.4GHz")
{
	$wlan_uid = $WLAN_APCLIENT;
}
else if($radioID == "RADIO_5GHz")
{
	$wlan_uid = $WLAN2_APCLIENT;
}
else
{
	$wlan_uid = $WLAN_APCLIENT;
}

$path_phyinf_wlan = XNODE_getpathbytarget("", "phyinf", "uid", $wlan_uid, 0);
$path_wlan_wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", query($path_phyinf_wlan."/wifi"), 0);
$path_runtime_phyinf_wlan = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $wlan_uid, 0);

TRACE_debug("path_phyinf_wlan=".$path_phyinf_wlan);
TRACE_debug("path_wlan_wifi=".$path_wlan_wifi);

if(get("", $path_phyinf_wlan."/active")=="1")	{$Enabled = "true";}
else	{$Enabled = "false";}
$SSID = get("", $path_wlan_wifi."/ssid");
$MacAddress = get("", $path_runtime_phyinf_wlan."/macaddr");
$ChannelWidth = "0";//Auto
$SecurityType = get("", $path_wlan_wifi."/authtype");
$Encryptions_string = get("", $path_wlan_wifi."/encrtype");
if(strstr(get("", $path_wlan_wifi."/authtype"), "WPA") != "")
{$Key = get("", $path_wlan_wifi."/nwkey/psk/key");}
else if(strstr(get("", $path_wlan_wifi."/encrtype"), "WEP") != "")
{
    if($SecurityType=="SHARED") { $SecurityType="WEP-SHARED"; }
    else { $SecurityType="WEPAUTO"; }
    $Key = get("", $path_wlan_wifi."/nwkey/wep/key");
}
else
{$Key = "";}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetAPClientSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetAPClientSettingsResult><?=$result?></GetAPClientSettingsResult>
			<Enabled><?=$Enabled?></Enabled>
			<SSID><?=$SSID?></SSID>
			<MacAddress><?=$MacAddress?></MacAddress>
			<ChannelWidth><?=$ChannelWidth?></ChannelWidth>
			<SupportedSecurity>
				<SecurityInfo>
					<SecurityType><?=$SecurityType?></SecurityType>
					<Encryptions>
						<string><?=$Encryptions_string?></string>
					</Encryptions>
				</SecurityInfo>
			</SupportedSecurity>
			<Key><? echo AES_Encrypt128($Key); ?></Key>
		</GetAPClientSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
