<?
include "/htdocs/phplib/xnode.php";
$path_run_inf_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);

anchor($path_run_inf_wan1."/ddns4");
$provider	= get("s", "provider"); 
$username	= get("s", "username");
$password	= get("s", "password");
$hostname	= get("s", "hostname");
$interval	= "21600";

if( $provider != "ORAY")
{
	$cmd = "susockc /var/run/ddnsd.susock DUMP ".$provider;
	setattr("uptime",	"get", $cmd." | scut -p uptime:");
	setattr("ipaddr",	"get", $cmd." | scut -p ipaddr:");
	setattr("status",	"get", $cmd." | scut -p state:");
	setattr("result",	"get", $cmd." | scut -p result:");
}
else
{
	setattr("uptime",  "get", "scut -p uptime: /var/run/peanut.info");
	setattr("ipaddr",  "get", "scut -p ip:     /var/run/peanut.info");
	setattr("status",  "get", "scut -p status: /var/run/peanut.info");
	setattr("usertype","get", "cat /var/run/peanut_user_type");
}

$addrtype	= query($path_run_inf_wan1."/inet/addrtype");
if ($addrtype == "ipv4")	$ipaddr = query($path_run_inf_wan1."/inet/ipv4/ipaddr");
else						$ipaddr = query($path_run_inf_wan1."/inet/ppp4/local");	

$set = 'SET "'.$provider.'" "'.$ipaddr.'" "'.$username.'" "'.$password.'" "'.$hostname.'" '.$interval;
$testtime = query($path_run_inf_wan1."/ddns4/testtime");
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
fwrite("a",$START,
	'susockc /var/run/ddnsd.susock '.$set.'\n'.
	'xmldbc -s '.$path_run_inf_wan1.'/ddns4/valid 1\n'.
	'xmldbc -s '.$path_run_inf_wan1.'/ddns4/provider '.$provider.'\n'.
	'xmldbc -s '.$path_run_inf_wan1.'/ddns4/testtimeCheck '.$testtime.'\n'.
	'exit 0\n');
fwrite("a", $STOP,
	'xmldbc -s '.$path_run_inf_wan1.'/ddns4/valid 0\n'.
	'susockc /var/run/ddnsd.susock DEL '.$provider.'\n'.
	'exit 0\n');
	
?>
