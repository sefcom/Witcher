<?
include "/htdocs/phplib/trace.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$cfg = query("/device/layout");
$sts = query("/runtime/device/layout");

$smode = query("/device/router/mode");
$rmode = query("/runtime/device/router/mode");

//if ($cfg != $sts) fwrite(a,$START, 'xmldbc -t "rc0:1:/etc/init0.d/rcS"\n');
if ($cfg != $sts) fwrite(a,$START, 'event REBOOT\n');
else fwrite("a",$START, "echo \"The layout of the device is not changed.\" > /dev/console\n");

if ($smode != $rmode) fwrite(a,$START, "service LAYOUT restart\n");
else fwrite("a",$START, "echo \"The router mode is not changed.\" > /dev/console\n");

/* Done */
fwrite("a",$START, "exit 0\n");
fwrite("a", $STOP, "exit 0\n");
?>
