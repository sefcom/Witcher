<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

function PHYINF_getifname($phyinf)
{
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	if ($p != "") return query($p."/name");
	return "";
}

function PHYINF_getruntimephypath($inf)
{
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	if ($stsp=="") return "";
	$phyinf = query($stsp."/phyinf");
	if ($phyinf=="") return "";
	$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	return $phyp;
}

function PHYINF_getruntimeifname($inf)
{
	$phyp = PHYINF_getruntimephypath($inf);
	if ($phyp=="") return "";
	if (query($phyp."/valid") == "1") return query($phyp."/name");
	return "";
}

function PHYINF_getruntimephymac($inf)
{
	$phyp = PHYINF_getruntimephypath($inf);
	if ($phyp=="") return "";
	if (query($phyp."/valid") == "1") return query($phyp."/macaddr");
	return "";
}

function PHYINF_getphypath($inf)
{
	$stsp = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);
	if ($stsp=="") return "";
	$phyinf = query($stsp."/phyinf");
	if ($phyinf=="") return "";
	$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	return $phyp; 
}

function PHYINF_getphyinf($inf)
{
	$phyp = PHYINF_getphypath($inf);
	if ($phyp=="") return "";
	return query($phyp."/name");
}

function PHYINF_getphymac($inf)
{
	$phyp = PHYINF_getphypath($inf);
	if ($phyp=="") return "";
	return query($phyp."/macaddr");
}

function devipv6addr($dev, $scope, $field)
{
	return "ip -f inet6 addr show dev ".$dev." scope ".$scope." | ".
			"scut -p inet6 | cut -d/ -f".$field;
}

function PHYINF_setup($uid, $type, $inf)
{
	$path = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 1);
	set($path."/valid",	"1");
	set($path."/type", $type);
	set($path."/name",	$inf);

	setattr($path."/mtu",				"get","ip -f link link show dev ".$inf." | scut -p mtu");
	setattr($path."/macaddr",			"get","ip -f link addr show dev ".$inf." | scut -p link/ether");
	setattr($path."/ipv6/link/ipaddr",	"get", devipv6addr($inf, "link", 1));
	setattr($path."/ipv6/link/prefix",	"get", devipv6addr($inf, "link", 2));
	setattr($path."/ipv6/global/ipaddr","get", devipv6addr($inf, "global", 1));
	setattr($path."/ipv6/global/prefix","get", devipv6addr($inf, "global", 2));
	setattr($path."/stats/rx/bytes",	"get","scut -p ".$inf.": -f 1 /proc/net/dev");
	setattr($path."/stats/rx/packets",	"get","scut -p ".$inf.": -f 2 /proc/net/dev");
	setattr($path."/stats/rx/multicast","get","scut -p ".$inf.": -f 8 /proc/net/dev");
	setattr($path."/stats/rx/error",	"get","scut -p ".$inf.": -f 3 /proc/net/dev");
	setattr($path."/stats/rx/drop",		"get","scut -p ".$inf.": -f 4 /proc/net/dev");
	setattr($path."/stats/tx/bytes",	"get","scut -p ".$inf.": -f 9 /proc/net/dev");
	setattr($path."/stats/tx/packets",	"get","scut -p ".$inf.": -f 10 /proc/net/dev");
	setattr($path."/stats/tx/error",	"get","scut -p ".$inf.": -f 11 /proc/net/dev");
	setattr($path."/stats/tx/drop",		"get","scut -p ".$inf.": -f 12 /proc/net/dev");
	setattr($path."/stats/tx/collision","get","scut -p ".$inf.": -f 14 /proc/net/dev");
	setattr($path."/stats/reset",		"set","/etc/scripts/resetstats.sh ".$inf);
	
	//longlay 20131216, fix packets count display problem when enable Broadcom CTF module
	if($uid=="ETH-1")
	{
		setattr($path."/stats/rx/bytes0",	"get","et -i eth0 robord 0x20 0x50 8");
		setattr($path."/stats/rx/bytes1",	"get","et -i eth0 robord 0x21 0x50 8");
		setattr($path."/stats/rx/bytes2",	"get","et -i eth0 robord 0x22 0x50 8");
		setattr($path."/stats/rx/bytes3",	"get","et -i eth0 robord 0x23 0x50 8");
		setattr($path."/stats/rx/packets0u",	"get","et -i eth0 robord 0x20 0x94 8");
		setattr($path."/stats/rx/packets0m",	"get","et -i eth0 robord 0x20 0x98 8");
		setattr($path."/stats/rx/packets0b",	"get","et -i eth0 robord 0x20 0x9c 8");
		setattr($path."/stats/rx/packets1u",	"get","et -i eth0 robord 0x21 0x94 8");
		setattr($path."/stats/rx/packets1m",	"get","et -i eth0 robord 0x21 0x98 8");
		setattr($path."/stats/rx/packets1b",	"get","et -i eth0 robord 0x21 0x9c 8");
		setattr($path."/stats/rx/packets2u",	"get","et -i eth0 robord 0x22 0x94 8");
		setattr($path."/stats/rx/packets2m",	"get","et -i eth0 robord 0x22 0x98 8");
		setattr($path."/stats/rx/packets2b",	"get","et -i eth0 robord 0x22 0x9c 8");
		setattr($path."/stats/rx/packets3u",	"get","et -i eth0 robord 0x23 0x94 8");
		setattr($path."/stats/rx/packets3m",	"get","et -i eth0 robord 0x23 0x98 8");
		setattr($path."/stats/rx/packets3b",	"get","et -i eth0 robord 0x23 0x9c 8");
		setattr($path."/stats/tx/bytes0",	"get","et -i eth0 robord 0x20 0x00 8");
		setattr($path."/stats/tx/bytes1",	"get","et -i eth0 robord 0x21 0x00 8");
		setattr($path."/stats/tx/bytes2",	"get","et -i eth0 robord 0x22 0x00 8");
		setattr($path."/stats/tx/bytes3",	"get","et -i eth0 robord 0x23 0x00 8");
		setattr($path."/stats/tx/packets0",	"get","et -i eth0 robord 0x20 0x0c 8");
		setattr($path."/stats/tx/packets1",	"get","et -i eth0 robord 0x21 0x0c 8");
		setattr($path."/stats/tx/packets2",	"get","et -i eth0 robord 0x22 0x0c 8");
		setattr($path."/stats/tx/packets3",	"get","et -i eth0 robord 0x23 0x0c 8");
	}
	else if($uid=="ETH-3")
	{
		setattr($path."/stats/rx/bytes4",	"get","et -i eth0 robord 0x24 0x50 8");
		setattr($path."/stats/rx/packets4u",	"get","et -i eth0 robord 0x24 0x94 8");
		setattr($path."/stats/rx/packets4m",	"get","et -i eth0 robord 0x24 0x98 8");
		setattr($path."/stats/rx/packets4b",	"get","et -i eth0 robord 0x24 0x9c 8");
		setattr($path."/stats/tx/bytes4",	"get","et -i eth0 robord 0x24 0x00 8");
		setattr($path."/stats/tx/packets4",	"get","et -i eth0 robord 0x24 0x0c 8");
	}

	return $path;
}

