#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function cmd($cmd)	{echo $cmd."\n";}
function msg($msg)	{cmd("echo [ENULA]: ".$msg." > /dev/console");}
function error($m)	{cmd("echo [ENULA]: ERROR: ".$m); return 9;}

/***********************************************************************/

function main_entry($inf, $devnam, $ipaddr, $prefix)
{

	$ipv6enable = fread("e", "/proc/sys/net/ipv6/conf/".$devnam."/disable_ipv6");
	if($ipv6enable=="0")
	{
		msg("Set ULA ... ");
		/* Start script */
		cmd("phpsh /etc/scripts/IPV6.INET.php ACTION=ATTACH".
			" MODE=UL".
			" INF=".$inf.
			" DEVNAM=".$devnam.
			" IPADDR=".$ipaddr.
			" PREFIX=".$prefix
		);
	}
	else
	{
		/* Generate wait script. */
		$enula = "/var/servd/INET.".$inf."-enula.sh";
		fwrite(w, $enula,
			"#!/bin/sh\n".
			"phpsh /etc/scripts/ENULA.php".
			" INF=".$inf.
			" DEVNAM=".$devnam.
			" IPADDR=".$ipaddr.
			" PREFIX=".$prefix.
			"\n"
		);

		/* Start script ... */
		cmd("chmod +x ".$enula);
		cmd('xmldbc -t "enula.'.$inf.':5:'.$enula.'"');
	}

	return 0;
}

/* Main entry */
main_entry(
	$_GLOBALS["INF"],
	$_GLOBALS["DEVNAM"],
	$_GLOBALS["IPADDR"],
	$_GLOBALS["PREFIX"]
	);
?>
