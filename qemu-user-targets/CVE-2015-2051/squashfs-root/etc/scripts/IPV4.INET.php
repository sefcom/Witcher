#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";

function add_each($list, $path, $node)
{
	//echo "# add_each(".$list.",".$path.",".$node.")\n";
	$i = 0;
	$cnt = scut_count($list, "");
	while ($i < $cnt)
	{
		$val = scut($list, $i, "");
		if ($val!="") add($path."/".$node, $val);
		$i++;
	}
	return $cnt;
}

function dev_detach($hasevent)
{
	$sts = XNODE_getpathbytarget("/runtime", "inf", "uid", $_GLOBALS["INF"], 0);
	if ($sts=="") return $_GLOBALS["INF"]." has no runtime nodes.";
	if (query($sts."/inet/addrtype")!="ipv4") return $_GLOBALS["INF"]." is not ipv4.";
	if (query($sts."/inet/ipv4/valid")!=1) return $_GLOBALS["INF"]." is not active.";
	$devnam = query($sts."/devnam");
	if ($devnam=="") return $_GLOBALS["INF"]." has no device name.";

	anchor($sts."/inet/ipv4");
	$ipaddr	= query("ipaddr");
	$mask	= query("mask");
	$srt	= query("sstrout#");
	$crt	= query("clsstrout#");
	echo "ip addr del ".$ipaddr."/".$mask." dev ".$devnam."\n";
	echo "ip route flush table ".$_GLOBALS["INF"]."\n";
	if ($srt>0 || $crt>0) echo "ip rule del table ".$_GLOBALS["INF"]."\n";
	if ($hasevent>0)
	{
		echo "rm -f /var/run/".$_GLOBALS["INF"].".UP\n";
		echo "event ".$_GLOBALS["INF"].".DOWN\n";
	}

	del($sts."/inet");
	del($sts."/udhcpc");
	if ( $_GLOBALS["INF"] != "BRIDGE-1")
		del($sts."/devnam");

		/*marco*/
	if($_GLOBALS["INF"]=="LAN-1")
	{
		echo "echo -n \"0\" > /proc/nf_accelerate_to_local \n";
	}
	
	$cnt = query($sts."/ipalias/cnt")+0;
	if($cnt!="" && $cnt!="0"){
		foreach($sts."/ipalias/ipv4/ipaddr"){
			if($InDeX > $cnt) break;
			$amask = "24";
			$max = ipv4maxhost($amask);
			$brd = ipv4ip($VaLuE, $amask, $max);
			set($sts."/inet/addrtype","ipv4"); 
			set($sts."/inet/ipv4/valid","1"); 
			set($sts."/inet/ipv4/ipaddr",$VaLuE); 
			set($sts."/inet/ipv4/mask",$amask);
			echo "ip addr add ".$VaLuE."/24 broadcast ".$brd." dev ".$devnam."\n";
		}
		echo "sleep 1\n";
		echo "event ".$_GLOBALS["INF"].".UP\n";
		if (isfile("/var/servd/".$_GLOBALS["INF"]."-udhcpd.pid")==1){
			echo "service DHCPS4.".$_GLOBALS["INF"]." restart\n";
		}
	}
}

