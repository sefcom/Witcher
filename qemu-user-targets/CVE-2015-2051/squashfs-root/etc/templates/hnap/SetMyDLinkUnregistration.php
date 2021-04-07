HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetMyDLinkUnregistration/";
$Unregistration=get("", $nodebase."Unregistration");
$result = "OK";

if(tolower($Unregistration) == "true")
{
	//If /mydlink/register_st is 0, mydlink engine restart would unregister this device. Mydlink engine would restart when WAN UP.
	set("/mydlink/register_st", 0);
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
if($result == "OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "opt.local restart > /dev/console\n");//Restart Mydlink engine.
	set("/runtime/hnap/dev_status", "ERROR");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetMyDLinkUnregistrationResponse xmlns="http://purenetworks.com/HNAP1/">
      <SetMyDLinkUnregistrationResult><?=$result?></SetMyDLinkUnregistrationResult>
    </SetMyDLinkUnregistrationResponse>
  </soap:Body>
</soap:Envelope>