/* mac normalize is used for mac comparison, 
 * it should make sure the mac addresses format are the same before comparison. */
function PHYINF_macnormalize($macaddr)
{
	if (strstr($macaddr, ":") > 0) { $delimiter = ":"; }
	else if (strstr($macaddr, "-") > 0) { $delimiter = "-"; }
	else if (strstr($macaddr, " ") > 0) { $delimiter = " "; }
	
	$count =  cut_count($macaddr, $delimiter);
	
	$i = 0;
	while ($i < $count)
	{
		$part = cut($macaddr, $i, $delimiter);
		$res = $res.$part;
		$i++;
	}
	if($res!="")	{$res = tolower($res);}
	else			{$res = tolower($macaddr);}
	return $res;
}

function PHYINF_validmacaddr($macaddr)
{
	if		(cut_count($macaddr, ":")==6) $delimiter = ":";
//	else if	(cut_count($macaddr, "-")==6) $delimiter = "-";		//ip doesn't support the format xx-xx-xx-xx-xx-xx
	else return 0;

	$i = 0;
	while ($i < 6)
	{
		$v = cut($macaddr, $i, $delimiter);
		if (strlen($v)!=2 || isxdigit($v)!=1) return 0;
		if ($i==0)
		{
			//hendry : check for multicast mac	
			$val = strtoul($v, 16);
			$ismulti = $val%2;
			if($ismulti==1)  { return 0;}
		}
		$i++;
	}

	if(toupper($macaddr)=="FF:FF:FF:FF:FF:FF" || $macaddr=="01:11:11:11:11:11" || $macaddr=="00:00:00:00:00:00")
	{
		return 0;	
	}

	return 1;
}

