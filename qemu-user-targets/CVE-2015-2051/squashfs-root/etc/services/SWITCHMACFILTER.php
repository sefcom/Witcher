<?
/*
# this file will be included by IPTMACCTRL.php.
# We keep original iptables mac filter,
# but if switch can support mac filter, we will include this.
# For more generic for different switch,
# we need "macfilter" command, and the command parameters follow "rtlioc macfilter".
*/

$DEBUG = 0;
$FILE= "/var/maclistfile";

fwrite("w",$FILE,  "");

$cmd = "macfilter reset\n";
if($DEBUG == 1) 
{
	fwrite("a",$STOP, "echo ".$cmd);
	fwrite("a",$START, "echo ".$cmd);
}

fwrite("a",$STOP, $cmd);			
fwrite("a",$START, $cmd);

foreach("/acl/macctrl/entry")
{
	$uid = query("uid");
	$enable = query("enable");
	$mac = query("mac");
	if($enable == 1) fwrite("a",$FILE, $mac."\n");		
}

$default_policy = query("/acl/macctrl/policy");
if($DEBUG == 1) fwrite("a",$START, "echo ".$default_policy."\n");

if($default_policy == "ACCEPT")
{																
	$cmd = "macfilter drop ".$FILE."\n";
	if($DEBUG == 1) fwrite("a",$START, "echo ".$cmd);
	fwrite("a",$START, $cmd);						
	
	$cpumac = query("/runtime/devdata/lanmac");
	$cmd = "macfilter cpumac ".$cpumac."\n";
	if($DEBUG == 1) fwrite("a",$START, "echo ".$cmd);
	fwrite("a",$START, $cmd);
}
else if($default_policy == "DROP")
{		
	$cmd = "macfilter allow ".$FILE."\n";
	if($DEBUG == 1) fwrite("a",$START, "echo ".$cmd);
	fwrite("a",$START, $cmd);						
	
	$cpumac = query("/runtime/devdata/lanmac");
	$cmd = "macfilter cpumac ".$cpumac."\n";
	if($DEBUG == 1) fwrite("a",$START, "echo ".$cmd);
	fwrite("a",$START, $cmd);
}
?>
