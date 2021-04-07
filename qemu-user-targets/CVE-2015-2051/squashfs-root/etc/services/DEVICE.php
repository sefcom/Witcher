<?
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

if(query("/device/eee")=="1") fwrite("w",$START, "rtlioc eneee\n");
else if(query("/device/eee")=="0") fwrite("w",$START, "rtlioc diseee\n");

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
