<?
include "/etc/services/DHCPS/dhcpserver.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
dhcps4setup("LAN-1");
?>
