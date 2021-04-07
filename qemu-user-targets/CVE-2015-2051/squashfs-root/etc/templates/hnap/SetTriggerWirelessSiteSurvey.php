HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

$nodebase = "/runtime/hnap/SetTriggerWirelessSiteSurvey/";
$RadioID = get("", $nodebase."RadioID");
$result = "OK";

if($RadioID!="RADIO_2.4GHz" && $RadioID!="RADIO_5GHz" &&
	$RadioID!="RADIO_2.4GHz_Guest" && $RadioID!="RADIO_2.4GHz")
{
	$result = "ERROR_BAD_RADIOID";
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]--> Trigger Wireless SiteSurvey Settings\" > /dev/console\n");
if($result=="OK")
{
	event("SITESURVEY");
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
		<SetTriggerWirelessSiteSurveyResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetTriggerWirelessSiteSurveyResult><?=$result?></SetTriggerWirelessSiteSurveyResult>
			<WaitTime>12</WaitTime>
		</SetTriggerWirelessSiteSurveyResponse>
	</soap:Body>
</soap:Envelope>