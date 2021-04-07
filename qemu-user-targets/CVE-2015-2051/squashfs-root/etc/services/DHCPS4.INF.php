<?
include "/htdocs/phplib/xnode.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
foreach("/runtime/inf")
{
	$uid = query("uid");
	fwrite(a, $START, "service DHCPS4.".$uid." restart\n");
	fwrite(a, $STOP,  "service DHCPS4.".$uid." stop\n");
}
?>
