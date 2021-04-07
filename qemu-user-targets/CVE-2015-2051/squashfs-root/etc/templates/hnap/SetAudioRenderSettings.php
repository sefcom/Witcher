HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/inet.php";

$nodebase = "/runtime/hnap/SetAudioRenderSettings/";
$result = "OK";

$AirPlay	= get("x", $nodebase."AirPlay");
$DLNA		= get("x", $nodebase."DLNA");
$MediaName	= get("x", $nodebase."MediaName");

$AirPlay_org	= get("x", "/device/audiorender/airplay");
$DLNA_org		= get("x", "/device/audiorender/dlna");
$MediaName_org	= get("x", "/device/audiorender/medianame");

if($AirPlay == "true")	{set("/device/audiorender/airplay", 1);}
else					{set("/device/audiorender/airplay", 0);}

if($DLNA == "true")	{set("/device/audiorender/dlna", 1);}
else				{set("/device/audiorender/dlna", 0);}

if($MediaName != ""){set("/device/audiorender/medianame", $MediaName);}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Audio Render Settings\" > /dev/console\n");
if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	if($AirPlay!=$AirPlay_org || $MediaName!=$MediaName_org)
	{fwrite("a",$ShellPath, "service AUDIORENDER.AIRPLAY restart > /dev/console\n");}
	if($DLNA!=$DLNA_org || $MediaName!=$MediaName_org)
	{fwrite("a",$ShellPath, "service AUDIORENDER.DLNA restart > /dev/console\n");}
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
		<SetAudioRenderSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetAudioRenderSettingsResult><?=$result?></SetAudioRenderSettingsResult>
		</SetAudioRenderSettingsResponse>
	</soap:Body>
</soap:Envelope>