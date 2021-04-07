<?
fwrite("w",$START,"#!/bin/sh\n");
fwrite("a",$START,"iptables -t nat -A PRE.BRIDGE-1 -j ACCEPT\n");
fwrite("a",$START,"exit 0\n");

fwrite("w",$STOP, "#!/bin/sh\n");
fwrite("a",$STOP,"iptables -t nat -F PRE.BRIDGE-1\n");
fwrite("a",$STOP, "exit 0\n");
?>
