HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<? 
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/trace.php";

$nodebase= "/runtime/hnap/SetTimeSettings";
$result = "OK";

$CurrentDate = get("x",$nodebase."/CurrentDate");
$CurrentTime = get("x",$nodebase."/CurrentTime");			
$NTP = get("x",$nodebase."/NTP");
$NTPServer = get("x",$nodebase."/NTPServer");
$TimeZone = get("x",$nodebase."/TimeZone");
$DaylightSaving = get("x",$nodebase."/DaylightSaving");
$DSTStartMonth = get("x",$nodebase."/DSTStartMonth");
$DSTStartWeek = get("x",$nodebase."/DSTStartWeek");
$DSTStartDayOfWeek = get("x",$nodebase."/DSTStartDayOfWeek");
$DSTStartTime = get("x",$nodebase."/DSTStartTime");
$DSTEndMonth = get("x",$nodebase."/DSTEndMonth");
$DSTEndWeek = get("x",$nodebase."/DSTEndWeek");
$DSTEndDayOfWeek = get("x",$nodebase."/DSTEndDayOfWeek");
$DSTEndTime = get("x",$nodebase."/DSTEndTime");

//Error Check
if($NTPServer!="" && isdomain($NTPServer)==0)
{$result = "ERROR";}

if($result == "OK")
{
	/*Date format for HNAP is yyyy/mm/dd. Date format for router is mm/dd/yyyy.*/
	$CurrentDate_Modify = cut($CurrentDate, 1, "/")."/".cut($CurrentDate, 2, "/")."/".cut($CurrentDate, 0, "/");

	set("/device/time/timezone",$TimeZone);

	if($NTP=="true") $NTP = 1;
	else $NTP = 0;

	if($DaylightSaving=="true") $DaylightSaving = 1;
	else $DaylightSaving = 0;

	if ($NTP=="1")
	{
		set("/device/time/ntp/enable",1);
		set("/device/time/ntp6/enable",1);
		set("/device/time/ntp/server",$NTPServer);
	}
	else
	{
		set("/device/time/ntp/enable",0);
		set("/device/time/ntp6/enable",0);
		set("/device/time/ntp/server","");
		set("/device/time/time",	$CurrentTime);
		set("/device/time/date",	$CurrentDate_Modify);

		//Refer /htdocs/phplib/setcfg/RUNTIME.TIME.php
		set("/runtime/device/tmp_date", $CurrentDate_Modify);
		set("/runtime/device/tmp_time", $CurrentTime);
		set("/runtime/device/ntp/state", "RUNNING");
		set("/runtime/device/timestate", "RUNNING");
	}

	if(strlen($DSTStartTime)==6)//ex: 4:00AM should be changed to 04:00:00 for router
	{$DSTStartTime = "0".$DSTStartTime;}
	if(strlen($DSTEndTime)==6)//ex: 4:00AM should be changed to 04:00:00 for router
	{$DSTEndTime = "0".$DSTEndTime;}


	if($DaylightSaving=="1")
	{
		//4:00AM should be changed to 04:00:00 for router
	    $DSTStartTime_Hour = cut($DSTStartTime, 0, ":");
	    if(substr($DSTStartTime, strlen($DSTStartTime)-2, 2)=="PM")
		{$DSTStartTime_Hour = $DSTStartTime_Hour+12;}
		$DSTStartTime = $DSTStartTime_Hour.":00:00";
		if(strlen($DSTStartTime)==7)
		{$DSTStartTime = "0".$DSTStartTime;}

	    $DSTEndTime_Hour = cut($DSTEndTime, 0, ":");
	    if(substr($DSTEndTime, strlen($DSTEndTime)-2, 2)=="PM")
		{$DSTEndTime_Hour = $DSTEndTime_Hour+12;}
		$DSTEndTime = $DSTEndTime_Hour.":00:00";
		if(strlen($DSTEndTime)==7)
		{$DSTEndTime = "0".$DSTEndTime;}

		set("/device/time/dst",1);
		set("/device/time/dstmanual",",M".$DSTStartMonth.".".$DSTStartWeek.".".$DSTStartDayOfWeek."/".$DSTStartTime.",M".$DSTEndMonth.".".$DSTEndWeek.".".$DSTEndDayOfWeek."/".$DSTEndTime);

		if(get("", "/device/time/dstoffset")=="")
		{set("/device/time/dstoffset", "+01:00");}
	}
	else
	{
		set("/device/time/dst",0);
		set("/device/time/dstmanual","");
		set("/device/time/dstoffset", "+01:00");
	}
}


if($result=="OK")
{
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service DEVICE.TIME restart > /dev/console\n");
	fwrite("a",$ShellPath, "service RUNTIME.TIME restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("w",$ShellPath, "#!/bin/sh\n");
	fwrite("a",$ShellPath, "echo [$0] > /dev/console\n");
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<SetTimeSettingsResponse xmlns="http://purenetworks.com/HNAP1/"> 
<SetTimeSettingsResult><?=$result?></SetTimeSettingsResult> 
</SetTimeSettingsResponse>
</soap:Body>
</soap:Envelope>