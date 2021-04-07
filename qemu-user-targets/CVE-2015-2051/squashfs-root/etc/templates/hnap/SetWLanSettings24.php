<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_phyinf_wlan1 = XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0);
$path_wlan1_wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", $path_phyinf_wlan1."/wifi", 0);
?>
HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetWLanSettings24/";
$Enabled=query($nodebase."Enabled");
$SSID=query($nodebase."SSID");
$SSIDBroadcast=query($nodebase."SSIDBroadcast");
$Channel=query($nodebase."Channel");

anchor($path_wlan1_wifi);

if($Enabled=="true")
{
	set("active", 1);
}
else if($Enabled=="false")
{
	set("active", 0);
}

if($SSID!="")
{
	$old_ssid = query("ssid");
	if($old_ssid != $SSID) 
	{ 
		set("wps/configured", "1"); 
	}
	set("ssid", $SSID);
}

if($SSIDBroadcast=="true")
{
	set("ssidhidden", 0);
}
else if($SSIDBroadcast=="false")
{
	set("ssidhidden", 1);
}

if($Channel!="")
{
	
	set($path_phy_wlan1."/media/channel", $Channel);
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->WLan Change\" > /dev/console\n");
fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
fwrite("a",$ShellPath, "service ".$SRVC_WLAN." restart > /dev/console\n");
fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
set("/runtime/hnap/dev_status", "ERROR");
/* Kwest mark: 
   only to fix issue that D-Link HNAP Interface Verificator can't set wireless security to DIR-615D.
   NOTE: it is not a standard action. D-Link SHOULD fix his tool's bug.
 */
$Enabled=query($nodebase."Enabled");
$Type=query($nodebase."Type");
$Key=query($nodebase."Key");
$WEPKeyBits=query($nodebase."WEPKeyBits");
if($Enabled=="true")
{
	if($Type=="WPA")
	{
		set("wps/configured", "1");
		set("authtype", "WPA+2PSK"); // WPA-PSK/WPA2-PSK
		set("encrtype", "TKIP+AES"); // TKIP/AES
		set("nwkey/psk/key", $Key);
		set("nwkey/psk/passphrase", "1");
	}
	else if($Type=="WEP")
	{
		set("wps/configured", "1");
		set("authtype", "OPEN"); // Open
		set("encrtype", "WEP"); // WEP
		set("nwkey/wep/ascii", 0); // HEX
		set("nwkey/wep/size", $WEPKeyBits);
		$defkey = query("nwkey/wep/defkey");
		set("nwkey/wep/key:".$defkey, $Key);
	}
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetWLanSettings24Response xmlns="http://purenetworks.com/HNAP1/">
      <SetWLanSettings24Result>OK</SetWLanSettings24Result>
    </SetWLanSettings24Response>
  </soap:Body>
</soap:Envelope>
