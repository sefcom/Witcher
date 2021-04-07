<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

$result = "OK";
?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
	<GetScheduleSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
		<GetScheduleSettingsResult><?=$result?></GetScheduleSettingsResult>
		<?
		foreach("/schedule/entry")
		{
			echo "		<ScheduleInfoLists>\n";
			echo "			<ScheduleName>".query("description")."</ScheduleName>\n";
			foreach("entry")
			{
				if(get("", "start")=="0:00" && get("", "end")=="24:00") {$ScheduleAllDay = "true";}
				else {$ScheduleAllDay = "false";}

				$start_hour = cut(get("", "start"), 0, ":");
				$start_min = cut(get("", "start"), 1, ":");
				$end_hour = cut(get("", "end"), 0, ":");
				$end_min = cut(get("", "end"), 1, ":");

				if(get("", "format")=="24")
				{
					$ScheduleTimeFormat = "true";
					$StartTimeMidDate = "false";
					$EndTimeMidDate = "false";
				}
				else
				{
					$ScheduleTimeFormat = "false";
					if($start_hour >= 12)
					{
						$start_hour = $start_hour -12;
						$end_hour = $end_hour -12;
						$StartTimeMidDate = "true";
						$EndTimeMidDate = "true";
					}
					else if($end_hour >= 12)
					{
						$end_hour = $end_hour -12;
						$StartTimeMidDate = "false";
						$EndTimeMidDate = "true";
					}
					else
					{
						$StartTimeMidDate = "false";
						$EndTimeMidDate = "false";
					}
				}

				echo "			<ScheduleInfo>\n";
				echo "				<ScheduleDate>".query("date")."</ScheduleDate>\n";
				echo "				<ScheduleAllDay>".$ScheduleAllDay."</ScheduleAllDay>\n";
				echo "				<ScheduleTimeFormat>".$ScheduleTimeFormat."</ScheduleTimeFormat>\n";

				echo "				<ScheduleStartTimeInfo>\n";
				echo "					<TimeHourValue>".$start_hour."</TimeHourValue>\n";
				echo "					<TimeMinuteValue>".$start_min."</TimeMinuteValue>\n";
				echo "					<TimeMidDateValue>".$StartTimeMidDate."</TimeMidDateValue>\n";
				echo "				</ScheduleStartTimeInfo>\n";

				echo "				<ScheduleEndTimeInfo>\n";
				echo "					<TimeHourValue>".$end_hour."</TimeHourValue>\n";
				echo "					<TimeMinuteValue>".$end_min."</TimeMinuteValue>\n";
				echo "					<TimeMidDateValue>".$EndTimeMidDate."</TimeMidDateValue>\n";
				echo "				</ScheduleEndTimeInfo>\n";
				echo "			</ScheduleInfo>\n";
			}
			echo "		</ScheduleInfoLists>";
		}
		?>
	</GetScheduleSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>

