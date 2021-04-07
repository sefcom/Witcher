<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/etc/services/INET/interface.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("a", $STOP, "#!/bin/sh\n");

if(query("/device/disable_nat")=="1")
{

}
else
{
/* start IPTABLES first */
fwrite("a",$START, "service IPTABLES start\n");
/* start IP6TABLES first */
if (isfile("/proc/net/if_inet6")==1)
	fwrite("a",$START, "service IP6TABLES start\n");
}
if (query("/runtime/device/layout")=="bridge")
{
	/* Start all LAN interfaces. */
	ifinetsetupall("BRIDGE");
	
	//restart power of LAN to make sure Bridge PC IP is correct when AP is changed.
	
	/* Power down LAN */
	fwrite("a",$START, "et -i eth0 robowr 0x10 0x0 0x800\n");
	fwrite("a",$START, "et -i eth0 robowr 0x11 0x0 0x800\n");
	fwrite("a",$START, "et -i eth0 robowr 0x12 0x0 0x800\n");
	fwrite("a",$START, "et -i eth0 robowr 0x13 0x0 0x800\n");
	
	fwrite("a",$START, "sleep 3\n");
	
	/* Enable LAN */
	fwrite("a",$START, "et -i eth0 robowr 0x10 0x0 0x8000\n");
	fwrite("a",$START, "et -i eth0 robowr 0x11 0x0 0x8000\n");
	fwrite("a",$START, "et -i eth0 robowr 0x12 0x0 0x8000\n");
	fwrite("a",$START, "et -i eth0 robowr 0x13 0x0 0x8000\n");
}
else
{
	SHELL_info($START, "BRIDGE: The device is not in the bridge mode.");
	SHELL_info($STOP,  "BRIDGE: The device is not in the bridge mode.");
}

/* Done */
fwrite("a",$START, "exit 0\n");
fwrite("a", $STOP, "exit 0\n");
?>
