HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$result = "OK";
if (query("/runtime/hnap/action_name") != "SetWirelessMode")
{
	$result = "ERROR";
}

$RadioID = query("/runtime/hnap/SetWirelessMode/RadioID");
$WirelessMode = query("/runtime/hnap/SetWirelessMode/WirelessMode");

$intf = "";

if ($RadioID == "RADIO_2.4GHz")
{
	$intf = $WLAN1;
}
else if ($RadioID == "RADIO_5GHz")
{
	$intf = $WLAN2;
}
else if ($RadioID == "RADIO_5GHz_2")
{
	$intf = $WLAN3;
}
else if ($RadioID == "RADIO_2.4G_Guest")
{
	$intf = $WLAN1_GZ;
}
else if ($RadioID == "RADIO_5G_Guest")
{
	$intf = $WLAN2_GZ;
}
else if ($RadioID == "RADIO_5GHz_2_Guest")
{
	$intf = $WLAN3_GZ;
}
else
{
	$result = "ERROR";
}

if ($result == "OK")
{
	if ($WirelessMode == "WirelessRouter")
	{
		$result = "OK";
	}
	else
	{
		$result = "ERROR";
	}
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetWirelessModeResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetWirelessModeResult><? echo $result ; ?></SetWirelessModeResult>
		</SetWirelessModeResponse>
	</soap:Body>
</soap:Envelope>
