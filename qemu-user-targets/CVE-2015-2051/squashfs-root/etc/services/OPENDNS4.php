<?
fwrite("w", $START, "#!/bin/sh\n");
fwrite("a", $START, "service DNS restart\n");
fwrite("a", $START, "/etc/events/UPDATERESOLV.sh\n");
fwrite("a", $START, "service DHCPS4.LAN-1 restart\n");
fwrite("a", $START, "service DHCPS4.LAN-2 restart\n");
fwrite("w", $STOP,  "#!/bin/sh\n");
fwrite("a", $STOP,  "service DNS restart\n");
?>
