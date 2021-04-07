<?
include "/etc/services/HTTP/httpsvcs.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
fwrite("a",$START,"service IPT.WAN-1 restart\n");
fwrite("a",$START,"service STUNNEL restart\n");
httpsetup("WAN-1");
?>
