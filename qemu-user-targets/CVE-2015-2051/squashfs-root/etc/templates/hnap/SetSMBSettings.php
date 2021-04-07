HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/inet.php";

$nodebase = "/runtime/hnap/SetSMBSettings/";
$result = "OK";

if(get("", $nodebase."SMBSecurity")=="Enable")
{	set("/samba/auth", 1);}
else if(get("", $nodebase."SMBSecurity")=="Disable")
{	set("/samba/auth", 0);}
else
{	$result = "ERROR";}


fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Samba Settings\" > /dev/console\n");

if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service SAMBA restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetSMBSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetSMBSettingsResult><?=$result?></SetSMBSettingsResult>
		</SetSMBSettingsResponse>
	</soap:Body>
</soap:Envelope>