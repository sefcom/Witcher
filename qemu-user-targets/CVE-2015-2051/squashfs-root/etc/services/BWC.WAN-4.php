<?
include "/etc/services/BWC/bwcsvcs.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
bwc_setup("WAN-4");
?>
