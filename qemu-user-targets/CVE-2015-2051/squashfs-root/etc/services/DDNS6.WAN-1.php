<?
include "/etc/services/DDNS/ddnsserver.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
ddns6setup("WAN-4","WAN-1");

?>
