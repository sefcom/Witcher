<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");} 

fwrite(w,$_GLOBALS["START"], "#!/bin/sh\n");
fwrite(w,$_GLOBALS["STOP"], "#!/bin/sh\n"); 

function setup_nameresolv($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		TRACE_debug("SERVICES/DEVICE.HOSTNAME: ifname = ".$ifname);
		startcmd("service NAMERESOLV.".$ifname." restart");
		$i++;
	}
}

function setup_dhcpc($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		if (query($ifpath."/inet/addrtype")=="ipv4" && query($ifpath."/inet/ipv4/static")=="0" 
			&& query($ifpath."/inet/ipv4/valid")=="1")
		{
			TRACE_debug("Restart DHCP client: ifname = ".$ifname);
			startcmd("service INET.".$ifname." restart");
		}
		$i++;
	}
}

setup_nameresolv("LAN");
/* If WAN mode is DHCP, restart DHCP client */
setup_dhcpc("WAN");

/* If have MDNSRESPONDER, and update hostname. */
if (query("/device/mdnsresponder/enable")=="1")
{
	startcmd("service MDNSRESPONDER restart");
}

/* Support device name resolving on Mac/ipad/iphone. 
Restart the DNS service to renew the file /var/hosts. */
startcmd("service DNS restart");
?>
