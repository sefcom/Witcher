<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
$path_wifi_wifi1 = XNODE_getpathbytarget("wifi", "entry", "uid", "WIFI-1", 0);
?>
HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetWLanSettings54/";
$Enabled=query($nodebase."Enabled");
$SSID=query($nodebase."SSID");
$SSIDBroadcast=query($nodebase."SSIDBroadcast");
$Channel=query($nodebase."Channel");

anchor("/wireless11a");

if($Enabled=="true")
{
	set("enable", 1);
}
else if($Enabled=="false")
{
	set("enable", 0);
}

if($SSID!="")
{
	$old_ssid = query($path_wifi_wifi1."/ssid");
	if($old_ssid != $SSID) 
	{ 
		set($path_wifi_wifi1."/wps/configured", "1"); 
	}
	set("ssid", $SSID);
}

if($SSIDBroadcast=="true")
{
	set("ssidHidden", 0);
}
else if($SSIDBroadcast=="false")
{
	set("ssidHidden", 1);
}

if($Channel!="")
{
	set("autoChannel", 0);
	set("channel", $Channel);
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
    <SetWLanSettings54Response xmlns="http://purenetworks.com/HNAP1/">
      <SetWLanSettings54Result>OK</SetWLanSettings54Result>
    </SetWLanSettings54Response>
  </soap:Body>
</soap:Envelope>
