#!/bin/sh
<?
function msg($m) {return 'echo "[INF-RESTART]: '.$m.'" > /dev/console';}
function say($m) {echo msg($m)."\n";}

if ($PREFIX!="")
{
	say("Stop [".$PREFIX."] services ...");
	echo "service ".$PREFIX." stop\n";
}
else
{
	say("Stop all INET services ...");
	echo "service WAN stop\n";
	echo "service LAN stop\n";
	echo "service BRIDGE stop\n";
}

foreach ("/runtime/inf")
{
	$uid = query("uid");
	if ($PREFIX!="" && cut($uid,0,'-')!=$PREFIX) continue;
	$mark = "/var/run/".$uid.".UP";
	$msg  = "Wait for ".$uid." to stop ...";
	echo 'while [ -f '.$mark.' ]; do '.msg($msg).'; sleep 1; done\n';
}

if ($PREFIX!="")
{
	say("Start [".$PREFIX."] service ...");
	echo "service ".$PREFIX." start\n";
}
else
{
	say("Start all INET services");
	echo "service BRIDGE start\n";
	echo "service LAN start\n";
	echo "service WAN start\n";
}
?>
