HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php"; 

$result = "OK";
$enable = get("","/runtime/hnap/SetDLNA/Enabled");
$devicename = get("","/runtime/hnap/SetDLNA/DeviceName");

if($enable == "true")
{
	/* /upnpav/dms/name is seted by defaultvalue */
	set("/upnpav/dms/active", 1);
	if(query("/upnpav/dms/sharepath")=="")	set("/upnpav/dms/sharepath", "/");
}
else
{
	set("/upnpav/dms/active", 0);
}
set("/upnpav/dms/name", $devicename);

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
fwrite("a",$ShellPath, "service UPNPAV restart > /dev/console\n");

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetDLNAResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetDLNAResult><?=$result?></SetDLNAResult>
</SetDLNAResponse>
</soap:Body>
</soap:Envelope>

