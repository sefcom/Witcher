HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/xnode.php";

$nodebase = "/runtime/hnap/SetScheduleSettings";
$node_sch_infolists = $nodebase."/ScheduleInfoLists";
$result = "REBOOT"; // The schedule for some functions may not take effect if the relative services would not restart or the device would not reboot.

//Move original schedule to "/runtime/hnap/SetScheduleSettings/schedule_old"
$sch_seqno = get("", "/schedule/seqno");
$sch_count = 0;
set($nodebase."/schedule_old", "");
movc("/schedule", $nodebase."/schedule_old");
set("/schedule/seqno", $sch_seqno);
set("/schedule/max", 10);
set("/schedule/count", $sch_count);

foreach($node_sch_infolists)
{
	//If the schedule name is new, the responded schedule uid is also new. If the schedule name is old, it has responded schedule uid.
	$sch_old_path = XNODE_getpathbytarget($nodebase."/schedule_old", "entry", "description", get("", "ScheduleName"), 0);
	if($sch_old_path!="") {$sch_uid = get("", $sch_old_path."/uid");}
	else
	{
		$sch_uid = "SCH-".$sch_seqno;
		$sch_seqno++;
	}
	set("/schedule/entry:".$InDeX."/uid", $sch_uid);
	set("/schedule/entry:".$InDeX."/description", get("", "ScheduleName"));
	set("/schedule/entry:".$InDeX."/exclude", 0);
	$sch_entry = "/schedule/entry:".$InDeX;
	$sch_count++;
	set("/schedule/seqno", $sch_seqno);
	set("/schedule/count", $sch_count);
	foreach("ScheduleInfo")
	{
		$sch_day_entry = $sch_entry."/entry:".$InDeX;
		$date = get("", "ScheduleDate");
		if($date >= 1 || $date <= 7) { set($sch_day_entry."/date", $date); }
		else { $result = "ERROR_BAD_ScheduleInfo"; }

		if (get("", "ScheduleAllDay") == "true") /* all day */
		{
			if (get("", "ScheduleTimeFormat") == "true") /* 24 hours */
			{set($sch_day_entry."/format", 24);}
			else if (get("", "ScheduleTimeFormat") == "false") /* 12 hours */
			{set($sch_day_entry."/format", 12);}
			set($sch_day_entry."/start", "0:00");
			set($sch_day_entry."/end", "24:00");
		}
		else if (get("", "ScheduleAllDay") == "false")
		{
			$start_hour = get("", "ScheduleStartTimeInfo/TimeHourValue");
			$start_min = get("", "ScheduleStartTimeInfo/TimeMinuteValue");
			$end_hour = get("", "ScheduleEndTimeInfo/TimeHourValue");
			$end_min = get("", "ScheduleEndTimeInfo/TimeMinuteValue");

			if (get("", "ScheduleTimeFormat") == "true") /* 24 hours */
			{set($sch_day_entry."/format", 24);}
			else if (get("", "ScheduleTimeFormat") == "false") /* 12 hours */
			{
				set($sch_day_entry."/format", 12);

				$start_mid = get("", "ScheduleStartTimeInfo/TimeMidDateValue");
				$end_mid = get("", "ScheduleEndTimeInfo/TimeMidDateValue");
				if ($start_mid == "true") //PM
				{
					$start_hour = $start_hour + 12;
					$end_hour = $end_hour + 12;
				}
				else if ($start_mid == "false") //AM
				{
					if ($end_mid == "true") //PM
					{$end_hour = $end_hour + 12;}
				}
			}
			$start_time = $start_hour.":".$start_min;
			$end_time = $end_hour.":".$end_min;
			set($sch_day_entry."/start", $start_time);
			set($sch_day_entry."/end", $end_time);
		}
		else { $result = "ERROR_BAD_ScheduleInfo"; }
	}
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->Schedule Settings\" > /dev/console\n");
if($result == "OK" || $result == "REBOOT")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
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
	<SetScheduleSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
	<SetScheduleSettingsResult><?=$result?></SetScheduleSettingsResult>
	</SetScheduleSettingsResponse>
	</soap:Body>
</soap:Envelope>
