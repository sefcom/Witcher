<?
include "/htdocs/phplib/xnode.php";

echo '#!/bin/sh\n';

/* return 1 if ipaddr is a private IPv4 address else return 0. */
function privatecheck($ipaddr)
{
	$private=0;
	if (isdomain($ipaddr)!="0")
	{
		$a = cut($ipaddr, 0, ".");
		$b = cut($ipaddr, 1, ".");
		$c = cut($ipaddr, 2, ".");
		$d = cut($ipaddr, 3, ".");
		if ($a==10)
		{
			$private=1;
		}
		else if ($a==172)
		{
			if ($b>=16 && $b<=31)
				$private=1;
		}
		else if ($a==192 && $b==168)
		{
			$private=1;
		}
	}
	return $private;
}

$ip = query("/runtime/services/wandetect/dhcp/".$INF."/ip");
if ($ip!="")
{
	$private = privatecheck($ip);
	if ($private==1)
	{
		$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
		$phyinf = query($infp."/phyinf");
		$inet = query($infp."/inet");
		$inetp = XNODE_getpathbytarget("inet", "entry", "uid", $inet, 0);
		$addrtype = query($inetp."/addrtype");
		$static = query($inetp."/ipv4/static");

		//if ($addrtype!="ipv4" || $static!="0")
		//{
			set("/runtime/services/wandetect/originet/addrtype", $addrtype);
			set("/runtime/services/wandetect/originet/ipv4/static", $static);
			set($inetp."/addrtype", "ipv4");
			set($inetp."/ipv4/static", "0");
		//}

		/* Restart WAN-1 as DHCP client */
		echo 'event INFSVCS.'.$INF.'.UP add "/etc/events/WAN_dhcp_pri.sh '.$INF.'"\n';
		echo 'service WAN restart\n';
	}
	else
	{
		set("/runtime/services/wandetect/wantype", "DHCP");
		set("/runtime/services/wandetect/desc", "Global IP");
	}
}
else
{
	echo 'event PPP.DISCOVER\n';
}
echo 'exit 0\n';
?>
