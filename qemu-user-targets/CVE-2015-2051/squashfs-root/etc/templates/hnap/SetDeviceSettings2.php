HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
$nodebase="/runtime/hnap/SetDeviceSettings2/";
$result = "OK";
$userName = query($nodebase."Username");
if($userName != "admin")
{ $result = "ERROR_USERNAME_NOT_SUPPORTED"; }
$tz = query($nodebase."TimeZone");
//else if( $tz == "UTC+00:00" || $tz == "UTC" || $tz == "GMT+00:00" || $tz == "GMT" )
$tz_len = strlen($tz);
if ($tz != "" && $tz_len >= 3)
{
	$tz_type = substr($tz, 0, 3);
	if ($tz_len > 3)
		$tz_time = substr($tz, 3, $tz_len - 3);

	if ($tz_type != "")
	{
		if ($tz_type == "UTC" || $tz_type == "GMT")
			$tz_string = "GMT";
		if ($tz_string != "" && $tz_time != "")
		{
			$tz_string = $tz_string.$tz_time;
		}
		if ($tz_string != "")
		{
			foreach("/runtime/services/timezone/zone")
			{
				$zone_name = cut(query("name"), 0, ")");
				$zone_name = cut($zone_name, 1, "(");
				if ($tz_string == $zone_name)
				{
					$tzInx = $InDeX;
					break;
				}
			}
		}
	}
}

if( $tzInx == "" )
{ $result = "ERROR_TIMEZONE_NOT_SUPPORTED"; }

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]--> Time Changed\" > /dev/console\n");
if( $result == "OK" )
{
	set("/device/time/timezone",$tzInx);
	set("/hnap/timezone:".$tzInx."/name", $tz);
	$autoAdj = query($nodebase."AutoAdjustDST");
	if($autoAdj == "true")
	{ set("/time/timezone/dst", "1"); }
	else
	{ set("/time/timezone/dst", "0"); }	
	$locale = query($nodebase."Locale");
	set("/hnap/Locale",$locale);

	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");	
	fwrite("a",$ShellPath, "service DEVICE.TIME start > /dev/console\n");
    fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"[$0] --> Failed\n");
}
?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SetDeviceSettings2Response xmlns="http://purenetworks.com/HNAP1/">
      <SetDeviceSettings2Result><?=$result?></SetDeviceSettings2Result>
    </SetDeviceSettings2Response>
  </soap:Body>
</soap:Envelope>
