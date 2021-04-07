HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/inet.php";

$nodebase = "/runtime/hnap/SetDMZSettings/";
$result = "OK";
$dmz_path = "/nat/entry/dmz";

if(get("", $nodebase."Enabled")=="true")
{
	set($dmz_path."/enable", "1");
	set($dmz_path."/inf", $LAN1);
	set($dmz_path."/hostid", ipv4hostid(get("", $nodebase."IPAddress"), get("", INET_getpathbyinf($LAN1)."/ipv4/mask")));
}	
else
{
	set($dmz_path."/enable", "0");
	set($dmz_path."/inf", "");
	set($dmz_path."/hostid", "");
}


fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->DMZ Settings\" > /dev/console\n");

if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service DMZ.NAT-1 restart > /dev/console\n");
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
		<SetDMZSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetDMZSettingsResult><?=$result?></SetDMZSettingsResult>
		</SetDMZSettingsResponse>
	</soap:Body>
</soap:Envelope>