HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php"; 
include "/htdocs/webinc/config.php";
$path_wifi_wifi1 = XNODE_getpathbytarget("wifi", "entry", "uid", "WIFI-1", 0);
$nodebase="/runtime/hnap/SetWLanSecurity/";
$Type=query($nodebase."Type");
$Key=query($nodebase."Key");
$Enabled=query($nodebase."Enabled");
$WEPKeyBits=query($nodebase."WEPKeyBits");
$result = "OK";
anchor($path_wifi_wifi1);
if($Enabled=="true")
{
	if($Type=="WPA")
	{
		set("wps/configured", "1");
		set("authtype", "WPA");

		//----- do not check empty value, because there maybe really empty value
		set("nwkey/eap/radius", query($nodebase."RadiusIP1"));
		set("nwkey/eap/port", query($nodebase."RadiusPort1"));
		set("nwkey/eap/radius", query($nodebase."RadiusIP2"));
		set("nwkey/eap/port", query($nodebase."RadiusPort2"));
		set("nwkey/eap/secret", $Key);
	}
	else if($Type=="WEP")
	{
		set("wps/configured", "1");
		set("authtype", "OPEN");
		set("encrypttype", "WEP");
		if($WEPKeyBits!="")
		{
			set("nwkey/wep/size", $WEPKeyBits);
		}
		$id=query("nwkey/wep/defkey");
		set("nwkey/wep/ascii", 1);
		set("nwkey/wep/key:".$id, $Key);
	}
}
else if($Enabled=="false")
{
	set("authtype", "OPEN");
	set("encrypttype", "NONE");
}
fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->WLan Change\" > /dev/console\n");
fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
fwrite("a",$ShellPath, "service ".$SRVC_WLAN." restart > /dev/console\n");
fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
set("/runtime/hnap/dev_status", "ERROR");
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetWLanSecurityResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetWLanSecurityResult><?=$result?></SetWLanSecurityResult>
    </SetWLanSecurityResponse>
  </soap:Body>
</soap:Envelope>
