<? include "/htdocs/phplib/html.php";
include "/htdocs/webinc/config.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$buildver = fread("s", "/etc/config/buildver");
$CurrentMajor = cut($buildver,0,".");
$CurrentMinor = substr(cut($buildver,1,"."), 0, 2);
$CurrentFWVersion = $CurrentMajor.".".$CurrentMinor;

//We could get the file after running checkfw.sh
if(isfile("/tmp/fwinfo.xml")==1)
{
	$LatesMajor = substr(get("","/runtime/firmware/fwversion/Major"), 1, 2);
	$LatesMinor = get("","/runtime/firmware/fwversion/Minor");
	$LatestFirmwareVersion = $LatesMajor.".".$LatesMinor;

	//Show the $LatestFirmwareVersion if it is different from $CurrentFWVersion and $LatesMajor & $LatesMinor are not empty.
	if($LatestFirmwareVersion==$CurrentFWVersion || $LatesMajor=="" || $LatesMinor=="")
	{$LatestFirmwareVersion = "";}

//add by jef to fit hanp spec 1.12
	$fwcheckparameter=query("/device/fwcheckparameter");
	if ($fwcheckparameter != "")
       { $region=$fwcheckparameter; }
	else
       { $region="Default"; }

}
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
    <GetDeviceSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetDeviceSettingsResult>OK</GetDeviceSettingsResult>
      <Type><?
			if (query("/device/layout")=="router") { echo "GatewayWithWiFi"; } 
			else                                   { echo "WiFiAccessPoint"; }
	  ?></Type>
	  	<DeviceModeAlpha><?
	  		$dev_layout = query("/device/layout");
	  		if($dev_layout=="bridge")
	  		{
		  		if($FEATURE_HAVEAPCLIENT==1) { echo "WiFiAPClient"; }
		  		else if($FEATURE_HAVEREPEATER==1) { echo "WiFiRepeater"; }
		  		else { echo "GatewayWithWiFi"; }
		  	}
	  	?></DeviceModeAlpha>
      <DeviceName><? echo get("x","/device/devicename");?></DeviceName><VendorName><? echo get("x","/runtime/device/vendor");?></VendorName>
      <ModelDescription><? echo get("x","/runtime/upnp/dev/devdesc/device/modelDescription");?></ModelDescription>
      <ModelName><? echo get("x","/runtime/device/modelname");?></ModelName>
      <FirmwareVersion><? echo query("/runtime/device/firmwareversion");?></FirmwareVersion>
      <FirmwareRegion><? echo $region;?></FirmwareRegion>
      <HardwareVersion><? echo query("/runtime/device/hardwareversion");?></HardwareVersion>
      <PresentationURL><? echo "http://".get("x","/device/hostname").".local/";?></PresentationURL>
      <CAPTCHA><?if(query("/device/session/captcha")=="1") { echo "true"; } else { echo "false"; }?></CAPTCHA>
      <SharePortStatus><? if(get("","/webaccess/enable")=="1") {echo "true";} else{ echo "false";}?></SharePortStatus>
      <LatestFirmwareVersion><? echo $LatestFirmwareVersion;?></LatestFirmwareVersion>
      <SOAPActions>
	        <string>http://purenetworks.com/HNAP1/GetDeviceSettings</string>
	        <string>http://purenetworks.com/HNAP1/SetDeviceSettings</string>
	        <string>http://purenetworks.com/HNAP1/GetRouterLanSettings</string>
	        <string>http://purenetworks.com/HNAP1/SetRouterLanSettings</string>
	        <string>http://purenetworks.com/HNAP1/GetMACFilters</string>
	        <string>http://purenetworks.com/HNAP1/SetMACFilters</string>
	        <string>http://purenetworks.com/HNAP1/GetMACFilters2</string>
	        <string>http://purenetworks.com/HNAP1/SetMACFilters2</string>
	        <string>http://purenetworks.com/HNAP1/GetWanSettings</string>
	        <string>http://purenetworks.com/HNAP1/SetWanSettings</string>
	        <string>http://purenetworks.com/HNAP1/GetWLanSettings24</string>
	        <string>http://purenetworks.com/HNAP1/SetWLanSettings24</string>
	        <string>http://purenetworks.com/HNAP1/GetWLanSecurity</string>
	        <string>http://purenetworks.com/HNAP1/SetWLanSecurity</string>
	        <string>http://purenetworks.com/HNAP1/GetForwardedPorts</string>
	        <string>http://purenetworks.com/HNAP1/SetForwardedPorts</string>
	        <string>http://purenetworks.com/HNAP1/GetPortMappings</string>
	        <string>http://purenetworks.com/HNAP1/AddPortMapping</string>
	        <string>http://purenetworks.com/HNAP1/DeletePortMapping</string>
	        <string>http://purenetworks.com/HNAP1/Reboot</string>
	        <string>http://purenetworks.com/HNAP1/GetConnectedDevices</string>
	        <string>http://purenetworks.com/HNAP1/RenewWanConnection</string>
	        <string>http://purenetworks.com/HNAP1/GetNetworkStats</string>
	        <string>http://purenetworks.com/HNAP1/IsDeviceReady</string>
					<string>http://purenetworks.com/HNAP1/SetAccessPointMode</string>
					<string>http://purenetworks.com/HNAP1/GetDeviceSettings2</string>
					<string>http://purenetworks.com/HNAP1/SetDeviceSettings2</string>
					<string>http://purenetworks.com/HNAP1/GetWLanRadios</string>
					<string>http://purenetworks.com/HNAP1/GetWLanRadioSettings</string>
					<string>http://purenetworks.com/HNAP1/SetWLanRadioSettings</string>
					<string>http://purenetworks.com/HNAP1/GetWLanRadioSecurity</string>
					<string>http://purenetworks.com/HNAP1/SetWLanRadioSecurity</string>
					<string>http://purenetworks.com/HNAP1/GetWanStatus</string>
					<string>http://purenetworks.com/HNAP1/GetClientStats</string>
					<string>http://purenetworks.com/HNAP1/GetRouterSettings</string>
					<string>http://purenetworks.com/HNAP1/SetRouterSettings</string>
					<string>http://purenetworks.com/HNAP1/GetIPv6Settings</string>
					<string>http://purenetworks.com/HNAP1/SetIPv6Settings</string>
					<string>http://purenetworks.com/HNAP1/GetOpenDNS</string>
					<string>http://purenetworks.com/HNAP1/GetFirmwareState</string>
					<string>http://purenetworks.com/HNAP1/SetWebFilterSettings</string>
					<string>http://purenetworks.com/HNAP1/GetWebFilterSettings</string>
					<string>http://purenetworks.com/HNAP1/SetDLNA</string>
					<string>http://purenetworks.com/HNAP1/GetDLNA</string>
      </SOAPActions>
      <SubDeviceURLs>
      </SubDeviceURLs>
      <Tasks>
      </Tasks>
    </GetDeviceSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>
