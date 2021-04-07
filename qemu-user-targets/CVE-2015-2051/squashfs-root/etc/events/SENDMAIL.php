<?
include "/htdocs/phplib/dumplog.php";
include "/htdocs/phplib/trace.php";

TRACE_debug("ACTION=".$ACTION);
$SendMailFlag = 1;
if ($ACTION == "LOGFULL")
{
	$logfull_enable = query("/device/log/email/logfull");
	TRACE_debug("logfull_enable=".$logfull_enable);
	if ($logfull_enable=="0")	$SendMailFlag = 0;
	else
	{
		$logfile = "/var/run/logfull.log";
		$type = query("/runtime/logfull/type");
		$path = "/runtime/logfull/".$type;
	
		if($type == "sysact") $str = "System";
		else if($type == "attack") $str = "Firewall and Security";
		else if($type == "drop") $str = "Router Status";	
		
		fwrite("w", $logfile, "\n[".$str."]\n");
		fwrite("a", $logfile, "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
		DUMPLOG_append_to_file($logfile, $path);
		del($path);
		
		echo 'logger -p 192.1 "Log of ['.$str.'] is full."\n';
	}
}
else
{
	/*write rg.log*/
	$logfile = "/var/run/rg.log";
	DUMPLOG_all_to_file($logfile);
}
TRACE_debug("SendMailFlag=".$SendMailFlag);
$enable       = query("/device/log/email/enable");
if($enable=="1" && $SendMailFlag==1)
{
	$from         = query("/device/log/email/from");
	$email_addr   = query("/device/log/email/to");
	$mail_subject = get(s,"/device/log/email/subject");
	$mail_server  = query("/device/log/email/smtp/server");
	$mail_port    = query("/device/log/email/smtp/port");
	if ($mail_port == ""){	$mail_port    = "25"; }
	$authenable = query("/device/log/email/authenable");
	$username  = query("/device/log/email/smtp/user");
	$password  = query("/device/log/email/smtp/password");
	
	fwrite("w", "/var/log/subject", $mail_subject);
	
	if ($from == "" || $email_addr == "" || $mail_server == "")
	{
		TRACE_error("sendmail: invalid args!!! from=[".$from."], to=[".$email_addr."], smtp server=[".$mail_server."]\n");
		return;
	}
	if ($mail_subject=="")$mail_subject = "RG log";
	echo 'logger -p 192.1 "Sending the Log to '.$email_addr.'"\n';
	/* static options */
	if ($authenable != "0")
	{
echo 'killall email\;';	
echo 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$username.
	 ' -s "'.$mail_subject.'"'.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' -tls'.
	 ' -m login'.
	 ' -u '.$username.
	 ' -i '.$password.
	 ' '.$email_addr.' &\n';
	 
TRACE_debug(
	 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$username.
	 ' -s "'.$mail_subject.'"'.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' -tls '.
	 ' -m login'.
	 ' -u '.$username.
	 ' -i '.$password.
	 ' '.$email_addr.' &\n'
	);
	}
	else
	{
echo 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$from.
	 ' -s "'.$mail_subject.'"'.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' '.$email_addr.' &\n';

TRACE_debug(
	 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$from.
	 ' -s "'.$mail_subject.'"'.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' '.$email_addr.' &\n'
	);
	}
}
?>
