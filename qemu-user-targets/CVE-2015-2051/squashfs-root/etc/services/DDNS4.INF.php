<?
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
foreach ("/inf")
{
	$uid = query("uid");
	$prefix = cut($uid,0,'-');
	if ($prefix == "WAN")
	{
		fwrite("a",$START,"service DDNS4.".$uid." restart\n");
		fwrite("a", $STOP,"service DDNS4.".$uid." stop\n");
	}
}
?>
