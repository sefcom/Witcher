<? /* vi: set sw=4 ts=4: */

/* XNODE_getvaluebynode() */
function XNODE_getvaluebynode($base, $node, $target, $idx)
{
	return query($base."/".$node.":".$idx."/".$target);
}

/* XNODE_getpathbytarget() */
function XNODE_getpathbytarget($base, $node, $target, $value, $create)
{
	foreach($base."/".$node)
	{
		if (query($target) == $value)
			return $base."/".$node.":".$InDeX;
	}
	
	if ($create > 0)
	{
		$i = query($base."/".$node."#")+1;
		$path = $base."/".$node.":".$i;
		set($path."/".$target, $value);
		return $path;
	}

	return "";
}

function XNODE_getschedule($base)
{
	$sch = query($base."/schedule");
	if ($sch != "")
	{
		$sptr = XNODE_getpathbytarget("/schedule", "entry", "uid", $sch, 0);
		if ($sptr != "") return $sptr;
	}
	return "";
}

function XNODE_getscheduledays($sch)
{
	$days = "";
	$comm = "";
	if (query($sch."/sun")=="1") { $ret=$ret.$comm."Sun"; $comm=","; }
	if (query($sch."/mon")=="1") { $ret=$ret.$comm."Mon"; $comm=","; }
	if (query($sch."/tue")=="1") { $ret=$ret.$comm."Tue"; $comm=","; }
	if (query($sch."/wed")=="1") { $ret=$ret.$comm."Wed"; $comm=","; }
	if (query($sch."/thu")=="1") { $ret=$ret.$comm."Thu"; $comm=","; }
	if (query($sch."/fri")=="1") { $ret=$ret.$comm."Fri"; $comm=","; }
	if (query($sch."/sat")=="1") { $ret=$ret.$comm."Sat"; $comm=","; }
	return $ret;
}

/*If the device time is included by schedule, it would return 1. Otherwise, it would return 0.*/
function XNODE_getschedule2013checktime($sch_uid)
{
	setattr("/runtime/device/date_u", "get", "date +%u");
	$day = get("", "/runtime/device/date_u");
	del("/runtime/device/date_u");

	$time_now = get("", "/runtime/device/time");
	$time_now = cut($time_now, 0, ":").cut($time_now, 1, ":").cut($time_now, 2, ":"); // ex: 05:33:30 => 053330
	if(substr($time_now, 0, 1) == "0") {$time_now = substr($time_now, 1, strlen($time_now)-1);} // ex: 053330 => 53330

	$sch_path = XNODE_getpathbytarget("/schedule", "entry", "uid", $sch_uid, 0);
	foreach($sch_path."/entry")
	{
		$sch_day = get("", "date");
		$sch_time_start = get("", "start");
		$sch_time_end = get("", "end");

		if(cut_count($sch_time_start, ":") == 3) // ex: 12:53:11
		{
			$sch_time_start = cut($sch_time_start, 0, ":").cut($sch_time_start, 1, ":").cut($sch_time_start, 2, ":");
			$sch_time_end = cut($sch_time_end, 0, ":").cut($sch_time_end, 1, ":").cut($sch_time_end, 2, ":");
		}
		else // ex: 12:53
		{
			$sch_time_start = cut($sch_time_start, 0, ":").cut($sch_time_start, 1, ":")."00";
			$sch_time_end = cut($sch_time_end, 0, ":").cut($sch_time_end, 1, ":")."00";
		}

		if($day == $sch_day && $sch_time_start <= $time_now && $time_now < $sch_time_end)
		{return 1;}
	}

	return 0;
}

/* It would return as schedule_2013 "1-8-0,1-10-0,3-2-0,3-10-0" */
function XNODE_getschedule2013cmd($sch_uid)
{
	$sch_cmd = "schedule_2013 ";
	$sch_comma = "";
	$sch_all = "";
	$sch = "";
	$sch_path = XNODE_getpathbytarget("/schedule", "entry", "uid", $sch_uid, 0);
	if($sch_path == "") {return "";}
	foreach($sch_path."/entry")
	{
		if(get("", "date") == "0") // It meats all week. Schedule is unnessary.
		{return "start";}
		else if(get("", "date") == "7") // 7 is sunday in HNAP Spec. and 0 is sunday in Seattle.
		{$date = "0";}
		else
		{$date = get("", "date");}
		$start_hour = cut(get("", "start"), 0, ":");
		$start_min = cut(get("", "start"), 1, ":");
		$end_hour = cut(get("", "end"), 0, ":");
		$end_min = cut(get("", "end"), 1, ":");
		$start = $date."-".dec2strf('%d', $start_hour)."-".dec2strf('%d', $start_min);// Transfer 09 to 9 and so on.
		$end = $date."-".dec2strf('%d', $end_hour)."-".dec2strf('%d', $end_min);

		// If the end time is 24:00, it means the last minute of the day is included. Discuss with D-Link Timmy.
		if(get("", "end")=="24:00")
		{
			$date = $date + 1;
			if($date == 7) {$date = 0;}
			$end = $date."-0-0";
		}

		$sch = $start.",".$end;
		$sch = $sch_comma.$sch;
		$sch_comma = ",";
		$sch_all = $sch_all.$sch;
	}

	$sch_cmd = 'schedule_2013 "'.$sch_all.'"';
	return $sch_cmd;
}

function XNODE_getschedulename($sch_uid)
{
	$sch_path = XNODE_getpathbytarget("/schedule", "entry", "uid", $sch_uid, 0);
	return get("", $sch_path."/description");
}

function XNODE_getscheduleuid($sch_name)
{
	$sch_path = XNODE_getpathbytarget("/schedule", "entry", "description", $sch_name, 0);
	return get("", $sch_path."/uid");
}

function XNODE_get_var($name)
{
	$path = XNODE_getpathbytarget("/runtime/services/globals", "var", "name", $name, 1);
	return query($path."/value");
}

function XNODE_set_var($name, $value)
{
	$path = XNODE_getpathbytarget("/runtime/services/globals", "var", "name", $name, 1);
	set($path."/value", $value);
}

function XNODE_del_var($name)
{
	$path = XNODE_getpathbytarget("/runtime/services/globals", "var", "name", $name, 1);
	$value = query($path."/value");
	del($path);
	return $value;
}

function XNODE_del_children($path, $child)
{
	$cnt = query($path."/".$child."#");
	while ($cnt > 0) { del($path."/".$child); $cnt--; }
}

function XNODE_add_entry($base, $uid_prefix)
{
	$seqno = query($base."/seqno");
	$count = query($base."/count");
	$max   = query($base."/max");
	if ($seqno == "" && $count == "")
	{
		$seqno = 1; 
		$count = 0;
	}
	if ($max != "" && $count >= $max) return "";

	$uid = $uid_prefix."-".$seqno;
	$seqno++;
	$count++;
	set($base."/seqno", $seqno);
	set($base."/count", $count);
	set($base."/entry:".$count."/uid", $uid);
	return $base."/entry:".$count;
}
function XNODE_del_entry($base, $index)
{
	$count = query($base."/count");
	$count--;
	del($base."/entry:".$index);
	set($base."/count",$count);
}
?>
