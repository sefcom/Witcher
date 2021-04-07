<?
include "/htdocs/mydlink/header.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";

$displaypass = $_GET["displaypass"];
$path_log		= "/device/log";
$syslog_enabled	= query($path_log."/remote/enable");
$syslog_addr	= query($path_log."/remote/ipv4/ipaddr");
$smtp_addr		= query($path_log."/email/to");
$smtp_subjuct	= query($path_log."/email/subject");
$smtp_form		= query($path_log."/email/from");
$smtp_server	= query($path_log."/email/smtp/server");
$smtp_auth		= query($path_log."/email/authenable");
if ($smtp_auth == 0)		{$smtp_auth="false";}
else if ($smtp_auth == 1)	{$smtp_auth="true";}
$smtp_username	= query($path_log."/email/smtp/user");
$smtp_password	= query($path_log."/email/smtp/password");
$smtp_port		= query($path_log."/email/smtp/port");
$smtp_secret		= query($path_log."/email/secret");
if ($smtp_secret == 0)		{$smtp_secret="false";}
else if ($smtp_secret == 1)	{$smtp_secret="true";}
		
?>
<emailsetting>
<config.log_to_syslog><?=$syslog_enabled?></config.log_to_syslog>
<config.log_syslog_addr><?=$syslog_addr?></config.log_syslog_addr>
<log_opt_system>true</log_opt_system>
<log_opt_dropPackets>true</log_opt_dropPackets>
<log_opt_SysActivity>true</log_opt_SysActivity>
<config.smtp_email_addr><?=$smtp_addr?></config.smtp_email_addr>
<config.smtp_email_subject><?=$smtp_subjuct?></config.smtp_email_subject>
<config.smtp_email_from_email_addr><?=$smtp_form?></config.smtp_email_from_email_addr>
<config.smtp_email_server_addr><?=$smtp_server?></config.smtp_email_server_addr>
<config.smtp_email_enable_smtp_auth><?=$smtp_auth?></config.smtp_email_enable_smtp_auth>
<config.smtp_email_acc_name><?=$smtp_username?></config.smtp_email_acc_name>
<config.smtp_email_pass><?if($displaypass==1){echo $smtp_password;}?></config.smtp_email_pass>
<config.smtp_email_port><?=$smtp_port?></config.smtp_email_port>
<config.smtp_email_secret><?=$smtp_secret?></config.smtp_email_secret>
</emailsetting>
