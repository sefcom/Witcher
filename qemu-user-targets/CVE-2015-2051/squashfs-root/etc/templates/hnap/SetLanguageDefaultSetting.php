HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/webinc/config.php";

$result = "OK";
$default_language = query("/runtime/hnap/SetLanguageDefaultSetting/SetDefaultLanguage");

if ($default_language == "")
{
	$result = "ERROR";
}

set ("/device/features/language", $default_language);

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Client Info Changed\" > /dev/console\n");
if($result == "OK")
{

	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
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
		<SetLanguageDefaultSettingResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetLanguageDefaultSettingResult><?=$result?></SetLanguageDefaultSettingResult>
		</SetLanguageDefaultSettingResponse>
	</soap:Body>
</soap:Envelope>