function dev_attach($hasevent)
{
	$cfg = XNODE_getpathbytarget("", "inf", "uid", $_GLOBALS["INF"], 0);
	if ($cfg=="") return $_GLOBALS["INF"]."does not exist!";
	$sts = XNODE_getpathbytarget("/runtime", "inf", "uid", $_GLOBALS["INF"], 1);

	/* Just in case the device is still alive. */
	if (query($sts."/inet/ipv4/valid")==1) dev_detach(0);

	/* Get the defaultroute metric from config. */
	$defrt = query($cfg."/defaultroute");
	/* Get the netmask */
	if ($_GLOBALS["SUBNET"]!="") $mask = ipv4mask2int($_GLOBALS["SUBNET"]);
	else $mask = $_GLOBALS["MASK"];
	/* Get the broadcast address */
	if ($_GLOBALS["BROADCAST"]!="") $brd = $_GLOBALS["BROADCAST"];
	else
	{
		$max = ipv4maxhost($mask);
		$brd = ipv4ip($_GLOBALS["IPADDR"], $mask, $max);
	}
	/* MTU */
	if ($_GLOBALS["MTU"]=="") $mtu = 1500;
	else $mtu = $_GLOBALS["MTU"]+0; // convert to integer, just in case.
	/* Record the domain in /runtime/device for our global domain name. */
	$domain=$_GLOBALS["DOMAIN"];/*marco*/
	if ($domain != query("/runtime/device/domain"))
	{
		set("/runtime/device/domain", $domain);
		$restartdhcpswer = 1;
	}
	//Remove all alias ip
	$cnt = query($sts."/ipalias/cnt")+0;
	if($cnt!="" && $cnt!="0")
	{
		foreach($sts."/ipalias/ipv4/ipaddr")
		{
			if($InDeX > $cnt) break;
			$amask = "24";
			echo "ip addr del ".$VaLuE."/".$amask." dev ".$_GLOBALS["DEVNAM"]."\n";
		}
	}

	/***********************************************/
	/* Update Status */
	set($sts."/defaultroute",	$defrt);
	set($sts."/devnam",			$_GLOBALS["DEVNAM"]);
	set($sts."/inet/uid",		query($cfg."/inet"));
	set($sts."/inet/addrtype",	"ipv4");
	set($sts."/inet/uptime",	query("/runtime/device/uptime"));
	set($sts."/inet/ipv4/valid","1");
	/* INET */
	anchor($sts."/inet/ipv4");
	set("static",	$_GLOBALS["STATIC"]);
	set("mtu",		$mtu);
	set("ipaddr",	$_GLOBALS["IPADDR"]);
	set("mask",		$mask);
	set("gateway",	$_GLOBALS["GATEWAY"]);
	set("domain",	$_GLOBALS["DOMAIN"]);
	/* DNS & Routing */
	add_each($_GLOBALS["DNS"],      $sts."/inet/ipv4", "dns");
	add_each($_GLOBALS["SSTROUT"],  $sts."/inet/ipv4", "sstrout");   /* static route - DHCP option 0x21 */
	add_each($_GLOBALS["CLSSTROUT"],$sts."/inet/ipv4", "clsstrout"); /* classes static route - DHCP option 0x79, RFC 3442 */

	/************************************************/
	/* attach */
//marco
	if ($_GLOBALS["GATEWAY"]!="")
	{
		echo "echo 0 > /proc/sys/net/ipv4/ip_forward\n";
	}
	echo "ip link set ".$_GLOBALS["DEVNAM"]." mtu ".$mtu."\n";
	echo "ip addr add ".$_GLOBALS["IPADDR"]."/".$mask." broadcast ".$brd." dev ".$_GLOBALS["DEVNAM"]."\n";
	/*add alias again*/
	foreach($sts."/ipalias/ipv4/ipaddr"){
		if($InDeX > $cnt) break;
		$amask = "24";
		$max = ipv4maxhost($amask);
		$brd = ipv4ip($VaLuE, $amask, $max);
		echo "ip addr add ".$VaLuE."/24 broadcast + dev ".$_GLOBALS["DEVNAM"]."\n";
	}
	
	/* gateway */
	if ($_GLOBALS["GATEWAY"]!="")
	{
		$netid = ipv4networkid($_GLOBALS["IPADDR"], $mask);
		if ($defrt!="" && $defrt>0)
		{	
			echo "ip route add default via ".$_GLOBALS["GATEWAY"]." metric ".$defrt." table default\n";
		}
		else
		{	
			echo "ip route add ".$netid."/".$mask." dev ".$_GLOBALS["DEVNAM"]." src ".$_GLOBALS["IPADDR"]." table ".$_GLOBALS["INF"]."\n";
		}
	}

	/* PPTP/L2TP connection */
/* In order to support server address in FQDN, move this part to SETVPNSRRT.php, the server IP is determined just before pppd dialup */	
/*	
	$upperlayer = query($cfg."/upperlayer");
	if ($upperlayer!="")
	{
		$infp = XNODE_getpathbytarget("", "inf", "uid", $upperlayer, 0);
		if ($infp=="") return $upperlayer." does not exist!";
		
		$inet = query($infp."/inet");
		if ($inet=="") return $upperlayer." inet does not exit!";
		
		$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);

		if (query($inetp."/ppp4/over")=="pptp" || query($inetp."/ppp4/over")=="l2tp")
		{
			if (query($inetp."/ppp4/over")=="pptp")
			{
				$server = query($inetp."/ppp4/pptp/server");
			}
			else if (query($inetp."/ppp4/over")=="l2tp")
			{
				$server = query($inetp."/ppp4/l2tp/server");
			}
			$dev = $_GLOBALS["DEVNAM"];
			$ip = $_GLOBALS["IPADDR"];
			$gw = $_GLOBALS["GATEWAY"];
			
			if (INET_validv4network($ip, $server, $mask) == 1)
			{
				echo "ip route add ".$server." dev ".$dev."\n";
			}
			else
			{
				echo "ip route add ".$server." via ".$gw." dev ".$dev."\n";
			}
		}
	}
*/

	/* Routing */
	$hasroute=0;
	foreach("sstrout")
	{
		$ipaddr = cut($VaLuE, 0, ",");
		$mask	= cut($VaLuE, 2, ",");
		$gateway= cut($VaLuE, 3, ",");
		$netid	= ipv4networkid($ipaddr, $mask);
		echo "ip route add ".$netid."/".$mask." via ".$gateway." table ".$_GLOBALS["INF"]."\n";
		$hasroute++;
	}
	foreach("clsstrout")
	{
		$ipaddr = cut($VaLuE, 0, ",");
		$mask	= cut($VaLuE, 2, ",");
		$gateway= cut($VaLuE, 3, ",");
		$netid	= ipv4networkid($ipaddr, $mask);
		echo "ip route add ".$netid."/".$mask." via ".$gateway." table ".$_GLOBALS["INF"]."\n";
		$hasroute++;
	}
	if ($hasroute>0) echo "ip rule add table ".$_GLOBALS["INF"]." prio 30000\n";

	if ($hasevent>0)
	{
		echo "echo 1 > /var/run/".$_GLOBALS["INF"].".UP\n";
		echo "event ".$_GLOBALS["INF"].".UP\n";
	}
	/*marco*/
	if($_GLOBALS["INF"]=="LAN-1")
	{
		//echo "echo -n \"".$_GLOBALS["DEVNAM"]."\" > /proc/nf_accelerate_to_local \n";
		//hendry, close temp since can't work with loopback (port redirection)
		echo "echo -n \"0\" > /proc/nf_accelerate_to_local \n";
	}

	if($_GLOBALS["INF"] == "BRIDGE-1")
		echo "service DHCPS4.".$_GLOBALS["INF"]." stop\n";
}

function main_entry()
{
	if ($_GLOBALS["INF"]=="") return "No INF !!";
	if		($_GLOBALS["ACTION"]=="ATTACH") return dev_attach(1);
	else if	($_GLOBALS["ACTION"]=="DETACH") return dev_detach(1);
	return "Unknown action - ".$_GLOBALS["ACTION"];
}

/*****************************************/
$ret = main_entry();
if ($ret != "") echo "# ".$ret."\nexit 9\n";
else echo "exit 0\n";
/*****************************************/
?>
