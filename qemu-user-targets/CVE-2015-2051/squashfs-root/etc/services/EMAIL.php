<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

$sch = query("/device/log/email/logsch");
$sch_uid = query("/device/log/email/schedule");
if ($sch=="0" || $sch=="") $sch_cmd = "start";
else if($sch_uid == "") $sch_cmd = "start";
else
{
	$sch_cmd = XNODE_getschedule2013cmd($sch_uid);
	TRACE_debug("$sch_cmd=".$sch_cmd);
}
if($sch_cmd == "") $sch_cmd = "start";
fwrite(a, $START, 'service LOG.EMAIL '.$sch_cmd.'\n');
fwrite(a, $STOP, 'service LOG.EMAIL stop\n');

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
