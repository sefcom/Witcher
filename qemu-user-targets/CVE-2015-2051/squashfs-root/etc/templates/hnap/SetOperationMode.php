HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

function checkAvailableOPMode($RaID, $CurOPMode)
{
	if($CurOPMode=="WirelessBridge" && $FEATURE_NOAPMODE != 1)
	{return "true";}
	else if($CurOPMode=="WirelessRouter" && get("", "/runtime/device/router/mode") != "")
	{return "true";}
	else if($CurOPMode=="WirelessAp" || $CurOPMode=="WirelessRepeaterExtender" || $CurOPMode=="WirelessBridge")
	{return "true";}
	return "false";
}

function GetCurrentOPModeFromDevice($RaID)
{
	if(get("", "/device/layout")=="router")
	{
		$ret = "WirelessRouter";
	}
	else if(get("", "/device/layout")=="bridge")
	{
		if(query("/device/op_mode")=="repeater_ext")
			{$ret="WirelessRepeaterExtender";}
		else
			{$ret = "WirelessBridge";}
	}
	return $CurOPMode;
}

$result = "OK";

$RadioID = query("/runtime/hnap/SetOperationMode/RadioID");
$CurrentOPMode = query("/runtime/hnap/SetOperationMode/CurrentOPMode");

if($RadioID!="RADIO_2.4GHz" && $RadioID!="RADIO_2.4G_Guest" && $RadioID!="RADIO_5GHz" && $RadioID!="RADIO_5G_Guest" && $RadioID!="RADIO_5GHz_2" && $RadioID!="RADIO_5GHz_2_Guest")
{$result = "ERROR_BAD_RADIOID";}
else if(checkAvailableOPMode($RadioID, $CurrentOPMode)!="true")
{$result = "ERROR_BAD_CurrentOPMode";}
else if(GetCurrentOPModeFromDevice($RadioID) != $CurrentOPMode)
{
	if($CurrentOPMode == "WirelessBridge")
	{
		set("/device/layout", "bridge");
	}
	else if($CurrentOPMode == "WirelessRouter")
	{
		set("/device/layout", "router");
	}
	$result = "REBOOT";
}
else
{	$result = "ERROR";}

fwrite("w",$ShellPath, "#!/bin/sh\n");
if($result == "REBOOT")
{
	fwrite("a",$ShellPath, "echo \"[$0]-->Operation mode is Changed\" > /dev/console\n");
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
		<SetOperationModeResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetOperationModeResult><? echo $result; ?></SetOperationModeResult>
		</SetOperationModeResponse>
	</soap:Body>
</soap:Envelope>
