HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
include "/htdocs/phplib/encrypt.php";
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetDeviceSettings/";
$dev_name = query($nodebase."DeviceName");
$captcha  = query($nodebase."CAPTCHA");
$PresentationURL = query($nodebase."PresentationURL");
set("/device/devicename", $dev_name);
$hostname = substr($PresentationURL, strlen("http://"), strstr($PresentationURL, ".local/") - strlen("http://")); //ex: Extract "dlinkrouter" from http://dlinkrouter.local/
set("/device/hostname", $hostname);
if($captcha=="true")
{
	set("/device/session/captcha", 1);
}
else if($captcha=="false")
{
	set("/device/session/captcha", 0);
}
$result = "OK";
foreach("/device/account/entry")
{
	if(query("group")==0)
	{
		if(get("", $nodebase."ChangePassword") == "true") {set("password", AES_Decrypt128(query($nodebase."AdminPassword")));}
	}
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Password Changed\" > /dev/console\n");
if($result == "OK")
{

	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "sh /var/servd/DEVICE.ACCOUNT_stop.sh\n");
	fwrite("a",$ShellPath, "xmldbc -P /etc/services/DEVICE.ACCOUNT.php -V START=/var/servd/DEVICE.ACCOUNT_start.sh -V STOP=/var/servd/DEVICE.ACCOUNT_stop.sh\n");
	fwrite("a",$ShellPath, "sh /var/servd/DEVICE.ACCOUNT_start.sh\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console\n");	
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetDeviceSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetDeviceSettingsResult><?=$result?></SetDeviceSettingsResult>
    </SetDeviceSettingsResponse>
  </soap:Body>
</soap:Envelope>
