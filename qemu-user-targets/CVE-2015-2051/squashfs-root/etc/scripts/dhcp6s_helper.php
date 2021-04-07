#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function cmd($cmd) {echo $cmd."\n";}
function msg($msg) {cmd("echo ".$msg." > /dev/console");}

/****************************************/
function add_host()
{
	if ($_GLOBALS["INF"]=="") return "No INF !!";
	/* The runtime node of INF should already be created when starting INET service.
	 * Set the create flag to make sure is will always be created. */
	$sts = XNODE_getpathbytarget("/runtime", "inf", "uid", $_GLOBALS["INF"], 0);
	if ($sts=="") return $_GLOBALS["INF"]." does not exist!";
	if ($_GLOBALS["HOST"]=="") return "HOST does not exist!";
	if ($_GLOBALS["MAC"]=="") return "MAC does not exist!";

	msg('Add host '.$_GLOBALS["HOST"].'/'.$_GLOBALS["MAC"]);

	/* Update Status */
	$found = 0;
	foreach($sts.'/dhcps6/leases/entry')
	{
		$ipaddr = query($sts."/dhcps6/leases/entry:".$InDeX."/ipaddr");
		if($ipaddr == $_GLOBALS["HOST"])
		{
			anchor($sts."/dhcps6/leases/entry:".$InDeX);
			set("ipaddr", 	$_GLOBALS["HOST"]);
			set("macaddr", 	$_GLOBALS["MAC"]);
			$found = 1;
			break;
		}
	}
	if($found==0)
	{
		$cnt = query($sts."/dhcps6/leases/entry#");
		$cnt++;
		anchor($sts."/dhcps6/leases/entry:".$cnt);
		set("ipaddr", 	$_GLOBALS["HOST"]);
		set("macaddr", 	$_GLOBALS["MAC"]);
	}
}

function remove_host()
{
	if ($_GLOBALS["INF"]=="") return "No INF !!";
	/* The runtime node of INF should already be created when starting INET service.
	 * Set the create flag to make sure is will always be created. */
	$sts = XNODE_getpathbytarget("/runtime", "inf", "uid", $_GLOBALS["INF"], 0);
	if ($sts=="") return $_GLOBALS["INF"]." does not exist!";
	if ($_GLOBALS["HOST"]=="") return "HOST does not exist!";

	msg('Remove host '.$_GLOBALS["HOST"]);

	/* Update Status */
	foreach($sts.'/dhcps6/leases/entry')
	{
		$ipaddr = query($sts."/dhcps6/leases/entry:".$InDeX."/ipaddr");
		if($ipaddr == $_GLOBALS["HOST"])
		{
			del($sts.'/dhcps6/leases/entry:'.$InDeX);
			break;
		}
	}
}

function add_route()
{
	msg('add route '.$_GLOBALS["DST"]);
	cmd("ip -6 route add ".$_GLOBALS["DST"]." via ".$_GLOBALS["GATEWAY"]." dev ".$_GLOBALS["DEVNAM"]." table DHCP\n");
}

function remove_route()
{
	msg('remove route '.$_GLOBALS["DST"]);
	cmd("ip -6 route del ".$_GLOBALS["DST"]." table DHCP\n");
}

function main_entry()
{
	if	($_GLOBALS["ACTION"]=="ADD_HOST") return add_host();
	else if	($_GLOBALS["ACTION"]=="RM_HOST")  return remove_host();
	else if	($_GLOBALS["ACTION"]=="ADD_ROUTE")  return add_route();
	else if	($_GLOBALS["ACTION"]=="RM_ROUTE")  return remove_route();
	return "Unknown action - ".$_GLOBALS["ACTION"];
}

/*****************************************/
/* Required variables:
 *
 *	ACTION:		ADD_HOST/RM_HOST/ADD_ROUTE/RM_ROUTE
 *
 *	Param for ADD_HOST/RM_HOST
 *	INF:		Interface uid 
 *	HOST:		Host IPv6 address
 *	MAC:		Host Mac adress
 *	
 * 	Param for ADD_ROUTE/RM_ROUTE
 *	DST:		Lease IPv6 address range(PD)
 *	GATEWAY:	Client IPv6 address(LL)
 *	DEV:		device name
 */
$ret = main_entry();
if ($ret!="")	cmd("# ".$ret."\nexit 9\n");
else			cmd("exit 0\n");
?>
