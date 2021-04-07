<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/trace.php"; 

/*------------------------------------------------------
	site survey path would be different because of vendor,
	there are two ways we should support:
		
		1. ex, realtek
				- (2.4G)	/runtime/wifi_tmpnode/sitesurvey_24G
				- (5G)		/runtime/wifi_tmpnode/sitesurvey_5G
		
		2. ex, boardcom
				- (both)	/runtime/wifi_tmpnode/sitesurvey
		
------------------------------------------------------*/

function SiteSurveyInfo($path)
{
	anchor($path);
	
	if(strip(get("x","ssid"))=="") {break;} //Don't show the wireless information if it's SSID is empty or blank.

	$signalstength=get("x","rssi");
	if($signalstength < 0)
	{
		$signalstength=$signalstength+100;
		$signalstength=$signalstength*2;
		if($signalstength < 0) {$signalstength=0;}
		else if($signalstength > 100)	{$signalstength=100;}
	}
	
	echo "					<APStatInfo>\n";
	echo "						<SSID>".get("x","ssid")."</SSID>\n";
	echo "						<Channel>".get("x","channel")."</Channel>\n";
	echo "						<SignalStrength>".$signalstength."</SignalStrength>\n";
	echo "						<MacAddress>".get("x","macaddr")."</MacAddress>\n";
	echo "						<SupportedSecurity>\n";
	echo "							<SecurityInfo>\n";
	
	$authtype = get("x","authtype");
	$encrtype = get("x","encrtype");
	
	if($encrtype!="NONE")
	{
		if($encrtype=="WEP") /* WEP */
		{
			$encr_string = "";
			if( $authtype == "SHARED" )
			{
				$type = "WEP-SHARED";
			}
			else if( $authtype == "OPEN" || $authtype == "WEPAUTO" )
			{
				$type = "WEP-AUTO";
			}
		}
		else if(strstr($authtype, "WPA")!="") /* WPA */
		{
			$encr_string="unset";
			$encr_string2="";
			if($authtype=="WPA") {$type="WPA-RADIUS";}
			else if($authtype=="WPA2") {$type="WPA2-RADIUS";}
			else if($authtype=="WPA+2") {$type="WPAORWPA2-RADIUS";}
			else if($authtype=="WPAPSK") {$type="WPA-PSK";}
			else if($authtype=="WPA2PSK") {$type="WPA2-PSK";}
			else if($authtype=="WPA+2PSK") {$type="WPAORWPA2-PSK";}
			else { TRACE_error("Unexpected authtype:".$authtype." with ssid=".get("x","ssid")); }
			
			if($encrtype=="TKIP") {$encr_string="TKIP";}
			else if($encrtype=="AES") {$encr_string="AES";}
			else if($encrtype=="TKIP+AES" || $encrtype=="TKIPAES")
			{
				$encr_string="TKIP";
				$encr_string2="AES";
			}
			else { $result = "ERROR"; }
		}
	}
	else /* NONE */
	{
		$type = "NONE";
		$encr_string = "";
	}
	
	echo "						<SecurityType>".$type."</SecurityType>\n";
	echo "						<Encryptions>\n";
	echo "							<string>".$encr_string."</string>\n";
	if($encr_string2!="")
	{
	    echo "					<string>".$encr_string2."</string>\n";
	}
	echo "							</Encryptions>\n";
	echo "							</SecurityInfo>\n";
	echo "						</SupportedSecurity>\n";
	echo "					</APStatInfo>\n";
}

$RadioID = get("","/runtime/hnap/GetSiteSurvey/RadioID");
$sitesurvey_node = "/runtime/wifi_tmpnode";

$result = "OK";

if($RadioID=="2.4GHZ" || $RadioID=="RADIO_24GHz" || $RadioID=="RADIO_2.4GHz")
{
	$sitesurvey_path = $sitesurvey_node."/sitesurvey_24G/entry";
	$mode = "2.4G";
}
else if($RadioID=="5GHZ" || $RadioID=="RADIO_5GHz")
{
	$sitesurvey_path = $sitesurvey_node."/sitesurvey_5G/entry";
	$mode = "5G";
}
else
{
	$result = "ERROR_BAD_BandID";
}

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetSiteSurveyResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetSiteSurveyResult><?=$result?></GetSiteSurveyResult>
			<APStatInfoLists>
				<?
					if($result == "OK")
					{
						if(query($sitesurvey_path."/ssid")=="" && query($sitesurvey_path."/macaddr")=="") //if the path is not exist
						{
							$sitesurvey_path = $sitesurvey_node."/sitesurvey/entry";
							foreach($sitesurvey_path)
							{
								$wlmode = query("wlmode");
								if($wlmode==$mode)
								{
									SiteSurveyInfo($sitesurvey_path.":".$InDeX);
								}
							}
						}
						else
						{
							foreach($sitesurvey_path)
							{
								SiteSurveyInfo($sitesurvey_path.":".$InDeX);
							}
						}
					}
				?>
			</APStatInfoLists>
		</GetSiteSurveyResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>