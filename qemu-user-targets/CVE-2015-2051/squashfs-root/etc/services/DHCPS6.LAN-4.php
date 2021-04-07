<?
include "/etc/services/DHCPS/dhcps6.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");
dhcps6setup("LAN-4");
?>
