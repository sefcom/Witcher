<?
include "/etc/services/DHCPC/dhcpc6.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");
dhcpc6setup("WAN-4");
?>
