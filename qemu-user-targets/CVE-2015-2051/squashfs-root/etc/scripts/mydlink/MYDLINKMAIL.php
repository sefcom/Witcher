<?
include "/htdocs/phplib/dumplog.php";
include "/htdocs/phplib/trace.php";

//TRACE_debug("SUBJECTPATH=".$SUBJECTPATH);
//TRACE_debug("MAILPATH=".$MAILPATH);

$enable       = query("/device/log/email/enable");
if($enable=="1")
{
	$from         = query("/device/log/email/from");
	$email_addr   = query("/device/log/email/to");
	$mail_subject = "mydlink";
	$mail_server  = query("/device/log/email/smtp/server");
	$mail_port    = query("/device/log/email/smtp/port");
	if ($mail_port == ""){	$mail_port    = "25"; }
	$authenable = query("/device/log/email/authenable");
	$username  = $from;
	$displayfrom = "mydlink";
	$password  = query("/device/log/email/smtp/password");
	$logfile = $MAILPATH;	
	
	if ($from == "" || $email_addr == "" || $mail_server == "")
	{
		TRACE_error("sendmail: invalid args!!! from=[".$from."], to=[".$email_addr."], smtp server=[".$mail_server."]\n");
		return;
	}
	/* static options */
	if ($authenable != "0")
	{
echo 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$displayfrom.
	 ' -s "'.$mail_subject.'"'.
	 ' -W '.$SUBJECTPATH.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' -html'.
	 ' -tls '.
	 ' -m login'.
	 ' -u '.$username.
	 ' -i '.$password.
	 ' '.$email_addr.' &\n';
	 
TRACE_debug(
	 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$displayfrom.
	 ' -s "'.$mail_subject.'"'.
	 ' -W '.$SUBJECTPATH.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' -html'.
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
	 ' -n '.$username.
	 ' -s "'.$mail_subject.'"'.
	 ' -W '.$SUBJECTPATH.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' -html'.
	 ' '.$email_addr.' &\n';

TRACE_debug(
	 'email'.
	 ' -V '.
	 ' -f '.$from.
	 ' -n '.$username.
	 ' -s "'.$mail_subject.'"'.
	 ' -W '.$SUBJECTPATH.
	 ' -r '.$mail_server.
	 ' -z '.$logfile.
	 ' -p '.$mail_port.
	 ' -html '.
	 ' '.$email_addr.' &\n'
	);
	}
}
?>
