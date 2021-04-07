<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

//$WLAN_supported_mode = "WirelessRouter,WirelessAp";
//$WLAN_supported_mode = "WirelessRouter";
if($WLAN_supported_mode=="")
    {$WLAN_supported_mode="WirelessRouter";}

function getWLANBand ($WLANID)
{
	$path_phyinf = XNODE_getpathbytarget("", "phyinf", "uid", $WLANID, 0);
	return query ($path_phyinf."/media/freq");
}
function getWLANMode ($WLANID)
{
	$path_phyinf = XNODE_getpathbytarget("", "phyinf", "uid", $WLANID, 0);
	$path_wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", query($path_phyinf."/wifi"), 0);
	return query ($path_wifi."/opmode");
}
function echoWLANSupportedMode ($WLAN_supported_mode)
{
	echo "\t\t\t\t\<SupportMode\>\n";

	$mode_number = cut_count($WLAN_supported_mode, ",");
	$mode_counter = 0;
	while ($mode_counter < $mode_number)
	{
		echo "\t\t\t\t\t\<string\>".cut($WLAN_supported_mode, $mode_counter, ",")."\</string\>\n";
		$mode_counter ++;
	}

	echo "\t\t\t\t\</SupportMode\>\n";

}
function getwifimode($wifi_id)
{
    //for temp
    if(get("", "/device/layout")=="router")
	{
		$ret = "WirelessRouter";
	}
	else if(get("", "/device/layout")=="bridge")
	{
		if(query("/device/op_mode")=="repeater_ext")
			{$ret="WirelessRepeaterExtender";}
		else
			{$ret = "WirelessBridge";}
	}
	return $ret;
	/*acturally method
	return getWLANMode($wifi_id);
	*/
}

$WLAN1_band = getWLANBand($WLAN1);
if ($WLAN1_band != "")
{
	$RadioID1 = "RADIO_".$WLAN1_band."GHz";
	$WirelessMode1 = getwifimode($WLAN1_band);//"WirelessRouter";
}

$WLAN1_GZ_band = getWLANBand($WLAN1_GZ);
if ($WLAN1_GZ_band != "")
{
	$RadioID1_GZ = "RADIO_".$WLAN1_GZ_band."G_Guest";
	$WirelessMode1_GZ = getwifimode($WLAN1_GZ_band);//"WirelessRouter";
}

$WLAN2_band = getWLANBand($WLAN2);
if ($WLAN2_band != "")
{
	$RadioID2 = "RADIO_".$WLAN2_band."GHz";
	$WirelessMode2 = getwifimode($WLAN2_band);//"WirelessRouter";
}

$WLAN2_GZ_band = getWLANBand($WLAN2_GZ);
if ($WLAN2_GZ_band != "")
{
	$RadioID2_GZ = "RADIO_".$WLAN2_GZ_band."G_Guest";
	$WirelessMode2_GZ = getwifimode($WLAN2_GZ_band);//"WirelessRouter";
}

$WLAN3_band = getWLANBand($WLAN3);
if ($WLAN3_band != "")
{
	$RadioID3 = "RADIO_".$WLAN3_band."GHz_2";
	$WirelessMode3 = getwifimode($WLAN3_band);//"WirelessRouter";
}

$WLAN3_GZ_band = getWLANBand($WLAN3_GZ);
if ($WLAN3_GZ_band != "")
{
	$RadioID3_GZ = "RADIO_".$WLAN3_GZ_band."GHz_2_Guest";
	$WirelessMode3_GZ = getwifimode($WLAN3_GZ_band);//"WirelessRouter";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetWirelessModeResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetWirelessModeResult>OK</GetWirelessModeResult>
<?

if ($RadioID1 != "")
{
	echo "\t\t\t\<WirelessModeList\>\n";

	echo "\t\t\t\t\<RadioID\>".$RadioID1."\</RadioID\>\n";
	echo "\t\t\t\t\<WirelessMode1\>".$WirelessMode1."\</WirelessMode1\>\n";

	echoWLANSupportedMode($WLAN_supported_mode);

	echo "\t\t\t\</WirelessModeList\>\n";
}
if ($RadioID1_GZ != "")
{
	echo "\t\t\t\<WirelessModeList\>\n";

	echo "\t\t\t\t\<RadioID\>".$RadioID1_GZ."\</RadioID\>\n";
	echo "\t\t\t\t\<WirelessMode1\>".$WirelessMode1_GZ."\</WirelessMode1\>\n";

	echoWLANSupportedMode($WLAN_supported_mode);

	echo "\t\t\t\</WirelessModeList\>\n";
}
if ($RadioID2 != "")
{
	echo "\t\t\t\<WirelessModeList\>\n";

	echo "\t\t\t\t\<RadioID\>".$RadioID2."\</RadioID\>\n";
	echo "\t\t\t\t\<WirelessMode1\>".$WirelessMode2."\</WirelessMode1\>\n";

	echoWLANSupportedMode($WLAN_supported_mode);

	echo "\t\t\t\</WirelessModeList\>\n";
}
if ($RadioID2_GZ != "")
{
	echo "\t\t\t\<WirelessModeList\>\n";

	echo "\t\t\t\t\<RadioID\>".$RadioID2_GZ."\</RadioID\>\n";
	echo "\t\t\t\t\<WirelessMode1\>".$WirelessMode2_GZ."\</WirelessMode1\>\n";

	echoWLANSupportedMode($WLAN_supported_mode);

	echo "\t\t\t\</WirelessModeList\>\n";
}
if ($RadioID3 != "")
{
	echo "\t\t\t\<WirelessModeList\>\n";

	echo "\t\t\t\t\<RadioID\>".$RadioID3."\</RadioID\>\n";
	echo "\t\t\t\t\<WirelessMode1\>".$WirelessMode3."\</WirelessMode1\>\n";

	echoWLANSupportedMode($WLAN_supported_mode);

	echo "\t\t\t\</WirelessModeList\>\n";
}
if ($RadioID3_GZ != "")
{
	echo "\t\t\t\<WirelessModeList\>\n";

	echo "\t\t\t\t\<RadioID\>".$RadioID3_GZ."\</RadioID\>\n";
	echo "\t\t\t\t\<WirelessMode1\>".$WirelessMode3_GZ."\</WirelessMode1\>\n";

	echoWLANSupportedMode($WLAN_supported_mode);

	echo "\t\t\t\</WirelessModeList\>\n";
}


?>		</GetWirelessModeResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
