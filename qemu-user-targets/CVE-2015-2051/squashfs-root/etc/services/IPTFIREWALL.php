<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/etc/services/IPTABLES/iptlib.php";

function fw_setting($firewallp, $chain)
{
	$def_policy = query($firewallp."/policy");
	$rules = 0;

	if ($def_policy != "DISABLE")
	{
		$cnt = query($firewallp."/count");
		if ($cnt=="") $cnt = 0;
		foreach ($firewallp."/entry")
		{
			if ($InDeX > $cnt) break;
			/* active ? */
			if (query("enable")!="1") continue;

			/* Reset the iptable command */
			$IPT = "";
			/* time */
			$sch = query("schedule");

			/* src interface */
			$srcinf = query("src/phyinf");	//check for phyinf first !
			if ($srcinf!="")
			{
				$phyinf = PHYINF_getifname($srcinf);
				if($phyinf=="") continue;
				//check if our kernel support BRIDGE_NETFILTER or not
				if(isfile("/proc/sys/net/bridge/bridge-nf-call-iptables")!=1)
					TRACE_error("Your kernel doesn't support -m physdev command. Please open CONFIG_BRIDGE_NETFILTER !!!!");

				$IPT=$IPT." -m physdev --physdev-in ".$phyinf;
			}else
			{
				$srcinf = query("src/inf");
				if ($srcinf!="")
				{
					$phyinf = PHYINF_getruntimeifname($srcinf);
					if ($phyinf == "") continue;
					$IPT=$IPT." -i ".$phyinf;
				}
			}

			/*
				Note : -m physdev --physdev-out isn't supported in OUTPUT/FORWARD/POSTROUTING/
			*/

			/* dst interface */
			$dstinf = query("dst/inf");
			if ($dstinf!="")
			{
				$phyinf = PHYINF_getruntimeifname($dstinf);
				if ($phyinf == "") continue;
				$IPT=$IPT." -o ".$phyinf;
			}

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
			$policy = query("policy");
			/* protocol ALL/TCP/UDP/TCP+UDP/ICMP */
			$prot = query("protocol");
			if ($prot=="TCP" || $prot=="UDP" || $prot=="TCP+UDP")
			{
				$dportstart	= query("dst/port/start");
				$dportend	= query("dst/port/end");

				/* port */
				if ($dportstart!="" && $dportend!="" &&
					$dportstart!=$dportend)	$IPT=$IPT." -m mport --dports ".$dportstart.":".$dportend;
				else if ($dportstart!="")	$IPT=$IPT." -m mport --dports ".$dportstart;
			}

			if ($policy == "DROP")
			{
				if($prot=="TCP+UDP")
				{
					if($sch=="")
					{
						fwrite('a',$_GLOBALS["START"],
							'iptables -A '.$chain.' -p tcp '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006:\n');
						fwrite('a',$_GLOBALS["START"],
						'iptables -A '.$chain.' -p udp '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006:\n');
					}
					else
					{
						$IPT_sch = 'iptables -A '.$chain.' -p tcp '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006';
						IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
						$IPT_sch = 'iptables -A '.$chain.' -p udp '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006';
						IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
					}
				}
				else
				{
					if($sch=="")
					{
						fwrite('a',$_GLOBALS["START"],
							'iptables -A '.$chain.' -p '.$prot.' '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006:\n');
					}
					else
					{
						$IPT_sch = 'iptables -A '.$chain.' -p '.$prot.' '.$IPT.' -j LOG --log-level notice --log-prefix DRP:006';
						IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
					}
				}
			}
			if ($prot=="TCP+UDP")
			{
				if($sch=="")
				{
					fwrite("a",$_GLOBALS["START"],
						'iptables -A '.$chain.' -p tcp '.$IPT.' -j '.$policy.'\n');
					fwrite("a",$_GLOBALS["START"],
						'iptables -A '.$chain.' -p udp '.$IPT.' -j '.$policy.'\n');
				}
				else
				{
					$IPT_sch = 'iptables -A '.$chain.' -p tcp '.$IPT.' -j '.$policy;
					IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
					$IPT_sch = 'iptables -A '.$chain.' -p udp '.$IPT.' -j '.$policy;
					IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
				}
			}
			else
			{
				if($sch=="")
				{
					fwrite("a",$_GLOBALS["START"],
						'iptables -A '.$chain.' -p '.$prot.' '.$IPT.' -j '.$policy.'\n');
				}
				else
				{
					$IPT_sch = 'iptables -A '.$chain.' -p '.$prot.' '.$IPT.' -j '.$policy;
					IPT_fwrite_schedule("a", $_GLOBALS["START"], $IPT_sch, $sch);
				}
			}

			$rules++;
		}

		if ($def_policy == "DROP")
		{
			fwrite("a",$_GLOBALS["START"],
				'iptables -A '.$chain.' -m state --state ESTABLISHED,RELATED -j ACCEPT\n'.
				'iptables -A '.$chain.' -j LOG --log-level notice --log-prefix DRP:006:\n'.
				'iptables -A '.$chain.' -j DROP\n'
				);
		}

		if($rules != 0)
		{
			XNODE_set_var($chain.".USED", $rules);
		}
	}
}

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

$CHAIN = 'FIREWALL';
XNODE_set_var($CHAIN.".USED", "0");
fwrite("a",$START, "iptables -F ".$CHAIN."\n");
fwrite("a",$STOP,  "iptables -F ".$CHAIN."\n");
fw_setting("/acl/firewall", $CHAIN);

$deny_qq = query("/acl/applications/qq/action");
$deny_msn = query("/acl/applications/msn/action");
if($deny_msn=="DENY")	/* Deny MSN*/
{
		fwrite("a",$_GLOBALS["START"],
			'iptables -A '.$CHAIN.' -d messenger.hotmail.com -j LOG --log-level notice --log-prefix DRP:006:\n'.
			'iptables -A '.$CHAIN.' -d messenger.hotmail.com -j DROP\n'.
			'iptables -A '.$CHAIN.' -d login.live.com -j LOG --log-level notice --log-prefix DRP:006:\n'.
			'iptables -A '.$CHAIN.' -d login.live.com -j DROP\n'.
			'iptables -A '.$CHAIN.' -p TCP -m mport --dport 1863 -j LOG --log-leve notice --log-prefix DRP:006:\n'.
			'iptables -A '.$CHAIN.' -p TCP -m mport --dport 1863 -j DROP\n'.
			);
		XNODE_set_var($CHAIN.".USED", "1");
}
if($deny_qq=="DENY")	/* Deny QQ*/
{
		fwrite("a",$_GLOBALS["START"],
			'iptables -A '.$CHAIN.' -p UDP -m mport --dport 8000 -j LOG --log-leve notice --log-prefix DRP:006:\n'.
			'iptables -A '.$CHAIN.' -p UDP -m mport --dport 8000 -j DROP\n'.
			);
		XNODE_set_var($CHAIN.".USED", "1");
}

$CHAIN2 = 'FIREWALL-2';
XNODE_set_var($CHAIN2.".USED", "0");
fwrite("a",$START, "iptables -F ".$CHAIN2."\n");
fwrite("a",$STOP,  "iptables -F ".$CHAIN2."\n");
fw_setting("/acl/firewall2", $CHAIN2);

$CHAIN3 = 'FIREWALL-3';
XNODE_set_var($CHAIN3.".USED", "0");
fwrite("a",$START, "iptables -F ".$CHAIN3."\n");
fwrite("a",$STOP,  "iptables -F ".$CHAIN3."\n");
fw_setting("/acl/firewall3", $CHAIN3);

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
