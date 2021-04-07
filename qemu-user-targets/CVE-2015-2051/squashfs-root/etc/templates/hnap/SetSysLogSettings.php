HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$nodebase= "/runtime/hnap/SetSysLogSettings";
$result = "OK";

$SysLog = query($nodebase."/SysLog");
$IPAddress = query($nodebase."/IPAddress");

TRACE_debug("=======SysLog=====".$SysLog);
TRACE_debug("=======IPAddress=====".$IPAddress);


if($SysLog=="true") $SysLog = 1; 
else $SysLog = 0;

if ($SysLog=="1")
{ 
	set("/device/log/remote/enable",1);
	set("/device/log/remote/ipv4/ipaddr",$IPAddress);
}	 

else 
{ 
	set("/device/log/remote/enable",0);
	set("/device/log/remote/ipv4/ipaddr","");
}	

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->System Log Settings\" > /dev/console\n");
if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetSysLogSettingsResponse xmlns="http://purenetworks.com/HNAP1/"> 
<SetSysLogSettingsResult><?=$result?></SetSysLogSettingsResult> 
</SetSysLogSettingsResponse>
</soap:Body>
</soap:Envelope>