#!/bin/sh
<? /* $IFNAME, $DEVICE, $SPEED, $IP, $REMOTE, $PARAM */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

$infp = XNODE_getpathbytarget("", "inf", "uid", $PARAM, 0);
if ($infp == "") exit;
$inet = query($infp."/inet");
if ($inet == "") exit;

$defaultroute = query($infp."/defaultroute");
/* create phyinf */
PHYINF_setup("PPP.".$PARAM, "ppp", $IFNAME);

/* check if having child */
$child = query($infp."/child");

/* get mtu value*/
$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
$mtu = query($inetp."/ppp6/mtu");

/* create inf */
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $PARAM, 1);
//del($stsp."/inet");//stop will do it..

if($child!="")
{
	set($stsp."/child/uid",			$child);
	set($stsp."/child/ipaddr",		$IP);
	set($stsp."/child/prefix",		"10");//fe80::/10
	set($stsp."/child/ppp6/peer",		$REMOTE);//to keep remote info
	set($stsp."/inet/ppp6/valid",		"1");
	echo "echo set already!! > /dev/console\n";
}
else
{
	$phyinf = "PPP.".$PARAM;
	$devnam = PHYINF_getifname($phyinf);
	$over_intf = get("",$inetp."/ppp6/over");
	set($stsp."/inet/uid",			$inet);
	set($stsp."/inet/addrtype",		"ppp6");
	set($stsp."/inet/uptime",		query("/runtime/device/uptime"));
	set($stsp."/inet/ppp6/valid",		"1");
	set($stsp."/inet/ppp6/mtu",           	$mtu);
	set($stsp."/inet/ppp6/local",		$IP);
	set($stsp."/inet/ppp6/peer",		$REMOTE);
/* +++ HuanYao Kang: add a over node to identify that wan type. */
	set($stsp."/inet/ppp6/over",		$over_intf);
	set($stsp."/phyinf",			$phyinf);
	set($stsp."/devnam",			$devnam);
	set($stsp."/defaultroute",		$defaultroute);
}

/* Add this network in 'LOCAL' */
/*echo 'ip route add '.$REMOTE.' dev '.$IFNAME.' src '.$IP.' table LOCAL\n';

if ($defaultroute!="")
{
	if($defaultroute == 0)
	{
		echo 'ip route add '.$REMOTE.' dev '.$IFNAME.' src '.$IP.' table '.$PARAM.'\n';
	}
	else if($defaultroute == 1)
	{
		echo '#pppd will insert defaultroute so we do not add defaultroute.\n';
	}
	else if($defaultroute > 1)
	{
		echo 'ip route add default via '.$REMOTE.' metric '.$defaultroute.' table default\n';
	}
}
*/

echo 'ip -6 route add default via '.$REMOTE.' dev '.$IFNAME.'\n';

/* record routing info to runtime node */
$base = "/runtime/dynamic/route6";
$cnt = query($base."/entry#");
if($cnt=="") $cnt=0;
$cnt++;
set($base."/entry:".$cnt."/ipaddr", "::");
set($base."/entry:".$cnt."/prefix", "0");
set($base."/entry:".$cnt."/gateway", $REMOTE);
set($base."/entry:".$cnt."/metric", "1024");
set($base."/entry:".$cnt."/inf", "PPP");

/* user dns */
$cnt = 0;
if ($inetp != "")
{
	$cnt = query($inetp."/ppp6/dns/count");
	if ($cnt=="") $cnt = 0;
	$i = 0;
	while ($i < $cnt)
	{
		$i++;
		$value = query($inetp."/ppp6/dns/entry:".$i);
		if ($value != "") add($stsp."/inet/ppp6/dns", $value);
	}
}


/* auto dns */
/*
if ($cnt == 0 && isfile("/etc/ppp/resolv.conf.".$PARAM)==1)
{
	$dnstext = fread("r", "/etc/ppp/resolv.conf.".$PARAM);
	$cnt = scut_count($dnstext, "");
	$i = 0;
	while ($i < $cnt)
	{
		$token = scut($dnstext, $i, "");
		if ($token == "nameserver")
		{
			$i++;
			$token = scut($dnstext, $i, "");
			add($stsp."/inet/ppp6/dns", $token);
		}
		$i++;
	}
}
*/

/* We use PING peer IP to trigger the dailup at 'ondemand' mode.
 * So we need to update the command to PING the new gateway. */
$dial = XNODE_get_var($PARAM.".DIALUP");
if ($dial=="") $dial = query($inetp."/ppp6/dialup/mode");
if ($dial=="ondemand")
	echo 'event '.$PARAM.'.PPP.DIALUP add "ping '.$REMOTE.'"\n';

//Check if the other up script is finished
if($child!="")
{
	$rinetp = query($stsp."/inet");
	$v4local = query($stsp."/inet/ppp4/local");
	//if($child!=""&&$rinetp!="")
	if($child!=""&&$v4local!="")
	{
		echo "echo IPCP is ready!! > /dev/console\n";

		/* user dns */
		$cnt = 0;
		if ($inetp != "")
		{
			$cnt = query($inetp."/ppp4/dns/count");
			if ($cnt=="") $cnt = 0;
			$i = 0;
			while ($i < $cnt)
			{
				$i++;
				$value = query($inetp."/ppp4/dns/entry:".$i);
				if ($value != "") add($stsp."/inet/ppp4/dns", $value);
			}
		}

		$childip = query($stsp."/child/ipaddr");
		$childpfx = query($stsp."/child/prefix");
		XNODE_set_var($child."_ADDRTYPE", "ipv6");
		XNODE_set_var($child."_IPADDR", $childip);
		XNODE_set_var($child."_PREFIX", $childpfx);
		XNODE_set_var($child."_PHYINF", "PPP.".$PARAM);
		echo "event ".$PARAM.".UP\n";
		echo "echo 1 > /var/run/".$PARAM.".UP\n";
	}
	else  echo "echo IPCP is not ready!! WAIT --- > /dev/console\n";
}
else
{
	echo "event ".$PARAM.".UP\n";
	echo "echo 1 > /var/run/".$PARAM.".UP\n";
}
?>
exit 0
