<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$nodebase= "/runtime/hnap/GetTimeSettings";
$result = "OK";

function month_trans($month)
{
	if($month=="Jan")		{return "01";}
	else if($month=="Feb")	{return "02";}
	else if($month=="Mar")	{return "03";}
	else if($month=="Apr")	{return "04";}
	else if($month=="May")	{return "05";}
	else if($month=="Jun")	{return "06";}
	else if($month=="Jul")	{return "07";}
	else if($month=="Aug")	{return "08";}
	else if($month=="Sep")	{return "09";}
	else if($month=="Oct")	{return "10";}
	else if($month=="Nov")	{return "11";}
	else if($month=="Dec")	{return "12";}
}

$Time_UTC = get("TIME.RFC1123","/runtime/device/uptime");
$CurrentDate_UTC = scut($Time_UTC, 3, "")."/".month_trans(scut($Time_UTC, 2, ""))."/".scut($Time_UTC, 1, "");
$CurrentTime_UTC = scut($Time_UTC, 4, "");

$NTP = get("x","/device/time/ntp/enable");
$DaylightSaving = get("x","/device/time/dst");

if($NTP==1) $NTP = "true"; 
else $NTP = "false";

if($DaylightSaving==1) $DaylightSaving = "true";
else $DaylightSaving = "false";

$NTPServer = get("x","/device/time/ntp/server");
$TimeZone = get("x","/runtime/device/timezone/index");

$temp = get("x","/device/time/dstmanual");

$dststart = cut($temp, 1, ",");
$dstend = cut($temp, 2, ",");

$dststart_date = cut($dststart, 0, "/");
$dststart_time = cut($dststart, 1, "/");

$dstend_date = cut($dstend, 0, "/");
$dstend_time = cut($dstend, 1, "/");

$dststart_month = cut($dststart_date, 0, ".");
$DSTStartMonth = scut($dststart_month, 0, "M");
$DSTStartWeek = cut($dststart_date, 1, ".");
$DSTStartDayOfWeek = cut($dststart_date, 2, ".");
$DSTStartTime = $dststart_time;

$dstend_month = cut($dstend_date, 0, ".");
$DSTEndMonth = scut($dstend_month, 0, "M");
$DSTEndWeek = cut($dstend_date, 1, ".");
$DSTEndDayOfWeek = cut($dstend_date, 2, ".");
$DSTEndTime = $dstend_time;

if($DaylightSaving == "false")
{
	set($DSTStartMonth,"");
	set($DSTStartWeek,"");
	set($DSTStartDayOfWeek,"");
	set($DSTStartTime,"");
	set($DSTEndMonth,"");
	set($DSTEndWeek,"");
	set($DSTEndDayOfWeek,"");
	set($DSTEndTime,"");
}
else	//ex: 04:00:00 should be changed to 4:00AM for HNAP spec.
{
	$DSTStartTime_Hour = cut($DSTStartTime, 0, ":");
	if(substr($DSTStartTime_Hour, 0, 1)=="0")
	{$DSTStartTime_Hour = substr($DSTStartTime_Hour, 1, 1);}
	if($DSTStartTime_Hour >= 13)
	{
		$DSTStartTime_Hour = $DSTStartTime_Hour - 12;
		$DSTStartTime_AMPM = "PM";
	}
	else {$DSTStartTime_AMPM = "AM";}
	$DSTStartTime = $DSTStartTime_Hour.":00".$DSTStartTime_AMPM;

	$DSTEndTime_Hour = cut($DSTEndTime, 0, ":");
	if(substr($DSTEndTime_Hour, 0, 1)=="0")
	{$DSTEndTime_Hour = substr($DSTEndTime_Hour, 1, 1);}
	if($DSTEndTime_Hour >= 13)
	{
		$DSTEndTime_Hour = $DSTEndTime_Hour - 12;
		$DSTEndTime_AMPM = "PM";
	}
	else {$DSTEndTime_AMPM = "AM";}
	$DSTEndTime = $DSTEndTime_Hour.":00".$DSTEndTime_AMPM;
}


?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetTimeSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
      <GetTimeSettingsResult><?=$result?></GetTimeSettingsResult> 
      <CurrentDate><?=$CurrentDate_UTC?></CurrentDate>
      <CurrentTime><?=$CurrentTime_UTC?></CurrentTime>
      <NTP><?=$NTP?></NTP>
      <NTPServer><?=$NTPServer?></NTPServer>
      <TimeZone><?=$TimeZone?></TimeZone> 
      <DaylightSaving><?=$DaylightSaving?></DaylightSaving>
      <DSTStartMonth><?=$DSTStartMonth?></DSTStartMonth>
      <DSTStartWeek><?=$DSTStartWeek?></DSTStartWeek>
      <DSTStartDayOfWeek><?=$DSTStartDayOfWeek?></DSTStartDayOfWeek>
      <DSTStartTime><?=$DSTStartTime?></DSTStartTime>
      <DSTEndMonth><?=$DSTEndMonth?></DSTEndMonth> 
      <DSTEndWeek><?=$DSTEndWeek?></DSTEndWeek>
      <DSTEndDayOfWeek><?=$DSTEndDayOfWeek?></DSTEndDayOfWeek>
      <DSTEndTime><?=$DSTEndTime?></DSTEndTime> 
    </GetTimeSettingsResponse> 
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>