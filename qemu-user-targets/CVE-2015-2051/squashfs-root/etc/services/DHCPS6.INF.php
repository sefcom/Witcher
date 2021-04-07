<?
include "/htdocs/phplib/xnode.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
foreach("/runtime/inf")
{
	$uid = query("uid");
	fwrite(a, $START, "service DHCPS6.".$uid." restart\n");
	fwrite(a, $STOP,  "service DHCPS6.".$uid." stop\n");
}
?>
