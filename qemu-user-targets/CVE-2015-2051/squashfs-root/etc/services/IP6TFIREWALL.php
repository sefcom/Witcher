<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/etc/services/IPTABLES/iptlib.php";
include "/etc/services/IP6TABLES/ip6tlib.php";

$CHAIN = 'FIREWALL';
$CHAIN_POLICY = 'FIREWALL_POLICY';
XNODE_set_var($CHAIN."6.USED", "0");

fwrite("w",$START, "#!/bin/sh\n");
fwrite("a",$START, "ip6tables -F ".$CHAIN."\n");
fwrite("a",$START, "ip6tables -F ".$CHAIN_POLICY."\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
fwrite("a",$STOP,  "ip6tables -F ".$CHAIN."\n");
fwrite("a",$STOP,  "ip6tables -F ".$CHAIN_POLICY."\n");

$def_policy = query("/acl6/firewall/policy");
$rules = 0;
if ($def_policy != "DISABLE")
{
	$cnt = query("/acl6/firewall/count");
	if ($cnt=="") $cnt = 0;
	foreach ("/acl6/firewall/entry")
	{
		if ($InDeX > $cnt) break;
		/* active ? */
		if (query("enable")!="1") continue;

		/* Reset the iptable command */
		$IPT = "";
		/* time */
		$sch = query("schedule");

		/* src interface */
		$srcinf = query("src/inf");
		if ($srcinf!="")
		{
			//+++ Jerry Kao, Searching the pppoe($type_ppp) first, if src/inf is WAN.
			$wan_ppp  = "";
			$srcinf_prefix = cut($srcinf, 0, "-");

			if ($srcinf_prefix == "WAN")
			{
				foreach ("/inf")
				{
					$uid_wan = query("uid");

					$wan_prefix = cut($uid_wan, 0,"-");
					if ($wan_prefix == "WAN")
					{
						$type_ppp = "";
						$infp  = XNODE_getpathbytarget("", "inf", "uid", $uid_wan, 0);
						$inet  = query($infp."/inet");
						if ($inet != "")
						{
							$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
							$type_ppp = query($inetp."/addrtype");
							if ($type_ppp == "ppp6" || $type_ppp == "ppp10")
							{
								$wan_ppp = $uid_wan;
							}
						}
					}
				}
			}
			//TRACE_debug("== s: type_ppp= ".$type_ppp);

			if ($wan_ppp != "")
			{
				$phyinf = PHYINF_getruntimeifname($wan_ppp);
			}
			else
			{
				$phyinf = PHYINF_getruntimeifname($srcinf);
			}
			if ($phyinf == "") continue;
			$IPT=$IPT." -i ".$phyinf;
		}

		/* dst interface */
		$dstinf = query("dst/inf");
		if ($dstinf!="")
		{
			//+++ Jerry Kao, Searching the pppoe($type_ppp) first, if dst/inf is WAN.
			$wan_ppp  = "";
			$dstinf_prefix = cut($dstinf, 0, "-");

			if ($dstinf_prefix == "WAN")
			{
				foreach ("/inf")
				{
					$uid_wan = query("uid");

					$wan_prefix = cut($uid_wan, 0,"-");
					if ($wan_prefix == "WAN")
					{
						$type_ppp = "";
						$infp  = XNODE_getpathbytarget("", "inf", "uid", $uid_wan, 0);
						$inet  = query($infp."/inet");
						if ($inet != "")
						{
							$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
							$type_ppp = query($inetp."/addrtype");
							if ($type_ppp == "ppp6" || $type_ppp == "ppp10")
							{
								$wan_ppp = $uid_wan;
							}
						}
					}
				}
			}

			if ($wan_ppp != "")
			{
				$phyinf = PHYINF_getruntimeifname($wan_ppp);
			}
			else
			{
				$phyinf = PHYINF_getruntimeifname($dstinf);
			}

			if ($phyinf == "") continue;
			$IPT=$IPT." -o ".$phyinf;
		}
		//TRACE_debug("== IPT_io= ".$IPT);

		/* check the IP range. */
		$sipstart	= query("src/host/start");
		$sipend		= query("src/host/end");
		$dipstart	= query("dst/host/start");
		$dipend		= query("dst/host/end");
		if ($sipstart != "" && $dipstart != "")
		{
			/* We have both source and destination IP address restriction */
			if ($sipend!="" &&
				$dipend!="")		$IPT=$IPT." -m iprange --src-range ".$sipstart."-".$sipend.
												" --dst-range ".$dipstart."-".$dipend;
			else if ($sipend!="")	$IPT=$IPT." -d ".$dipstart." -m iprange --src-range ".$sipstart."-".$sipend;
			else if ($dipend!="")	$IPT=$IPT." -s ".$sipstart." -m iprange --dst-range ".$dipstart."-".$dipend;
			else					$IPT=$IPT." -s ".$sipstart." -d ".$dipstart;
		}
		else if ($sipstart != "")
		{
			/* We have only source IP address restriction */
			if ($sipend != "")	$IPT=$IPT." -m iprange --src-range ".$sipstart."-".$sipend;
			else				$IPT=$IPT." -s ".$sipstart;
		}
		else if ($dipstart != "")
		{
			/* We have only destination IP address restriction */
			if ($dipend != "")	$IPT=$IPT." -m iprange --dst-range ".$dipstart."-".$dipend;
			else				$IPT=$IPT." -d ".$dipstart;
		}



		/* policy ? ACCEPT/DROP */
		//$policy = query("policy");
		if($def_policy == "ACCEPT"){$policy	= "DROP";}
		else {$policy = "ACCEPT";}

		/* protocol ALL/TCP/UDP/ICMP */
		$prot = query("protocol");
		if ($prot=="TCP" || $prot=="UDP")
		{
			$dportstart	= query("dst/port/start");
			$dportend	= query("dst/port/end");

			/* port */
			if ($dportstart!="" && $dportend!="" &&
				$dportstart!=$dportend)	$IPT=$IPT." -m mport --dports ".$dportstart.":".$dportend;
			else if ($dportstart!="")	$IPT=$IPT." -m mport --dports ".$dportstart;
		}

		if($sch == "")
		{
			if ($policy == "DROP") fwrite('a',$_GLOBALS["START"],
				'ip6tables -A '.$CHAIN.' -p '.$prot.' '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006:\n');
			fwrite("a",$_GLOBALS["START"],
				'ip6tables -A '.$CHAIN.' -p '.$prot.' '.$IPT.' -j '.$policy.'\n');
		}
		else
		{
			if ($policy == "DROP")
			{
				$IPT_sch = 'ip6tables -A '.$CHAIN.' -p '.$prot.' '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006';
				IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
			}
			$IPT_sch = 'ip6tables -A '.$CHAIN.' -p '.$prot.' '.$IPT.' -j '.$policy;
			IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
		}

		$rules++;
	}

	if ($def_policy == "DROP")
	{
		fwrite("a",$_GLOBALS["START"],
			'ip6tables -A '.$CHAIN.' -m state --state ESTABLISHED,RELATED -j ACCEPT\n'.
			//'ip6tables -A '.$CHAIN.' -j LOG --log-level notice --log-prefix DRP:006:\n'.
			//'ip6tables -A '.$CHAIN.' -j DROP\n'
			'ip6tables -F '.$CHAIN_POLICY.'\n'.
			'ip6tables -A '.$CHAIN_POLICY.' -j LOG --log-level notice --log-prefix DRP:006:\n'.
			'ip6tables -A '.$CHAIN_POLICY.' -j DROP\n'
			);
	}
}

if($def_policy != "DISABLE" )
{
	//+++ Jerry Kao, modified for the case that user configures no rules in "Turn IPv6 Filtering ON and ALLOW rules listed",
	//               router should DROP all packets between WAN and LAN.
	XNODE_set_var($CHAIN."6.USED", "1");

	//XNODE_set_var($CHAIN."6.USED", $rules);
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
