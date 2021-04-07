<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");} 

fwrite(w,$_GLOBALS["START"], "#!/bin/sh\n");
fwrite(w,$_GLOBALS["STOP"], "#!/bin/sh\n"); 

startcmd("service UPNPAV stop");
startcmd("service ITUNES stop");
startcmd("service NETATALK stop");
startcmd("service SAMBA stop");
//startcmd("phpsh /etc/scripts/webaccess_map.php");
startcmd("service WEBACCESS stop");

startcmd("service UPNPAV start");
startcmd("service ITUNES start");
startcmd("service NETATALK start");
startcmd("service SAMBA start");
/* when usb mount/umount need to update web file access map table, UI don't need */ 
startcmd("phpsh /etc/scripts/webaccess_map.php");
startcmd("service WEBACCESS start");


?>
