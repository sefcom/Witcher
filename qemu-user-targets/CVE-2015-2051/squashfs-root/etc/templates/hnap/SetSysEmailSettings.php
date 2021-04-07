HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

<?
echo "\<\?xml version='1.0' encoding='utf-8'\?\>";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/encrypt.php";

$nodebase= "/runtime/hnap/SetSysEmailSettings";
$path_dev_log = "/device/log";
$result = "OK";

$SysEmail = query($nodebase."/SysEmail");
$EmailFrom = query($nodebase."/EmailFrom");
$EmailTo = query($nodebase."/EmailTo");
$EmailSubject = query($nodebase."/EmailSubject");
$SMTPServerAddress = query($nodebase."/SMTPServerAddress");
$SMTPServerPort = query($nodebase."/SMTPServerPort");
$Authentication = query($nodebase."/Authentication");
$AccountName = query($nodebase."/AccountName");
$AccountPassword = query($nodebase."/AccountPassword");
$AccountPassword = AES_Decrypt128($AccountPassword);
$OnLogFull = query($nodebase."/OnLogFull");
$OnSchedule = query($nodebase."/OnSchedule");
$ScheduleName = query($nodebase."/ScheduleName");

if($SysEmail == "true") { set($path_dev_log."/email/enable", "1"); }
else { set($path_dev_log."/email/enable", "0"); }

set($path_dev_log."/email/from", $EmailFrom);
set($path_dev_log."/email/to", $EmailTo);
set($path_dev_log."/email/subject", $EmailSubject);

set($path_dev_log."/email/smtp/server", $SMTPServerAddress);
set($path_dev_log."/email/smtp/port", $SMTPServerPort);

if($Authentication == "true")
{ 
	set($path_dev_log."/email/authenable", "1");
	set($path_dev_log."/email/smtp/user", $AccountName);
	set($path_dev_log."/email/smtp/password", $AccountPassword);
}
else { set($path_dev_log."/email/authenable", "0"); }

if($OnLogFull == "true") { set($path_dev_log."/email/logfull", "1"); }
else { set($path_dev_log."/email/logfull", "0"); }

if($OnSchedule == "true")
{ 
	set($path_dev_log."/email/logsch", "1");
	set($path_dev_log."/email/schedule", XNODE_getscheduleuid($ScheduleName));
}
else
{
	set($path_dev_log."/email/logsch", "0");
	set($path_dev_log."/email/schedule", "");
}

fwrite("w",$ShellPath, "#!/bin/sh\n");
fwrite("a",$ShellPath, "echo \"[$0]-->System Email Settings\" > /dev/console\n");
if($result=="OK")
{
	fwrite("a",$ShellPath, "event DBSAVE > /dev/console\n");
	fwrite("a",$ShellPath, "service DEVICE.LOG restart > /dev/console\n");
	fwrite("a",$ShellPath, "service EMAIL restart > /dev/console\n");
	fwrite("a",$ShellPath, "xmldbc -s /runtime/hnap/dev_status '' > /dev/console\n");
	set("/runtime/hnap/dev_status", "ERROR");
}
else
{
	fwrite("a",$ShellPath, "echo \"We got a error in setting, so we do nothing...\" > /dev/console");
}

?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
	xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<SetSysEmailSettingsResponse xmlns="http://purenetworks.com/HNAP1/">
			<SetSysEmailSettingsResult><?=$result?></SetSysEmailSettingsResult>
		</SetSysEmailSettingsResponse>
	</soap:Body>
</soap:Envelope>