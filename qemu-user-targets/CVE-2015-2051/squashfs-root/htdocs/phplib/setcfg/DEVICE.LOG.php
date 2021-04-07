<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";

$level = query($SETCFG_prefix."/device/log/level");

TRACE_debug("SETCFG/DEVICE.LOG: /device/log/level = ".$level);
set("/device/log/level", $level);

$subject = query($SETCFG_prefix."/device/log/email/subject");
TRACE_debug("SETCFG/DEVICE.LOG: /device/log/emai/subject = ".$subject);
fwrite("w", "/var/log/subject", $subject);

set("/device/log/email", "");
movc($SETCFG_prefix."/device/log/email", "/device/log/email");

//set("/device/log/remote", "");
//movc($SETCFG_prefix."/device/log/remote", "/device/log/remote");
?>
