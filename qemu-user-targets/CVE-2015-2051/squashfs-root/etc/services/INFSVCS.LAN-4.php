<?
include "/etc/services/INFSVCS/infservices.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("W", $STOP, "#!/bin/sh\n");
infsvcs_lan("4");
?>
