<?
include "/htdocs/phplib/trace.php";

function DUMPLOG_append_to_file($file, $base)
{
	if ($file=="" || $base=="")
	{
		TRACE_error("DUMPLOG_append_to_file: ERROR - invalid args: file=[".$file."], base=[".$base."]\n");
		return;
	}
	foreach($base."/entry")
	{
		$time = get("TIME.ASCTIME", "time");
		$msg = get("", "message");
		fwrite("a", $file, "[Time]".$time);
		fwrite("a", $file, "[Message:".$InDeX."]".$msg."\n");
		fwrite("a", $file, "--------------------------------------------------------------------------------------------\n");
	}
}

function DUMPLOG_all_to_file($file)
{
	if ($file == "")
	{
		TRACE_error("DUMPLOG_to_file: ERROR - Need destination file to dump.\n");
		return;
	}
	fwrite("w", $file, "");
	fwrite("a", $file, "[System]\n");
	fwrite("a", $file, "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
	DUMPLOG_append_to_file($file, "/runtime/log/sysact");

	fwrite("a", $file, "\n[Firewall & Security]\n");
	fwrite("a", $file, "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
	DUMPLOG_append_to_file($file, "/runtime/log/attack");

	fwrite("a", $file, "\n[Router Status]\n");
	fwrite("a", $file, "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
	DUMPLOG_append_to_file($file, "/runtime/log/drop");
}
?>
