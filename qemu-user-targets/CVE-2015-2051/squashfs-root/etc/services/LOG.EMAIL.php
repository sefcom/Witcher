<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
$log_sch=query("/device/log/email/logsch");

	$script_loop	= "/var/run/email_sch_loop.sh";
	fwrite("w",$START, "#!/bin/sh\n");
	fwrite("w",$STOP,  "#!/bin/sh\n");
	if($log_sch=="1")
	{
		fwrite("w",$script_loop, "#!/bin/sh\n");

		fwrite("a", $START, "chmod 755 ".$script_loop."\n");
		fwrite("a", $START, "sh ".$script_loop."\n");
		fwrite("a", $script_loop, "event SENDMAIL\n");
		fwrite("a", $script_loop, "xmldbc -t \"mailsch:3600:".$script_loop."\"\n");
		fwrite("a", $STOP, "xmldbc -k mailsch\n");
	}
	fwrite("a",$START, "exit 0\n");
	fwrite("a",$STOP,  "exit 0\n");

?>
