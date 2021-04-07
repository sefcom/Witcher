<? /* vi: set sw=4 ts=4: */
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"] = $result;
	$_GLOBALS["FATLADY_node"]   = $node;
	$_GLOBALS["FATLADY_message"]= $message;
}

set_result("FAILED","","");
$rlt = "0";
$cnt = query($FATLADY_prefix."/schedule/count");

foreach ($FATLADY_prefix."/schedule/entry")
{
	if ($InDeX > $cnt) break;
	$start_time_hr = cut(query("start"), 0, ":");
	$start_time_min = cut(query("start"), 1, ":");
	$end_time_hr = cut(query("end"), 0, ":");
	$end_time_min = cut(query("end"), 1, ":");

	if ($start_time_hr <= 0 && $start_time_min <=0 &&
		$end_time_hr >= 23 && $end_time_min >= 59) $wholeday="yes";

	if (query("description")=="")
	{
		set_result("FAILED",$FATLADY_prefix."/entry:".$InDeX,i18n("Please input the schedule name."));
		$rlt = "-1";
		break;
	}

	if (query("sun")!="1" && query("mon")!="1" && query("tue")!="1" && query("wed")!="1" &&
		query("thu")!="1" && query("fri")!="1" && query("sat")!="1")
	{
		set_result("FAILED",$FATLADY_prefix."/entry:".$InDeX,i18n("Please select the working day(s)."));
		$rlt = "-1";
		break;
	}

	if (isdigit($start_time_min)==0||isdigit($end_time_min)==0)
	{
		set_result("FAILED",$FATLADY_prefix."/entry:".$InDeX,i18n("The time is invalid.")
					." ".i18n("Wrong value."));
		$rlt = "-1";
		break;
	}

	if ($start_time_min>59||$end_time_min>59)
	{
		set_result("FAILED",$FATLADY_prefix."/entry:".$InDeX,i18n("The input minute is out of range."));
		$rlt = "-1";
		break;
	}

	$stime = $start_time_hr * 60 + $start_time_min;
	$etime = $end_time_hr * 60 + $end_time_min;
/**
 *	if ($stime >= $etime)
 *	{
 *		set_result("FAILED",$FATLADY_prefix."/entry:".$InDeX,i18n("The time frame is invalid.")
 *					." ".i18n("The start time should be less than the end time."));
 *		$rlt = "-1";
 *		break;
 *	}
*/
	if (query("sun")!="1" && query("mon")!="1" && query("tue")!="1" && query("wed")!="1" &&
		query("thu")!="1" && query("fri")!="1" && query("sat")=="0" && $wholeday=="yes")
	{
		set_result("FAILED",$FATLADY_prefix."/entry:".$InDeX,i18n("Invalid rule, the schedule is always active."));
		$rlt = "-1";
		break;
	}

	if (query("exclude")!="1") set("exclude", "0");
}

if ($rlt=="0")
{
	set($FATLADY_prefix."/valid", "1");
	set_result("OK", "", "");
}
?>
