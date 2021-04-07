<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/etc/services/INET/interface.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

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
if (query("/runtime/device/layout")=="router")
{
	/* setup ipaddress for all WAN interfaces. */
	ifinetsetupall("WAN");

	/* LAN interface is needed by VSVR, PFWD & DMZ,
	 * they should be started after LAN. */
	fwrite("a",$START, "service IPTMASQ start\n");
	fwrite("a",$START, "service IPTVSVR start\n");
	fwrite("a",$START, "service IPTPFWD start\n");
	fwrite("a",$START, "service IPTPORTT start\n");
	fwrite("a",$START, "service IPTDMZ start\n");

	/* start IPT.ifname services for all WAN interfaces. */
	srviptsetupall("WAN");

	fwrite("a",$STOP, "service IPTDMZ stop\n");
	fwrite("a",$STOP, "service IPTPORTT stop\n");
	fwrite("a",$STOP, "service IPTPFWD stop\n");
	fwrite("a",$STOP, "service IPTVSVR stop\n");
	fwrite("a",$STOP, "service IPTMASQ stop\n");

	chkconnsetupall("WAN");
}
else
{
	SHELL_info($START, "WAN: The device is not in the router mode.");
	SHELL_info($STOP,  "WAN: The device is not in the router mode.");
}

/* Done */
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