/*
Generate random mac address to avoid confile while MP
*/
function PHYINF_getrandommac()
{
    $ran_idx=3;
    $idx=0;
    $prefix_mac="00:de:fa";
    $mac = "";

    $mac = $mac.$prefix_mac;

    while($idx < $ran_idx)
    {
	    $idx++;
	    $ran=rand()%255;
	    $ran=strtoul($ran, 10);
	    $ran_hex=dec2strf("%02x",$ran);
	    $mac=$mac.":".$ran_hex;
	
	}
	return $mac;
}
																			
/* Get the configured MAC address for the physical interface.
 * $mode:
 *		1BRIDGE	- Bridge mode with 1 interface.
 *		1W1L	- Router mode with 1 WAN, 1 LAN interfaces.
 *		1W2L	- Router mode with 1 WAN, 2 LAN interfaces.
 *
 *  INF\MODE    1BRIDGE     1W1L        1W2L
 *  ----------- ----------- ----------- ----------
 *  ETH-1       BRIDGE-1    LAN-1       LAN-1
 *  ETH-2       not used    WAN-1       LAN-2
 *  ETH-2       not used    not used    WAN-1
 */
function PHYINF_getdevdatamac($name)
{
	$mac = query("/runtime/devdata/".$name);
	if ($mac == "")
	{
		if		($name=="wanmac")	$mac = PHYINF_getrandommac();// "00:de:fa:3a:01:00";
		else if	($name=="wanmac2")	$mac = PHYINF_getrandommac();// "00:de:fa:3a:02:00";
		else if	($name=="lanmac")	$mac = PHYINF_getrandommac();// "00:de:fa:1a:01:00";
		else if	($name=="lanmac2")	$mac = PHYINF_getrandommac();// "00:de:fa:1a:02:00";
	}
	if ($mac=="") $mac = "00:de:fa:00:01:00"; /* Always return somethying. */
	return $mac;
}

function PHYINF_gettargetmacaddr($mode, $ifname)
{
	/* Get the MAC address from the physical interface setting. */
	$path = XNODE_getpathbytarget("", "phyinf", "uid", $ifname, 0);
	if ($path!="") $mac = query($path."/macaddr");
	if ($mac=="")
	{
		if ($mode=="1BRIDGE")   
		{	/* Only 1 interface, use WAN MAC address */
			if		($ifname=="ETH-1") $mac = PHYINF_getdevdatamac("wanmac");
		}
		else if ($mode=="1W1L")
		{	/* 2 interfaces, ETH-1 is LAN and ETH-2 is WAN */
			if      ($ifname=="ETH-1") $mac = PHYINF_getdevdatamac("lanmac");
			else if ($ifname=="ETH-2") $mac = PHYINF_getdevdatamac("wanmac");
		}
		else if ($mode=="1W2L")
		{	/* 3 interfaces, ETH-1 is LAN1, ETH-2 is LAN2 and ETH-3 is WAN. */
			if      ($ifname=="ETH-1") $mac = PHYINF_getdevdatamac("lanmac");
			else if ($ifname=="ETH-2") $mac = PHYINF_getdevdatamac("lanmac2");
			else if ($ifname=="ETH-3") $mac = PHYINF_getdevdatamac("wanmac");
		}
	}
	if ($mac=="") $mac = "00:de:fa:00:02:00"; /* Always return somethying. */
	return tolower($mac);
}

/* Get the configured MAC address of the logical interface */
function PHYINF_getmacsetting($ifname)
{
	/* Get the MAC address from the physical interface setting. */
	$path = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($path!="")
	{
		$phyinf = query($path."/phyinf");
		if ($phyinf!="")
		{
			$path = XNODE_getpathbytarget("", "phyinf", "uid", $phyinf, 0);
			if ($path!="") $mac = query($path."/macaddr");
		}
	}

	/* If there is no setting for MAC address, use factory setting.*/
	if ($mac=="")
	{
		if		($ifname=="BRIDGE-1")	$mac = PHYINF_getdevdatamac("wanmac");
		else if	($ifname=="BRIDGE-2")	$mac = PHYINF_getdevdatamac("wanmac2");
		else if	($ifname=="LAN-1")		$mac = PHYINF_getdevdatamac("lanmac");
		else if	($ifname=="LAN-2")		$mac = PHYINF_getdevdatamac("lanmac2");
		else if	($ifname=="WAN-1")		$mac = PHYINF_getdevdatamac("wanmac");
		else if	($ifname=="WAN-2")		$mac = PHYINF_getdevdatamac("wanmac2");
	}
	if ($mac=="") $mac = "00:de:fa:00:03:00"; /* Always return somethying. */
	return tolower($mac);
}
?>
