#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

/**********************************************************/

function add_tunnel($mode, $type, $devnam, $inf, $remote, $local, $mtu)
{
	/* Prepare the remote/local commands. */
	if ($remote!="") $rcmd = " remote ".$remote;
	if ($local!="")  $lcmd = " local ".$local;

	/* add the tunnel. */
	if ($mode=="6IN4" || $mode=="6TO4" || $mode=="6RD")
	{
		echo "ip tunnel add ".$devnam." mode sit ttl 128".$rcmd.$lcmd."\n";
	}
	else if ($mode=="TSP")
	{
		if ($type=="v6v4")
			 echo "ip tunnel add ".$devnam." mode sit ttl 64".$rcmd.$lcmd."\n";
		else echo "ip tuntap add ".$devnam." mode tun\n";
	}
	else return "";

	if($mode=="6RD")
	{
		$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
		$ipaddr = query($p."/inet/ipv6/ipv6in4/rd/ipaddr");	
		$prefix = query($p."/inet/ipv6/ipv6in4/rd/prefix");
		$v4mask = query($p."/inet/ipv6/ipv6in4/rd/v4mask");
		$hubspoke = query($p."/inet/ipv6/ipv6in4/rd/hubspokemode");
		$networkid = ipv4networkid($local,$v4mask);
		if($v4mask!="0") $rpcmd = " 6rd-relay_prefix ".$networkid."/".$v4mask;
		else $rpcmd = "";
		if($hubspoke!="1")
		{
			echo "ip tunnel 6rd dev ".$devnam." 6rd-prefix ".$ipaddr."/".$prefix.$rpcmd."\n";
		}
	}


	/* Enable IPv6 on the tunnel device. */
	if ($mtu!="") echo "ip link set ".$devnam." mtu ".$mtu."\n";
	echo "ip link set ".$devnam." up\n";
	echo "echo 0 > /proc/sys/net/ipv6/conf/".$devnam."/disable_ipv6\n";

	$uid = "TUN.".$inf;
	$p = PHYINF_setup($uid, "tunnel", $devnam);
	set($p."/tunnel/mode",	$mode);
	set($p."/tunnel/type",	$type);
	set($p."/tunnel/remote",$remote);
	set($p."/tunnel/local",	$local);
	return $uid;
}

function del_tunnel($uid)
{
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 0);
	if ($p=="")
	{
		TRACE_debug("Woops !!!!!!! ".$uid." is not exist!");
		return;
	}
	anchor($p);

	$mode = query("tunnel/mode");
	$type = query("tunnel/type");
	$name = query("name");

	if		($mode=="6IN4")	$tunnel = "tunnel";
	else if	($mode=="6TO4")	$tunnel = "tunnel";
	else if	($mode=="6RD")	$tunnel = "tunnel";
	else if	($mode=="TSP")
	{
		if ($type=="v6v4")	$tunnel = "tunnel";
		else				$tunnel = "tuntap";
	}
	else return;

	/* The TSPC will destroy the tunnel device, so we don't need to del the tunnel. */
	if ($mode!="TSP")
	{
		echo "ip link set ".$name." down\n";
		if		($tunnel=="tunnel") echo "ip tunnel del ".$name."\n";
		else if ($tunnel=="tuntap")	echo "ip tuntap del ".$name." mode tun\n";
	}
	echo "/etc/scripts/delpathbytarget.sh /runtime phyinf uid ".$uid."\n";
}

/**********************************************************/

function prepare_tsp_child($stsp, $child)
{
	$mac = PHYINF_getphymac($child);
	$hostid = ipv6eui64($mac);

	/* If the prefix is less than 64, the child can use 64 bits prefix length. */
	$prefix	= $_GLOBALS["TSP_PREFIX"]."::";
	$plen	= $_GLOBALS["TSP_PREFIXLEN"];
	if ($plen<64) $plen = 64;

	$ipaddr = ipv6ip($prefix, $plen, $hostid, 0, 0);

	TRACE_debug("INET: TSP Child [".$child."] use ".$ipaddr."/".$plen);
	set($stsp."/child/uid", $child);
	set($stsp."/child/ipaddr", $ipaddr);
	set($stsp."/child/prefix", $plen);
}

/* Get IPv4 wan type and return the lease time. */
function get_wan_ipv4_lease_time()
{
	include "/htdocs/webinc/config.php";
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $WAN1, "0");
	if (get("",$stsp."/inet/addrtype")!="ipv4")
	{ return ""; }
	if (get("",$stsp."/inet/ipv4/static")!="0")
	{ return ""; }
	return get("",$stsp."/udhcpc/lease");
}

function tunnel_create($p)
{
	/* Create the tunnel */
	$phy = add_tunnel($_GLOBALS["MODE"], "", $_GLOBALS["DEVNAM"], $_GLOBALS["INF"],
					$_GLOBALS["REMOTE"], $_GLOBALS["LOCAL"], $_GLOBALS["MTU"]);
	if ($phy=="") return;

	/* Convert the IPv6 address to simplified format. */
	$ipaddr = ipv6ip($_GLOBALS["IPADDR"], 128, 0, 0, 0);
	$gateway= ipv6ip($_GLOBALS["GATEWAY"], 128, 0, 0, 0);
	/* TODO: DNS may also need to convert... */

	/* update the phyiscal interface the tunnel device. */
	set($p."/phyinf", $phy);

	// HuanYao: Get IPv4 lease time if IPv4 wan mode is DHCP. 
	$lease_time = get_wan_ipv4_lease_time();
	if ($lease_time == "")
	{ $lease_time = 60*60; }
	else if ($lease_time <= 60)
	{ $lease_time = 60; }
	else if ($lease_time >= 60*90)
	{ $lease_time = 60*90; }
	XNODE_set_var($_GLOBALS["INF"]."_ROUTERLFT", $lease_time);
	
	/* Attach IP address. */
	echo "phpsh /etc/scripts/IPV6.INET.php ACTION=ATTACH".
			" MODE=".$_GLOBALS["MODE"].
			" INF=".$_GLOBALS["INF"].
			" DEVNAM=".$_GLOBALS["DEVNAM"].
			" MTU=".$_GLOBALS["MTU"].
			" IPADDR=".$ipaddr.
			" PREFIX=".$_GLOBALS["PREFIX"].
			" GATEWAY=".$gateway.
			' "DNS='.$_GLOBALS["DNS"].'"'.
			"\n";

	/* Prepare the configuration for TSP child. */
	$child = query($p."/child/uid");
	if ($_GLOBALS["MODE"]=="TSP" && $child!="") prepare_tsp_child($p, $child);
	
	// HuanYao: Apply to child router lifetime. 
	XNODE_set_var($child."_ROUTERLFT", $lease_time);
}

function tunnel_destroy($p)
{
	$phy = query($p."/phyinf");
	echo "phpsh /etc/scripts/IPV6.INET.php ACTION=DETACH INF=".$_GLOBALS["INF"]."\n";
	del_tunnel($phy);
}

/* Main Entry *******************************************************/
$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $INF, 0);
if ($p=="") echo "# Woops!!!, No runtime nodes for ".$INF."\n";
else if ($ACTION=="CREATE")  tunnel_create($p);
else if ($ACTION=="DESTROY") tunnel_destroy($p);
else tunnel_create($p);
?>
