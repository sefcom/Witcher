<? include "/htdocs/phplib/html.php";
if($Remove_XML_Head_Tail != 1)	{HTML_hnap_200_header();}

include "/htdocs/webinc/config.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/encrypt.php";

$path_dev_log = "/device/log";
$reslut = "OK";

if (get("x", $path_dev_log."/email/enable") == "1") { $enable = true; }
else { $enable = false; }

$email_from = get("x", $path_dev_log."/email/from");
$email_to = get("x", $path_dev_log."/email/to");
$email_subj = get("x", $path_dev_log."/email/subject");

$smt_addr = get("x", $path_dev_log."/email/smtp/server");
if (get("x", $path_dev_log."/email/smtp/port") == "") { $smt_port = "25"; }
else { $smt_port = get("x", $path_dev_log."/email/smtp/port"); }

if (get("x", $path_dev_log."/email/authenable") == "1") { $auth = true; }
else { $auth = false; }
$name = get("x", $path_dev_log."/email/smtp/user");
$passwd = get("x", $path_dev_log."/email/smtp/password");

if (get("x", $path_dev_log."/email/logfull") == "1") { $log_full = true; }
else { $log_full = false; }
if (get("x", $path_dev_log."/email/logsch") == "1") { $log_sch = true; }
else { $log_sch = false; }
$sch_name = XNODE_getschedulename(get("x", $path_dev_log."/email/schedule"));

?>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_header();}?>
		<GetSysEmailSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<GetSysEmailSettingsResult><?=$reslut?></GetSysEmailSettingsResult>
			<SysEmail><?=$enable?></SysEmail>
			<EmailFrom><?=$email_from?></EmailFrom>
			<EmailTo><?=$email_to?></EmailTo>
			<EmailSubject><?=$email_subj?></EmailSubject>
			<SMTPServerAddress><?=$smt_addr?></SMTPServerAddress>
			<SMTPServerPort><?=$smt_port?></SMTPServerPort>
			<Authentication><?=$auth?></Authentication>
			<AccountName><?=$name?></AccountName>
			<AccountPassword><? echo AES_Encrypt128($passwd); ?></AccountPassword>
			<OnLogFull><?=$log_full?></OnLogFull>
			<OnSchedule><?=$log_sch?></OnSchedule>
			<ScheduleName><?=$sch_name?></ScheduleName>
		</GetSysEmailSettingsResponse>
<? if($Remove_XML_Head_Tail != 1)	{HTML_hnap_xml_tail();}?>