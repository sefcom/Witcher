<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/inet.php";
include "/etc/services/IPTABLES/iptlib.php";

function startcmd($cmd)    {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)     {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}

function setup_conntrack_max()
{
	/* When enable QoS, may shall downgrade conntrack support number */
	$conntrack_min = query("/runtime/device/conntrack_min");
	$conntrack_max = query("/runtime/device/conntrack_max");
	if ($conntrack_min != "" && $conntrack_min > 0 )
	{
		if (isfile("/proc/sys/net/netfilter/nf_conntrack_max")==1)
		{
			startcmd("echo ".$conntrack_min." > /proc/sys/net/netfilter/nf_conntrack_max");
		}	
		else if (isfile("/proc/sys/net/netfilter/ip_conntrack_max")==1)
		{
			startcmd("echo ".$conntrack_min." > /proc/sys/net/netfilter/ip_conntrack_max");
		}			
	}
	/* When stop QoS, shall recover max conntrack support number */
	if ($conntrack_max != "" && $conntrack_max > 0 )
	{
		if (isfile("/proc/sys/net/netfilter/nf_conntrack_max")==1)
		{
			stopcmd("echo ".$conntrack_max." > /proc/sys/net/netfilter/nf_conntrack_max");
		}	
		else if (isfile("/proc/sys/net/netfilter/ip_conntrack_max")==1)
		{
			stopcmd("echo ".$conntrack_max." > /proc/sys/net/netfilter/ip_conntrack_max");
		}			
	}
}

function bwc_error($errno)
{
	startcmd("exit ".$errno."\n");
	stopcmd( "exit ".$errno."\n");
}

function copy_bwc_entry($from, $to)
{
	del($to."/bwc");
	set($to."/bwc/uid",				query($from."/uid"));
	set($to."/bwc/autobandwidth",	query($from."/autobandwidth"));
	set($to."/bwc/bandwidth",		query($from."/bandwidth"));

}

/* service_pre_trigger() and service_post_trigger() both are
   used to trigger other services which needed to start/restart/stop
   when bwc status change(start/restart/stop) */
function service_pre_trigger()
{
	/* remove/insert software nat/TurboNAT */
	startcmd("rmmod sw_tcpip");
	stopcmd("insmod /lib/modules/sw_tcpip.ko");
}
function service_post_trigger()
{
	/* restart IPTDEFCHAIN */
	startcmd("service IPTDEFCHAIN restart");
	stopcmd("service IPTDEFCHAIN restart");
}

function porttype_handle($entryp, $ipt_add_cmd, $ipt_del_cmd, $portrange, $mark_cmd) 
{
	$i = 0;
	while ( $i < 2 )
	{
		$tmp_ipt_add_cmd = "";
		$tmp_ipt_del_cmd = "";
		if ($portrange=="-1") {
			if ( $i == 0 ) { // for tcp
				$tmp_ipt_add_cmd = $ipt_add_cmd." -p tcp -m mport --ports 0:65535";
				$tmp_ipt_del_cmd = $ipt_del_cmd." -p tcp -m mport --ports 0:65535";
			}else { // for udp
				$tmp_ipt_add_cmd = $ipt_add_cmd." -p udp -m mport --ports 0:65535";
				$tmp_ipt_del_cmd = $ipt_del_cmd." -p udp -m mport --ports 0:65535";
			}
		} else if ($portrange=="0") {
			if ( $i == 0 ) { // for tcp
				$tmp_ipt_add_cmd = $ipt_add_cmd." -p tcp --dport ".query($entryp."/port/start");
				$tmp_ipt_del_cmd = $ipt_del_cmd." -p tcp --dport ".query($entryp."/port/start");
			}else { // for udp
				$tmp_ipt_add_cmd = $ipt_add_cmd." -p udp --dport ".query($entryp."/port/start");
				$tmp_ipt_del_cmd = $ipt_del_cmd." -p udp --dport ".query($entryp."/port/start");
			}
		} else {
			if ( $i == 0 ) { // for tcp
				$tmp_ipt_add_cmd = $ipt_add_cmd." -p tcp -m mport --ports ".query($entryp."/port/start").":".query($entryp."/port/end");
				$tmp_ipt_del_cmd = $ipt_del_cmd." -p tcp -m mport --ports ".query($entryp."/port/start").":".query($entryp."/port/end");
			}else { // for udp
				$tmp_ipt_add_cmd = $ipt_add_cmd." -p udp -m mport --ports ".query($entryp."/port/start").":".query($entryp."/port/end");
				$tmp_ipt_del_cmd = $ipt_del_cmd." -p udp -m mport --ports ".query($entryp."/port/start").":".query($entryp."/port/end");
			}
		}
		startcmd($tmp_ipt_add_cmd.$mark_cmd);
		$i++;
	} // while() --- END
}

function bwc_bc_start($rtbwcp, $name, $ifname)
{
	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname." parent 50: protocol all prio 1";
	$tc_filter_del	= "tc filter del dev ".$ifname." parent 50: protocol all prio 1";
	$ipt_flush_cmd	= "iptables -t mangle -F PRE.BWC.".$name;
	$ipt_add_prefix	= "iptables -t mangle -A PRE.BWC.".$name;
	$ipt_del_prefix	= "iptables -t mangle -D PRE.BWC.".$name;

	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	$rate1 = $trate/4; $rate2 = $trate/4;
	$rate3 = $trate/4; $rate4 = $trate/4;
	$rate1_celi = $trate; $rate2_celi = $trate;
	$rate3_celi = $trate; $rate4_celi = $trate;

	$trate = $trate.$unit;
	$rate1 = $rate1.$unit; $rate2 = $rate2.$unit;
	$rate3 = $rate3.$unit; $rate4 = $rate4.$unit;
	$rate1_celi = $rate1_celi.$unit; $rate2_celi = $rate2_celi.$unit;
	$rate3_celi = $rate3_celi.$unit; $rate4_celi = $rate4_celi.$unit;

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc */
	startcmd($tc_qd_add." root handle 50:0 htb default 20");

	/* add root class */
	startcmd($tc_class_add." parent 50:0 classid 50:1 htb rate ".$trate);

	/* add "50:10" class, and prio:1 */
	startcmd($tc_class_add." parent 50:1 classid 50:10 htb rate ".$rate1." ceil ".$rate1_celi." prio 1");

	/* add "50:20" class, and prio:2 */
	startcmd($tc_class_add." parent 50:1 classid 50:20 htb rate ".$rate2." ceil ".$rate2_celi." prio 2");

	/* add "50:30" class, and prio:3 */
	startcmd($tc_class_add." parent 50:1 classid 50:30 htb rate ".$rate3." ceil ".$rate3_celi." prio 3");

	/* add "50:40" class, and prio:4 */
	startcmd($tc_class_add." parent 50:1 classid 50:40 htb rate ".$rate4." ceil ".$rate4_celi." prio 4");

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 50:10 handle 5010: sfq perturb 10");
	startcmd($tc_qd_add." parent 50:20 handle 5020: sfq perturb 10");
	startcmd($tc_qd_add." parent 50:30 handle 5030: sfq perturb 10");
	startcmd($tc_qd_add." parent 50:40 handle 5040: sfq perturb 10");

	/* add filter, use fw when upstream */
	startcmd($tc_filter_add." handle 5010 fw classid 50:10");
	startcmd($tc_filter_add." handle 5020 fw classid 50:20");
	startcmd($tc_filter_add." handle 5030 fw classid 50:30");
	startcmd($tc_filter_add." handle 5040 fw classid 50:40");

	/* set mark, use iptables/mangle */
	startcmd($ipt_flush_cmd);
	foreach($rtbwcp."/rules/entry")
	{
		if (query("enable")=="1")
		{
			$bwcqd_name = query("bwcqd");
			$bwcqdp = XNODE_getpathbytarget("/bwcqd", "entry", "uid", $bwcqd_name, 0);
			if( $bwcqdp == "" ) { continue; }

			$entryp = $rtbwcp."/rules/entry:".$InDeX; 
			$ipt_add_cmd	= $ipt_add_prefix;
			$ipt_del_cmd	= $ipt_del_prefix;
			$startip = query("/ipv4/start");
			$endip = query("/ipv4/end");
			$int_start = ipv4hostid($startip, 0);
			$int_end = ipv4hostid($endip, 0);
			if($int_start > $int_end) { $iprange = $int_start - $int_end; }
			else { $iprange = $int_end - $int_start; }
			$portrange	 	= query("/port/range");
			$startport = query("/port/start");
			$endport = query("/port/end");
			if($startport > $endport) { $portrange = $startport - $endport; }
			else { $portrange = $endport - $startport; }
			$mark_cmd		= " -j MARK --set-mark 50".query($bwcqdp."/priority")."0";

			/* check iptype */
			if($iprange != "") {

				if ($iprange =="-1") {
					$mask = INF_getcurrmask(query("bwc_rule_inf"));
					$ipt_add_cmd = $ipt_add_cmd." -s ".query($entryp."/ipv4/start")."/".$mask;
					$ipt_del_cmd = $ipt_del_cmd." -s ".query($entryp."/ipv4/start")."/".$mask;

				} else if ($iprange =="0") {
				    $ipt_add_cmd = $ipt_add_cmd." -s ".query($entryp."/ipv4/start");
					$ipt_del_cmd = $ipt_del_cmd." -s ".query($entryp."/ipv4/start");

				} else {
					$ipt_add_cmd = $ipt_add_cmd." -m iprange --src-range ".query($entryp."/ipv4/start")."-".query($entryp."/ipv4/end");
					$ipt_del_cmd = $ipt_del_cmd." -m iprange --src-range ".query($entryp."/ipv4/start")."-".query($entryp."/ipv4/end");
				}

				/* check port type */
				if ($portrange != "") {
					porttype_handle($entryp, $ipt_add_cmd, $ipt_del_cmd, $porttype, $mark_cmd);
				} else {
					startcmd($ipt_add_cmd.$mark_cmd);
				}
			} else {

				/* check port type */
				if ($porttype != "") {
					porttype_handle($entryp, $ipt_add_cmd, $ipt_del_cmd, $porttype, $mark_cmd);
				} else {
					// ip and port both are empty, nothing need to do.
				}
			}
		}
	}
}

function bwc_bc_stop($rtbwcp, $name, $ifname)
{
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$ipt_flush_cmd	= "iptables -t mangle -F PRE.BWC.".$name;

	/* clean all qdisc*/
	stopcmd($tc_qd_del." root 2>/dev/null");

	/* cleann all iptables/mangle/subchain rules */
	stopcmd($ipt_flush_cmd);
}

function bwc_tc_start($rtbwcp, $name, $ifname)
{
	$LANSTR="LAN-1";
//	$LANDEV = PHYINF_getruntimeifname($LANSTR);

	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$tc_filter_del	= "tc filter del dev ".$ifname;
	$ipt_add_prefix	= "iptables -t mangle -A PRE.BWC.".$LANSTR;

	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	$trate = $trate.$unit;

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc */
	startcmd($tc_qd_add." root handle 2: htb default 40");

	/* add root class */
	startcmd($tc_class_add." parent 2:0 classid 2:1 htb rate ".$trate);
	startcmd($tc_class_add." parent 2:1 classid 2:40 htb rate 1".$unit." ceil ".$trate);

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 2:40 handle 400: sfq perturb 10");

	startcmd($tc_filter_add." parent 2: protocol all u32 match ip tos 0x00 0xe0 flowid 2:40");

	$classid_base=10;
	$mark_base=10;
	foreach($rtbwcp."/rules/entry")
	{
		if (query("enable")=="1")
		{
			$bwcf_name = query("bwcf");
			$bwcfp = XNODE_getpathbytarget("/bwc/bwcf", "entry", "uid", $bwcf_name, 0);
			if($bwcfp == "" ) { continue; }
			$bwcqd_name = query("bwcqd");
			$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd", "entry", "uid", $bwcqd_name, 0);
			if($bwcqdp == "" ) { continue; }

			$bandwidth = query($bwcqdp."/bandwidth");
			if($bandwidth == "" || $bandwidth == "0") { continue; }
			$rate = $bandwidth.$unit;
			
			$startip = query($bwcfp."/ipv4/start");
			$endip = query($bwcfp."/ipv4/end");
			$int_start = ipv4hostid($startip, 0);
			$int_end = ipv4hostid($endip, 0);
			
			if($int_start > $int_end)
			{
				$iprange = $int_start - $int_end + 1;
				$startip = query($bwcfp."/ipv4/end");
				$endip = query($bwcfp."/ipv4/start");
			}
			else
			{
				$iprange = $int_end - $int_start + 1;
			}

			if( $name == "WAN-1" )   /* Upload bandwidth control */
			{
				if(query($bwcqdp."/flag") == "MAXBD")
				{
					startcmd($tc_class_add." parent 2:1 classid 2:".$classid_base." htb rate 1".$unit." ceil ".$rate);
					startcmd($tc_qd_add." parent 2:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 2: protocol ip handle ".$mark_base." fw classid 2:".$classid_base);
					startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j MARK --set-mark ".$mark_base);
					startcmd("echo ".$startip."-".$endip." ".$mark_base." 0 > /proc/fastnat/fortcmarksupport");
				}
				else if(query($bwcqdp."/flag") == "RSVBD")
				{
					startcmd($tc_class_add." parent 2:1 classid 2:".$classid_base." htb rate ".$rate." ceil ".$trate);
					startcmd($tc_qd_add." parent 2:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 2: protocol ip handle ".$mark_base." fw classid 2:".$classid_base);
					startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j MARK --set-mark ".$mark_base);
					startcmd("echo ".$startip."-".$endip." ".$mark_base." 0 > /proc/fastnat/fortcmarksupport");
				}
				else
				{
				}
			}
			if( $name == "LAN-1" ) /* Download bandwidth control */
			{
				if(query($bwcqdp."/flag") == "MAXBD")
				{
					startcmd($tc_class_add." parent 2:1 classid 2:".$classid_base." htb rate 1".$unit." ceil ".$rate);
					startcmd($tc_qd_add." parent 2:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 2: protocol ip u32 match ip dst ".$startip."/".$iprange." flowid 2:".$classid_base);
				}
				else if(query($bwcqdp."/flag") == "RSVBD")
				{
					startcmd($tc_class_add." parent 2:1 classid 2:".$classid_base." htb rate ".$rate." ceil ".$trate);
					startcmd($tc_qd_add." parent 2:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 2: protocol ip u32 match ip dst ".$startip."/".$iprange." flowid 2:".$classid_base);
				}
				else
				{
					startcmd("echo Unknown Traffic Control Operation Mode...ERROR!!!");
				}
			}
		}
		$classid_base++;
		$mark_base++;
	}

}

function bwc_tc_stop($rtbwcp, $name, $ifname)
{
	$LANSTR="LAN-1";
	$LANDEV = PHYINF_getruntimeifname($LANSTR);

	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$ipt_flush_cmd	= "iptables -t mangle -F PRE.BWC.".$LANSTR;

	/* clean all qdisc*/
	stopcmd($tc_qd_del." root 2>/dev/null");

	/* clean fastnat */
	stopcmd("echo 0 > /proc/fastnat/qos");
	stopcmd("echo > /proc/fastnat/fortcmarksupport");

	/* cleann all iptables/mangle/subchain rules */
	stopcmd($ipt_flush_cmd);
}

function bwc_tc_connmark_start($rtbwcp, $name, $ifname)
{
	//#[$rtbwcp=/bwc:2/entry:2 $name=LAN-1 $ifname=br0]   
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	
	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$tc_filter_del	= "tc filter del dev ".$ifname;
	$ipt_add_prefix	= "iptables -t filter -A FWD.BWC.".$name;
	$ipt_out_add_prefix	= "iptables -t filter -A FWD.BWC.".$name;

	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	if($name=="LAN-1")
	{
		if($trate<200) $trate=200;
	}	
	$trate = $trate.$unit;

	/* TC fw policy will be:
		0: check skb->mark only. This is linux native default.
		1: check connection->mark only.
		2: Prefer connection->mark, if connection->mark==0, then check skb->mark later.
		3: Prefer skb->mark, if skb->mark==0, then check connection->mark later.
		4: check connection->mark only, and speed up TCP small packets.
	  */
	startcmd("echo 4 > /proc/sche/fw_policy");

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc */
	startcmd($tc_qd_add." root handle 66:0 prio bands 2 priomap 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0");

	/* add root class */
	startcmd($tc_qd_add." parent 66:1 handle 1:0 htb default 40"); //rate queue
	startcmd($tc_qd_add." parent 66:2 handle 2:0 sfq perturb 10"); //rateless queue, for packets to device
		
	startcmd($tc_class_add." parent 1:0 classid 1:1 htb rate ".$trate);
	startcmd($tc_class_add." parent 1:1 classid 1:40 htb rate 1".$unit." ceil ".$trate." quantum 1500");

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 1:40 handle 400: sfq perturb 10");

	/* set mark, use iptables/forward */
	startcmd("iptables -t filter -F FWD.BWC.".$name);
	
	/* we don't want to limit the rate for packets to router, set conntrack mark 0xFF00 */
	if($name=="LAN-1")
	{
		$path_run_inf_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0);
		$lanip = get("",$path_run_inf_lan1."/inet/ipv4/ipaddr");	
		startcmd($tc_filter_add." parent 66:0 protocol all prio 1 handle 0xFF00 fw classid 66:2");
		startcmd("iptables -t nat -I PRE.LAN-1 -d ".$lanip." -j CONNMARK --set-xmark 0xFF00/0xFF00");
		stopcmd("iptables -t nat -D PRE.LAN-1 -d ".$lanip." -j CONNMARK --set-xmark 0xFF00/0xFF00");
	}
	
	$bwc_main_chain_lan_rules = 0;

	/* At FORWARD chain, try to filter some packets that from another LAN interface. 
		It imply that packets from WAN. */
	$prefix = cut($name,0,'-');		
	if ($prefix == "LAN")
	{
		$mode = query("/device/router/mode"); 
		if ($mode!="1W1L")
		{
			foreach ("/inf")
			{
				$uid = query("uid");
				$active = query("active");
				$inf_prefix = cut($uid,0,'-');
				if ( $inf_prefix == "LAN" && $name != $uid && $active == "1" )
				{
					$infstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $uid, 0);
					$addrtype = query($infstsp."/inet/addrtype");
					if ($addrtype=="ipv4" || $addrtype=="ppp4")
					{
						$lan_ifname = PHYINF_getruntimeifname($uid);
						if ( $lan_ifname != "" && $lan_ifname != $ifname )
						{
							startcmd($ipt_out_add_prefix." -i ".$lan_ifname." -j RETURN");
							$bwc_main_chain_lan_rules++;
						}
					}
				}
			}
		}
	}

	$classid_base=10;
	
	/* available connmark mark form 0x100/0xFF00 to 0xFF00/0xFF00 restrict by kernel, sammy */
	$mark_base=strtoul("100",16);
	$hex_mark_base = dec2strf("0x%x",$mark_base);
	
	$rulecnt = query("/runtime/bwc/rules/count");
	foreach($rtbwcp."/rules/entry")
	{
		if (query("enable")=="1")
		{
			/* add chain for each rule */
			$rulecnt++;
			set("/runtime/bwc/rules/count", $rulecnt);
			$rchain = "BWC.TC".$rulecnt;
			startcmd("iptables -t filter -N ".$rchain);
			$ipt_add_rprefix = "iptables -t filter -A ".$rchain;
			
			$bwcf_name = query("bwcf");
			$bwcfp = XNODE_getpathbytarget("/bwc:2/bwcf", "entry", "uid", $bwcf_name, 0);
			if($bwcfp == "" ) { continue; }
			$bwcqd_name = query("bwcqd");
			$bwcqdp = XNODE_getpathbytarget("/bwc:2/bwcqd", "entry", "uid", $bwcqd_name, 0);
			if($bwcqdp == "" ) { continue; }

			$bandwidth = query($bwcqdp."/bandwidth");
			if($bandwidth == "" || $bandwidth == "0") { continue; }
			$rate = $bandwidth.$unit;
			
			$startip = query($bwcfp."/ipv4/start");
			$endip = query($bwcfp."/ipv4/end");
			$int_start = ipv4hostid($startip, 0);
			$int_end = ipv4hostid($endip, 0);
			
			if($int_start > $int_end)
			{
				$iprange = $int_start - $int_end + 1;
				$startip = query($bwcfp."/ipv4/end");
				$endip = query($bwcfp."/ipv4/start");
			}
			else
			{
				$iprange = $int_end - $int_start + 1;
			}
			
			/* time */
			$sch = query("schedule");
			if ($sch=="") $timecmd = "";
			else $timecmd = IPT_build_time_command($sch);
			
			if( $name == "WAN-1" )   /* Upload bandwidth control */
			{
				if(query($bwcqdp."/flag") == "MAXBD")
				{
					startcmd("#=========================================================================================");
					startcmd($tc_class_add." parent 1:1 classid 1:".$classid_base." htb rate 1".$unit." ceil ".$rate." quantum 1500");
					startcmd($tc_qd_add." parent 1:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 1: protocol all handle ".$hex_mark_base." fw classid 1:".$classid_base);
					startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j BWC.TC".$rulecnt);
					startcmd($ipt_add_rprefix." ".$timecmd." -j CONNMARK --set-xmark ".$hex_mark_base."/0xFF00");
					startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j RETURN");
				}
				else if(query($bwcqdp."/flag") == "RSVBD")
				{
					startcmd("#=========================================================================================");
					startcmd($tc_class_add." parent 1:1 classid 1:".$classid_base." htb rate ".$rate." ceil ".$trate." quantum 1500");
					startcmd($tc_qd_add." parent 1:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 1: protocol all handle ".$hex_mark_base." fw classid 1:".$classid_base);
					startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j BWC.TC".$rulecnt);
					startcmd($ipt_add_rprefix." ".$timecmd." -j CONNMARK --set-xmark ".$hex_mark_base."/0xFF00");
					startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j RETURN");
				}
				else
				{
					startcmd("echo Unknown Traffic Control Operation Mode...ERROR!!!");
				}
			}
			if( $name == "LAN-1" ) /* Download bandwidth control */
			{
				if(query($bwcqdp."/flag") == "MAXBD")
				{
					startcmd("#=========================================================================================");
					startcmd($tc_class_add." parent 1:1 classid 1:".$classid_base." htb rate 1".$unit." ceil ".$rate." quantum 1500");
					startcmd($tc_qd_add." parent 1:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 1: protocol all handle ".$hex_mark_base." fw classid 1:".$classid_base);
					startcmd($ipt_add_prefix." -m iprange --dst-range ".$startip."-".$endip." -j BWC.TC".$rulecnt);
					startcmd($ipt_add_rprefix." ".$timecmd." -j CONNMARK --set-xmark ".$hex_mark_base."/0xFF00");
					startcmd($ipt_add_prefix." -m iprange --dst-range ".$startip."-".$endip." -j RETURN");					
				}
				else if(query($bwcqdp."/flag") == "RSVBD")
				{
					startcmd("#=========================================================================================");
					startcmd($tc_class_add." parent 1:1 classid 1:".$classid_base." htb rate ".$rate." ceil ".$trate." quantum 1500");
					startcmd($tc_qd_add." parent 1:".$classid_base." handle ".$classid_base."0: sfq perturb 10");
					startcmd($tc_filter_add." parent 1: protocol all handle ".$hex_mark_base." fw classid 1:".$classid_base);
					startcmd($ipt_add_prefix." -m iprange --dst-range ".$startip."-".$endip." -j BWC.TC".$rulecnt);
					startcmd($ipt_add_rprefix." ".$timecmd." -j CONNMARK --set-xmark ".$hex_mark_base."/0xFF00");
					startcmd($ipt_add_prefix." -m iprange --dst-range ".$startip."-".$endip." -j RETURN");							
				}
				else
				{
					startcmd("echo Unknown Traffic Control Operation Mode...ERROR!!!");
				}
			}
		}
		$classid_base++;
		$mark_base+=strtoul("100",16); //add 0x100
		$hex_mark_base = dec2strf("0x%x",$mark_base);
	}
}

function bwc_tc_connmark_stop($rtbwcp, $name, $ifname)
{
	/* clean all qdisc*/
	stopcmd("tc qdisc del dev ".$ifname." root 2>/dev/null");

	/* cleann all iptables/forward/subchain rules */
	stopcmd("iptables -t filter -F FWD.BWC.".$name);
}

function aqc_tc_start($rtbwcp, $name, $ifname)
{
	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$tc_filter_del	= "tc filter del dev ".$ifname;
	$ipt_add_prefix	= "iptables -t mangle -A PST.BWC.".$name;
	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	if($trate == 0 || $trate == "")
	{
		$trate = 102400;	/* 100Mbps */
	}

	/* Priority VO: Voice, VI: Video, BG: Background, BE: Best-Effort */
	/* ceil */
	$prio0_MAX=$trate * 90 / 100;	/* VO: Voice */
	$prio1_MAX=$trate * 90 / 100;	/* VI: Video */
	$prio2_MAX=$trate * 80 / 100;	/* BG: Background */
	$prio3_MAX=$trate * 80 / 100;	/* BE: Best-Effort */
	/* rate */
	$prio0_MIN=$trate * 40 / 100;	/* VO: Voice */
	$prio1_MIN=$trate * 45 / 100;	/* VI: Video */
	$prio2_MIN=$trate * 10 / 100;	/* BG: Background */
	$prio3_MIN=$trate * 5 / 100;	/* BE: Best-Effort */

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* config tx queue length */
	startcmd("ip link set ".$ifname." txqueuelen 20 2>/dev/null");

	/* add root qdisc */
	startcmd($tc_qd_add." root handle 1: htb default 42");

	/* add root class */
	startcmd($tc_class_add." parent 1:0 classid 1:1 htb rate ".$trate.$unit);

	/* add leaf class */
	startcmd($tc_class_add." parent 1:1 classid 1:40 htb prio 0 rate ".$prio0_MIN.$unit." ceil ".$prio0_MAX.$unit." burst 0k cburst 0k");
	startcmd($tc_class_add." parent 1:1 classid 1:41 htb prio 1 rate ".$prio1_MIN.$unit." ceil ".$prio1_MAX.$unit." burst 0k cburst 0k");
	startcmd($tc_class_add." parent 1:1 classid 1:42 htb prio 2 rate ".$prio2_MIN.$unit." ceil ".$prio2_MAX.$unit." burst 0k cburst 0k");
	startcmd($tc_class_add." parent 1:1 classid 1:43 htb prio 3 rate ".$prio3_MIN.$unit." ceil ".$prio3_MAX.$unit." burst 0k cburst 0k");

	/* ADD CLASSIFICATION FILTER */
	/*startcmd($tc_filter_add." parent 1: protocol all prio 1 u32 match ip tos 0x00 0xE0 flowid 1:40");
	startcmd($tc_filter_add." parent 1: protocol all prio 1 u32 match ip tos 0x80 0xE0 flowid 1:41");
	startcmd($tc_filter_add." parent 1: protocol all prio 1 u32 match ip tos 0x40 0xE0 flowid 1:42");
	startcmd($tc_filter_add." parent 1: protocol all prio 1 u32 match ip tos 0x20 0xE0 flowid 1:43");*/

	/* auto qos classification */
	/* (Level 3,4) 340 : Voice, on-line games, 
	   (Level 5) 500 : Video, small packets,
	   (Level 6) 600 : Background, default,
	   (Level 7) 700 : Best-Effort, bad guys */

	startcmd($tc_filter_add." parent 1: protocol ip prio 10 handle 340 fw classid 1:40");
	startcmd($tc_filter_add." parent 1: protocol ip prio 20 handle 500 fw classid 1:41");
	startcmd($tc_filter_add." parent 1: protocol ip prio 40 handle 700 fw classid 1:43");

	startcmd($ipt_add_prefix." -m length --length 0:256 -j MARK --set-mark 500");
	startcmd($ipt_add_prefix." -m connautoqos --level 3 -j MARK --set-mark 340");
	startcmd($ipt_add_prefix." -m connautoqos --level 4 -j MARK --set-mark 340");
	startcmd($ipt_add_prefix." -m connautoqos --level 5 -j MARK --set-mark 500");
	startcmd($ipt_add_prefix." -m connautoqos --level 7 -j MARK --set-mark 700");
}

function aqc_tc_stop($rtbwcp, $name, $ifname)
{
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$ipt_flush_cmd	= "iptables -t mangle -F PST.BWC.".$name;

	/* clean all qdisc*/
	stopcmd($tc_qd_del." root 2>/dev/null");

	/* clean fastnat */
	stopcmd("echo 0 > /proc/fastnat/qos");
	stopcmd("echo > /proc/fastnat/fortcmarksupport");

	/* cleann all iptables/mangle/subchain rules */
	stopcmd($ipt_flush_cmd);
}

function bwc_tc_spq_start($rtbwcp, $name, $ifname)
{
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
		
	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	if($trate <= 0 || $trate == "")
	{
		$trate = 1024000;	/* 1000Mbps */
	}
	
	/* TC fw policy will be:
		0: check skb->mark only. This is linux native default.
		1: check connection->mark only.
		2: Prefer connection->mark, if connection->mark==0, then check skb->mark later.
		3: Prefer skb->mark, if skb->mark==0, then check connection->mark later.
		4: check connection->mark only, and speed up TCP small packets.
	  */
	startcmd("echo 4 > /proc/sche/fw_policy");

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc. */
	startcmd($tc_qd_add." root handle 66:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 66:2 handle 2:0 sfq perturb 10");

	/* add htb qdisc. */
	startcmd($tc_qd_add." parent 66:1 handle 1:0 htb default 1");
	startcmd($tc_class_add." parent 1:0 classid 1:1 htb rate ".$trate.$unit." ceil ".$trate.$unit);

	/* add prio qdisc. */
	startcmd($tc_qd_add." parent 1:1 handle 20:0 prio bands 8 priomap 7 7 7 7 7 7 7 7 7 7 7 7 7 7 7 7");

	/* add leaf qdisc */
    /* add limit, default is 128 packets, too large.
      * Smaller it can let higher/normal/bf priority have explicit rate different
      */
	startcmd($tc_qd_add." parent 20:1 handle 100:0 sfq perturb 10 limit 32");
	startcmd($tc_qd_add." parent 20:2 handle 200:0 sfq perturb 10 limit 32");
	startcmd($tc_qd_add." parent 20:3 handle 300:0 sfq perturb 10 limit 32");
	startcmd($tc_qd_add." parent 20:4 handle 400:0 sfq perturb 10 limit 32");
	startcmd($tc_qd_add." parent 20:5 handle 500:0 sfq perturb 10 limit 24");
	startcmd($tc_qd_add." parent 20:6 handle 600:0 sfq perturb 10 limit 24");
	startcmd($tc_qd_add." parent 20:7 handle 700:0 sfq perturb 10 limit 16");
	startcmd($tc_qd_add." parent 20:8 handle 800:0 sfq perturb 10 limit 16");
	
	/* add filter, connmark value will use bit8 - bit16 */
	startcmd($tc_filter_add." parent 66:0 protocol all prio 1 handle 32768 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 2 handle 16384 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 3 handle 8192 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 4 handle 4096 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 5 handle 2048 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 6 handle 1024 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 7 handle 512 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 8 handle 256 fw classid 66:1");
	
	
	startcmd($tc_filter_add." parent 20:0 protocol all prio 1 handle 32768 fw classid 20:1");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 2 handle 16384 fw classid 20:2");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 3 handle 8192 fw classid 20:3");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 4 handle 4096 fw classid 20:4");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 5 handle 2048 fw classid 20:5");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 6 handle 1024 fw classid 20:6");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 7 handle 512 fw classid 20:7");
	startcmd($tc_filter_add." parent 20:0 protocol all prio 8 handle 256 fw classid 20:8");

	/* if LAN qos enable, then enable qos on physical interface, too. */
	$prefix = cut($name,0,'-');
	
	if ($prefix == "LAN")
	{
		/* get LAN-x's phyinfp */
		$lan_phyinfp = PHYINF_getphypath($name);
		if ($lan_phyinfp != "" )
		{	
			$cnt=0;
			/* wifi physical inf */
			foreach($lan_phyinfp."/bridge/port")	{	$cnt++;	}
			foreach($lan_phyinfp."/bridge/port")
			{
				if ($InDeX > $cnt) break;
				$wlan_phyinf_uid = $VaLuE;
	
				if ( $wlan_phyinf_uid != "" )
				{
					$phyinf_name="";
					$phyinf_name = PHYINF_getifname($wlan_phyinf_uid);
					if ( $phyinf_name != "" )
					{
						$tc_qd_add		= "tc qdisc add dev ".$phyinf_name;
						$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
						$tc_class_add	= "tc class add dev ".$phyinf_name;
						$tc_filter_add	= "tc filter add dev ".$phyinf_name;				
						
						/* clean all qdisc*/
						startcmd($tc_qd_del." root 2>/dev/null");
					
						/* add root qdisc. */
						startcmd($tc_qd_add." root handle 1:0 prio bands 8 priomap 7 7 7 7 7 7 7 7 7 7 7 7 7 7 7 7");
					
						/* add leaf qdisc */
						startcmd($tc_qd_add." parent 1:1 handle 100:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:2 handle 200:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:3 handle 300:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:4 handle 400:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:5 handle 500:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:6 handle 600:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:7 handle 700:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 1:8 handle 800:0 sfq perturb 10");
									
						/* add filter */
						startcmd($tc_filter_add." parent 1:0 protocol all prio 1 handle 32768 fw classid 1:1");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 2 handle 16384 fw classid 1:2");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 3 handle 8192 fw classid 1:3");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 4 handle 4096 fw classid 1:4");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 5 handle 2048 fw classid 1:5");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 6 handle 1024 fw classid 1:6");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 7 handle 512 fw classid 1:7");
						startcmd($tc_filter_add." parent 1:0 protocol all prio 8 handle 256 fw classid 1:8");
					}				
					
				}
			}
			
			/* ethernet physical inf */
			foreach("/runtime/phyinf")
			{
				$phyinf_name="";
				if ( query("valid") == 1 &&  query("type") == "eth" && query("uid") == $lan_phyinf."-PHY_1" )
				{
					$phyinf_name = query("name");
				}
				
				if ( $phyinf_name != "" )
				{
					$tc_qd_add		= "tc qdisc add dev ".$phyinf_name;
					$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
					$tc_class_add	= "tc class add dev ".$phyinf_name;
					$tc_filter_add	= "tc filter add dev ".$phyinf_name;				
					
					/* clean all qdisc*/
					startcmd($tc_qd_del." root 2>/dev/null");
				
					/* add root qdisc. */
					startcmd($tc_qd_add." root handle 1:0 prio bands 8 priomap 7 7 7 7 7 7 7 7 7 7 7 7 7 7 7 7");
				
					/* add leaf qdisc */
					startcmd($tc_qd_add." parent 1:1 handle 100:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:2 handle 200:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:3 handle 300:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:4 handle 400:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:5 handle 500:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:6 handle 600:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:7 handle 700:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 1:8 handle 800:0 sfq perturb 10");
								
					/* add filter */
					startcmd($tc_filter_add." parent 1:0 protocol all prio 1 handle 32768 fw classid 1:1");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 2 handle 16384 fw classid 1:2");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 3 handle 8192 fw classid 1:3");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 4 handle 4096 fw classid 1:4");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 5 handle 2048 fw classid 1:5");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 6 handle 1024 fw classid 1:6");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 7 handle 512 fw classid 1:7");
					startcmd($tc_filter_add." parent 1:0 protocol all prio 8 handle 256 fw classid 1:8");
				}
			}
		}
	}

	$ipt_out_flush_cmd	= "iptables -t filter -F FWD.BWC.".$name;
	$ipt_out_add_prefix	= "iptables -t filter -A FWD.BWC.".$name;
	$ipt_out_insert_prefix	= "iptables -t filter -I FWD.BWC.".$name;
	$ipt_out_flush_http_cmd	= "iptables -t filter -F FWD.BWC.".$name.".HTTP";
	$ipt_out_add_http_prefix	= "iptables -t filter -A FWD.BWC.".$name.".HTTP";
	$ipt_out_insert_http_prefix	= "iptables -t filter -I FWD.BWC.".$name.".HTTP";
	$ipt_out_flush_p2p_cmd	= "iptables -t filter -F FWD.BWC.".$name.".P2P";
	$ipt_out_add_p2p_prefix	= "iptables -t filter -A FWD.BWC.".$name.".P2P";
	$ipt_out_insert_p2p_prefix	= "iptables -t filter -I FWD.BWC.".$name.".P2P";
	$ipt_out_flush_voice_cmd	= "iptables -t filter -F FWD.BWC.".$name.".VOICE";
	$ipt_out_add_voice_prefix	= "iptables -t filter -A FWD.BWC.".$name.".VOICE";
	$ipt_out_insert_voice_prefix	= "iptables -t filter -I FWD.BWC.".$name.".VOICE";


	/* set mark, use iptables/forward */
	startcmd($ipt_out_flush_cmd);
	startcmd($ipt_out_flush_http_cmd);
	startcmd($ipt_out_flush_p2p_cmd);
	startcmd($ipt_out_flush_voice_cmd);

	$bwc_main_chain_lan_rules = 0;
	$bwc_main_chain_wan_rules = 0;

	/* At FORWARD chain, try to filter some packets that from another LAN interface. 
		It imply that packets from WAN. */
	if ($prefix == "LAN")
	{
		$mode = query("/device/router/mode"); 
		if ($mode!="1W1L")
		{
			foreach ("/inf")
			{
				$uid = query("uid");
				$active = query("active");
				$inf_prefix = cut($uid,0,'-');
				if ( $inf_prefix == "LAN" && $name != $uid && $active == "1" )
				{
					$infstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $uid, 0);
					$addrtype = query($infstsp."/inet/addrtype");
					if ($addrtype=="ipv4" || $addrtype=="ppp4")
					{
						$lan_ifname = PHYINF_getruntimeifname($uid);
						if ( $lan_ifname != "" && $lan_ifname != $ifname )
						{
							startcmd($ipt_out_add_prefix." -i ".$lan_ifname." -j RETURN");
							$bwc_main_chain_lan_rules++;
						}
					}
				}
			}
		}
	}


	/* for upstream, mark all traffic that TO private network as 128(Bit8, lowest priority). */
	if ($prefix == "WAN")
	{
		$mark_val = 128;
		$mark_cmd= " -j CONNMARK --set-xmark ".$mark_val."/16777088";
        $dipstring=" -m iprange --dst-range 10.0.0.0-10.255.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
		
        $dipstring=" -m iprange --dst-range 172.16.0.0-172.31.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
		
        $dipstring=" -m iprange --dst-range 192.168.0.0-192.168.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
		
        $dipstring=" -m iprange --dst-range 169.254.0.0-169.254.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;

		/* Treat all multicast stream as ISP local access */
        $dipstring=" -m iprange --dst-range 224.0.0.0-239.255.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
	}
	else if ($prefix == "LAN")
	{
		/* for downstream, mark all traffic that FROM private network as 128(Bit8, lowest priority). */
		$mark_val = 128;
		$mark_cmd= " -j CONNMARK --set-xmark ".$mark_val."/16777088";
        $sipstring=" -m iprange --src-range 10.0.0.0-10.255.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

        $sipstring=" -m iprange --src-range 172.16.0.0-172.31.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

        $sipstring=" -m iprange --src-range 192.168.0.0-192.168.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

        $sipstring=" -m iprange --src-range 169.254.0.0-169.254.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

		/* Treat all multicast stream as ISP local access */
        $dipstring=" -m iprange --dst-range 224.0.0.0-239.255.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_lan_rules++;
	}

	/* default rules:
	  1. ICMP length smaller than 128 bytes as highest
	  2. ..
	  */
	
	/* 1. ICMP length smaller than 128 bytes as highest */
	$mark_val = 16384;
	$mark_cmd= " -j CONNMARK --set-xmark ".$mark_val."/16777088";
	startcmd($ipt_out_add_prefix." -p icmp -m length --length 0:128 ".$mark_cmd);
	if ($prefix == "WAN")	{	$bwc_main_chain_wan_rules++;	}
	else if ($prefix == "LAN")	{	$bwc_main_chain_lan_rules++;	}
	startcmd($ipt_out_add_prefix." -p icmp -m length --length 0:128 -j RETURN");
	if ($prefix == "WAN")	{	$bwc_main_chain_wan_rules++;	}
	else if ($prefix == "LAN")	{	$bwc_main_chain_lan_rules++;	}


	$logcmd_new	= " -j LOG --log-level info --log-prefix 'ATT:002[NEW]:'\n";
	$logcmd_original	= " -j LOG --log-level info --log-prefix 'ATT:002[ORIGINAL]:'\n";
	$logcmd_match	= " -j LOG --log-level info --log-prefix 'ATT:002[MATCH]:'\n";
	$logcmd_unmatch	= " -j LOG --log-level info --log-prefix 'ATT:002[UNMATCH]:'\n";
	$logcmd_skip	= " -j LOG --log-level info --log-prefix 'ATT:002[SKIP]:'\n";

	/* if apps shall be special flag, Bit17-Bit24 will be used as special flag only.*/
	$have_flag_apps=0;
	/* Bit17 will be used as HTTP related app's special flag.*/
	$have_http_flag_apps = 0;
	/* Bit18 will be used as P2P related app's special flag.*/
	$have_p2p_flag_apps = 0;
	/* Bit19 will be used as VOICE related app's special flag.*/
	$have_voice_flag_apps = 0;
	//$entry_count = query($rtbwcp."/count");
	$entry_count = query($rtbwcp."/rules/count");
	$entry_idx = 0;

	foreach($rtbwcp."/rules/entry")
	{
		$entry_idx++;
		if (query("enable")=="1" && $entry_idx <= $entry_count )
		{
			/* does this entry is http related apps - such as Youtube */
			$entry_is_http_apps = 0;
			/* does this entry is p2p related apps - such as Bittorrent */
			$entry_is_p2p_apps = 0;
			/* does this entry is voice related apps - such as SIP */
			$entry_is_voice_apps = 0;
			$bwcqd_name = query("bwcqd");
			$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd", "entry", "uid", $bwcqd_name, 0);
			if( $bwcqdp == "" ) { continue; }

			$bwcf_name = query("bwcf");
			$bwcfp = XNODE_getpathbytarget("/bwc/bwcf", "entry", "uid", $bwcf_name, 0);
			if( $bwcfp == "" ) { continue; }

			$proto = query($bwcfp."/protocol");
			$startip = query($bwcfp."/ipv4/start");
			$endip = query($bwcfp."/ipv4/end");
			$dstartip = query($bwcfp."/dst/ipv4/start");
			$dendip = query($bwcfp."/dst/ipv4/end");
			$dporttype = query($bwcfp."/dst/port/type");
			$dportname = query($bwcfp."/dst/port/name");
			$dstartport = query($bwcfp."/dst/port/start");
			$dendport = query($bwcfp."/dst/port/end");
			
			$priority = query($bwcqdp."/priority");
			if ( $priority == "VO" )	{	$mark_val = 16384;	}
			else if ( $priority == "VI" )	{	$mark_val = 4096;	}
			else if ( $priority == "BG" )	{	$mark_val = 1024;	}
			else if ( $priority == "BE" )	{	$mark_val = 256;	}
			else 							{	$mark_val = 256;	}
			
			
			/* connection mark is unsigned int
				Bit1-Bit7 will not be used.
				Bit25-Bit32 will not be used.
				Bit8-Bit16 will be used as connection priority at tc fw classifier.
				Bit17-Bit24 will be used as special flag only.
				Now, Bit17 is used for HTTP base application only.
				When connection is HTTP(dport==80), then this flag will be enable, and it's value will be 0x10000.
				When flag enable, then this packet will not be processed by Turbonat.
				When flag enable, then this packet will be check by some layer7 match, such as Youtube.
				When layer7 matched, then this flag will be erase, then packet can be processed by turbonat.
				Note: for xmark: F indicate clear, 0 indicate keep it
				*/ 
			/* 	0x0000FF80	= 65408, 0x00FFFF80	= 16777088 */
			$mark_cmd		= " -j CONNMARK --set-xmark ".$mark_val."/65408";
			$mark2_cmd		= " -j CONNMARK --set-xmark ".$mark_val."/16777088";

			/* protocol str */
            if        ($proto=="TCP")    { $protocol=" -p tcp"; }
            else if   ($proto=="UDP")    { $protocol=" -p udp"; }
            else if   ($proto=="ICMP")    { $protocol=" -p icmp"; }
            else						 { $proto="ALL"; $protocol=" -p all"; }

			//marco, if proto is all , we need to seperate the rules to tcp and udp instead os -p all
			if($proto=="ALL")
			{
				$proto_is_all=1;	
				$index=2;
			}
			else
			{
				$index=1;
				$proto_is_all=0;
			}
			while($index>0)
			{			
				if($proto_is_all==1)
				{
					if($index==2)
					{
						$protocol=" -p tcp";
					}
					else
					{
						$protocol=" -p udp";
					}		
				}
				$index--;
				
				/* src iptype */
				$iprange = 0;
				$sipstring="";
				$in_iprange = 0;
				$in_dipstring="";
	            if ( $startip != "" && $endip == "" ) 
	            { 
	            	$sipstring=" -s ".$startip; 
	            	$in_dipstring=" -d ".$startip; 
	            }
	            else if ( $startip != "" && $startip == $endip ) 
	            { 
	            	$sipstring=" -s ".$startip; 
	            	$in_dipstring=" -d ".$startip; 
	            }
	            else if ( $startip != "" && $startip != $endip )	
	            { 
	            	$sipstring=" -m iprange --src-range ".$startip."-".$endip; 
	            	$iprange=1;
	            	$in_dipstring=" -m iprange --dst-range ".$startip."-".$endip; 
	            	$in_iprange=1;
	            }
	
				/* dst iptype */
				$dipstring="";
				$in_sipstring="";
	            if ( $dstartip != "" && $dendip == "" ) 
	            { 
	            	$dipstring=" -d ".$dstartip; 
	            	$in_sipstring=" -s ".$dstartip; 
	            }
	            else if ( $dstartip != "" && $dstartip == $dendip ) 
	            { 
	            	$dipstring=" -d ".$dstartip; 
	            	$in_sipstring=" -s ".$dstartip; 
	            }
	            else if ( $dstartip != "" && $dstartip != $dendip )	
	            { 
	            	if ($iprange == 0 )	{$dipstring=" -m iprange --dst-range ".$dstartip."-".$dendip;}
	            	else				{$dipstring=" --dst-range ".$dstartip."-".$dendip; }
	            	if ($in_iprange == 0 )	{$in_sipstring=" -m iprange --src-range ".$dstartip."-".$dendip;}
	            	else				{$in_sipstring=" --src-range ".$dstartip."-".$dendip; }
	            }
					
				$dportstring="";
				$in_sportstring="";
				if ($proto == "TCP" || $proto == "UDP" || $proto == "ALL" )
	            {
					if ( $dporttype == 1 && $dportname != "" )
					{
						if ( $dportname == "YOUTUBE" || 
								$dportname == "HTTP_AUDIO" || 
								$dportname == "HTTP_VIDEO" || 
								$dportname == "HTTP_DOWNLOAD" )	
						{	
							$have_flag_apps=1;
							$have_http_flag_apps=1;	
							$entry_is_http_apps=1; 
						}
						else if ( $dportname == "P2P" )	
						{	
							$have_flag_apps=1;
							$have_p2p_flag_apps=1;	
							$entry_is_p2p_apps=1; 
						}
						else if ( $dportname == "VOICE" )	
						{	
							$have_flag_apps=1;
							$have_voice_flag_apps=1;	
							$entry_is_voice_apps=1; 
						}
						else if ( $dportname == "HTTP" )	
						{	
			            	$dportstring=" --dport 80"; 
			            	$in_sportstring=" --sport 80";
						}
						else if ( $dportname == "FTP" )	
						{	
			            	$dportstring=" --dport 21"; 
			            	$in_sportstring=" --sport 21"; 
						}
					}
					else
					{ 
		                /* gen dst portstring */
			            if ( $dstartport != "" && $dendport == "" ) 
			            { 
			            	$dportstring=" --dport ".$dstartport; 
			            	$in_sportstring=" --sport ".$dstartport; 
			            }
			            else if ( $dstartport != "" && $dstartport == $dendport ) 
			            { 
			            	$dportstring=" --dport ".$dstartport; 
			            	$in_sportstring=" --sport ".$dstartport; 
			            }
		 	            else if ( $dstartport != "" && $dstartport != $dendport )	
		 	            { 
		 	            	$dportstring=" --dport ".$dstartport.":".$dendport; 
		 	            	$in_sportstring=" --sport ".$dstartport.":".$dendport; 
		 	            }
		 	        }
				}
	
				if ( $entry_is_http_apps == 1 )
				{
					if ( $dportname == "YOUTUBE" || 
							$dportname == "HTTP_AUDIO" || 
							$dportname == "HTTP_VIDEO" || 
							$dportname == "HTTP_DOWNLOAD" )	
					{
						if ( $dportname == "YOUTUBE" )	{	$layer7_pattern_name = "youtube";	}
						else if ( $dportname == "HTTP_AUDIO" )	{	$layer7_pattern_name = "httpaudio";	}
						else if ( $dportname == "HTTP_VIDEO" )	{	$layer7_pattern_name = "httpvideo";	}
						else if ( $dportname == "HTTP_DOWNLOAD" )	{	$layer7_pattern_name = "httpdownload";	}
						
						/* 65536 = 0x10000 */
						if ($prefix == "LAN")
						{
							startcmd($ipt_out_add_http_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 65536/65536 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							startcmd($ipt_out_add_http_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/65536 -j RETURN");
						}
						else
						{
							startcmd($ipt_out_add_http_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 65536/65536 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							startcmd($ipt_out_add_http_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/65536 -j RETURN");
						}
					}
				}
				else if ( $entry_is_voice_apps == 1 )
				{
					/* how many VOICE apps will be detected */
					$voice_apps_max = 5;
					$voice_apps_index = 1;
					
					while ( $voice_apps_index <= $voice_apps_max )
					{
						if 		( $voice_apps_index == 1 )	{	$layer7_pattern_name = "jabber";	$protocol = " -p tcp";	}
						else if ( $voice_apps_index == 2 )	{	$layer7_pattern_name = "sip";	$protocol = " -p udp";	}
						else if ( $voice_apps_index == 3 )	{	$layer7_pattern_name = "rtp_tcp";	$protocol = " -p tcp";	}
						else if ( $voice_apps_index == 4 )	{	$layer7_pattern_name = "rtp";	$protocol = " -p udp";	}
						else if ( $voice_apps_index == 5 )	{	$layer7_pattern_name = "rtcp";	$protocol = " -p udp";	}
	
						
						/* 327552 = 0x4FF80, 262144= 0x40000, 16711680 = 0xFF0000 */
						if ($prefix == "LAN")
						{
							//startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 262144/262144 ".$logcmd_original );
							startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 262144/262144 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							//startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/262144 ".$logcmd_match );
							startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/262144 -j RETURN");
							//startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark ! --mark 0/262144 ".$logcmd_unmatch );
						}
						else
						{
							//startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 262144/262144 ".$logcmd_original );
							startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 262144/262144 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							//startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/262144 ".$logcmd_match );
							startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/262144 -j RETURN");
							//startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark ! --mark 0/262144  ".$logcmd_unmatch );
						}
						$voice_apps_index++;
					}
				}
				else if ( $entry_is_p2p_apps == 1 )
				{
					/* how many P2P apps will be detected */
					$p2p_max = 2;
					$p2p_index = 1;
					
					while ( $p2p_index <= $p2p_max )
					{
						if 		( $p2p_index == 1 )	{	$layer7_pattern_name = "bittorrent";	}
						else if ( $p2p_index == 2 )	{	$layer7_pattern_name = "edonkey";	}
	
						/* 131072 = 0x20000 */
						if ($prefix == "LAN")
						{
							if ( $layer7_pattern_name == "edonkey" )
							{
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark ! --mark 131072/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix." -p tcp ".$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_tcp ".$mark2_cmd);
								startcmd($ipt_out_add_p2p_prefix." -p udp ".$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_udp ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark ! --mark 0/131072 ".$logcmd_unmatch );
							}
							else
							{
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark ! --mark 0/131072 ".$logcmd_unmatch );
							}
						}
						else
						{
							if ( $layer7_pattern_name == "edonkey" )
							{
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark ! --mark 131072/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix." -p tcp ".$sipstring.$dipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_tcp ".$mark2_cmd);
								startcmd($ipt_out_add_p2p_prefix." -p udp ".$sipstring.$dipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_udp ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark ! --mark 0/131072 ".$logcmd_unmatch );
							}
							else
							{
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark ! --mark 0/131072  ".$logcmd_unmatch );
							}
						}
						$p2p_index++;
					}
				}
				else
				{
					if ($prefix == "LAN")
					{
						startcmd($ipt_out_add_prefix.$protocol.$in_dipstring.$in_sipstring.$in_sportstring.$mark_cmd);
						startcmd($ipt_out_add_prefix.$protocol.$in_dipstring.$in_sipstring.$in_sportstring." -j RETURN");
					}
					else
					{
						startcmd($ipt_out_add_prefix.$protocol.$sipstring.$dipstring.$dportstring.$mark_cmd);
						startcmd($ipt_out_add_prefix.$protocol.$sipstring.$dipstring.$dportstring." -j RETURN");
					}
				}
		    }//marco
		}
	}

	/* add some command for special flag apps */
	$is_first=1;
	if ($prefix == "LAN")	{	$insert_idx= $bwc_main_chain_lan_rules + 1;		}
	else					{	$insert_idx= $bwc_main_chain_wan_rules + 1;		}
	if ( $have_flag_apps == 1 )
	{
		if ( $have_http_flag_apps == 1 )
		{
			/* careful for command sequence */
			/* for all NEW http base apps come in, give it a flag value(bit17). */
			/* 65536 = 0x10000, 130944= 0x1FF80, 16711680 = 0xFF0000, 65408=0xFF80 */
			startcmd($ipt_out_insert_http_prefix." 1 -m connmark --mark 0/65536 -j RETURN");
			startcmd($ipt_out_insert_http_prefix." 1 -m connmark --mark 0/130944 -j CONNMARK --set-xmark 65536/65536");
			/* Flag bit is set(bit17), and connection have transfer over 2k bytes, then disable the flag. */
			startcmd($ipt_out_add_http_prefix." -m connmark --mark 65536/65536 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes -j CONNMARK --set-xmark 0/65536");
			/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
			/* jump to user-define chain to process all http related apps, such as Youtube */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -p tcp --dport 80 -j FWD.BWC.".$name.".HTTP");
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -p tcp --sport 80 -j FWD.BWC.".$name.".HTTP");
			if ( $is_first == 1 )	
			{	
				/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
				startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
				$insert_idx = $insert_idx +4;	
			}
			else					
			{	
				$insert_idx = $insert_idx +3;	
			}
			$is_first++;
		}

		if ( $have_voice_flag_apps == 1 )
		{
			/* careful for command sequence */
			/* for all NEW VOICE base apps come in, give it a flag value(bit19). */
			/* 327552 = 0x4FF80, 262144= 0x40000, 16711680 = 0xFF0000, 65408=0xFF80 */
			startcmd($ipt_out_insert_voice_prefix." 1 -m connmark --mark 0/262144 -j RETURN");
			startcmd($ipt_out_insert_voice_prefix." 1 -m connmark --mark 0/327552 -j CONNMARK --set-xmark 262144/262144");
			//startcmd($ipt_out_insert_voice_prefix." 1 -m connmark --mark 0/327552 ".$logcmd_new );
			/* Flag bit is set(bit19), and connection have transfer over 2k bytes, then disable the flag. */
			//startcmd($ipt_out_add_voice_prefix." -m connmark --mark 262144/262144 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes ".$logcmd_skip);
			startcmd($ipt_out_add_voice_prefix." -m connmark --mark 262144/262144 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes -j CONNMARK --set-xmark 0/262144");
			/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
			/* jump to user-define chain to process all voice related apps, such as SIP */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -j FWD.BWC.".$name.".VOICE");
			if ( $is_first == 1 )	
			{	
				/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
				startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
				$insert_idx = $insert_idx +3;	
			}
			else					
			{	
				$insert_idx = $insert_idx +2;	
			}
			$is_first++;
		}
		
		if ( $have_p2p_flag_apps == 1 )
		{
			/* careful for command sequence */
			/* 131072 = 0x20000, 196480= 0x2FF80, 16711680 = 0xFF0000, 65408=0xFF80 */
			startcmd($ipt_out_insert_p2p_prefix." 1 -m connmark --mark 0/131072 -j RETURN");
			/* for all NEW P2P base apps come in, give it a flag value(bit18). */
			startcmd($ipt_out_insert_p2p_prefix." 1 -m connmark --mark 0/196480 -j CONNMARK --set-xmark 131072/131072");
			//startcmd($ipt_out_insert_p2p_prefix." 1 -m connmark --mark 0/196480  ".$logcmd_new );
			/* Flag bit is set(bit18), and connection have transfer over 2k bytes, then disable the flag. */
			//startcmd($ipt_out_add_p2p_prefix." -m connmark --mark 131072/131072 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes ".$logcmd_skip);
			startcmd($ipt_out_add_p2p_prefix." -m connmark --mark 131072/131072 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes -j CONNMARK --set-xmark 0/131072");
			/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
			/* jump to user-define chain to process all http related apps, such as Youtube */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -j FWD.BWC.".$name.".P2P");
			if ( $is_first == 1 )	
			{	
				/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
				startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
				$insert_idx = $insert_idx +3;	
			}
			else					
			{	
				$insert_idx = $insert_idx +2;	
			}
			$is_first++;
		}
	}

	/* Finally, mark all packets are FROM/TO internet and doesn't have connmark(0) , priority is 256 */
	$mark_val = 256;
	$mark_cmd= " -m connmark --mark 0/65408 -j CONNMARK --set-xmark ".$mark_val."/65408";
	startcmd($ipt_out_add_prefix.$mark_cmd);

	/* OUTPUT chain: for all traffic that device send out will be treat as the highest priority packet */
	$ipt_output_flush_cmd	= "iptables -t filter -F OUTP.BWC.".$name;
	$ipt_output_add_prefix	= "iptables -t filter -A OUTP.BWC.".$name;

	/* set mark, use iptables/output */
	startcmd($ipt_output_flush_cmd);

	/* OUTPUT chain: for all traffic mark as lowest priority, 
		it means that those traffic will not have rate limit. 
		But, it will lost traffic scheduling feature at the same time. 
		*/
	$mark_val = 128;
	$mark_cmd= " -m connmark --mark 0/65408 -j CONNMARK --set-xmark ".$mark_val."/65408";
	startcmd($ipt_output_add_prefix.$mark_cmd);
}

function bwc_tc_spq_stop($rtbwcp, $name, $ifname)
{
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");	
	
	$tc_qd_del		= "tc qdisc del dev ".$ifname;

	/* clean all qdisc*/
	stopcmd($tc_qd_del." root 2>/dev/null");

	$prefix = cut($name,0,'-');
	if ($prefix == "LAN")
	{
		/* get LAN-x's phyinfp */
		$lan_phyinfp = PHYINF_getphypath($name);
		if ($lan_phyinfp != "" )
		{	
			$cnt=0;
			/* wifi physical inf */
			foreach($lan_phyinfp."/bridge/port")	{	$cnt++;	}
			foreach($lan_phyinfp."/bridge/port")
			{
				if ($InDeX > $cnt) break;
				$wlan_phyinf_uid = $VaLuE;
	
				if ( $wlan_phyinf_uid != "" )
				{
					$phyinf_name="";
					$phyinf_name = PHYINF_getifname($wlan_phyinf_uid);
					if ( $phyinf_name != "" )
					{
						$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
						
						/* clean all qdisc*/
						stopcmd($tc_qd_del." root 2>/dev/null");
					}				
				}
			}
			
			/* ethernet physical inf */
			foreach("/runtime/phyinf")
			{
				$phyinf_name="";
				if ( query("valid") == 1 &&  query("type") == "eth" && query("uid") == $lan_phyinf."-PHY_1" )
				{
					$phyinf_name = query("name");
				}
				
				if ( $phyinf_name != "" )
				{
					$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
					
					/* clean all qdisc*/
					stopcmd($tc_qd_del." root 2>/dev/null");
				}
			}
		}
	}
	
	/* cleann all iptables/forward/subchain rules */
	$ipt_out_flush_cmd	= "iptables -t filter -F FWD.BWC.".$name;
	stopcmd($ipt_out_flush_cmd);
	$ipt_out_flush_http_cmd	= "iptables -t filter -F FWD.BWC.".$name.".HTTP";
	stopcmd($ipt_out_flush_http_cmd);
	$ipt_out_flush_p2p_cmd	= "iptables -t filter -F FWD.BWC.".$name.".P2P";
	stopcmd($ipt_out_flush_p2p_cmd);
	$ipt_out_flush_voice_cmd	= "iptables -t filter -F FWD.BWC.".$name.".VOICE";
	stopcmd($ipt_out_flush_voice_cmd);

	/* cleann all iptables/output/subchain rules */
	$ipt_output_flush_cmd	= "iptables -t filter -F OUTP.BWC.".$name;
	stopcmd($ipt_output_flush_cmd);
}	
	
function bwc_tc_wfq_start($rtbwcp, $name, $ifname)
{
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	
	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	if($trate <= 0 || $trate == "")
	{
		$trate = 1024000;	/* 1000Mbps */
	}

	foreach("/bwc/bwcqd/entry")
	{
		$priority = query("priority");
		$weight = query("weight");
		if ( $priority == "VO" )	{	if ($weight != "" && $weight > 0 )	{	$weight_q1 = $weight; }	}
		else if ( $priority == "VI" )	{	if ($weight != "" && $weight > 0 )	{	$weight_q2 = $weight; }	}
		else if ( $priority == "BG" )	{	if ($weight != "" && $weight > 0 )	{	$weight_q3 = $weight; }	}
		else if ( $priority == "BE" )	{	if ($weight != "" && $weight > 0 )	{	$weight_q4 = $weight; }	}
	}

	if ($weight_q1 == 0 || $weight_q1 == "" )	{	$weight_q1 =1; }
	if ($weight_q2 == 0 || $weight_q2 == "" )	{	$weight_q2 =1; }
	if ($weight_q3 == 0 || $weight_q3 == "" )	{	$weight_q3 =1; }
	if ($weight_q4 == 0 || $weight_q4 == "" )	{	$weight_q4 =1; }

	$tatal_weight = $weight_q1 + $weight_q2 + $weight_q3 +	$weight_q4;
	$rate_max_q1= $trate * 96 / 100;
	if ($rate_max_q1 <= 0)	{	$rate_max_q1 = 1;	}
	$rate_max_q2= $trate * 94 / 100;
	if ($rate_max_q2 <= 0)	{	$rate_max_q2 = 1;	}
	$rate_max_q3= $trate * 92 / 100;
	if ($rate_max_q3 <= 0)	{	$rate_max_q3 = 1;	}
	$rate_max_q4= $trate * 90 / 100;
	if ($rate_max_q4 <= 0)	{	$rate_max_q4 = 1;	}

	$rate_min_q1= $trate * $weight_q1 / $tatal_weight;
	if ($rate_min_q1 <= 0)	{	$rate_min_q1 = 1;	}
	$rate_min_q2= $trate * $weight_q2 / $tatal_weight;
	if ($rate_min_q2 <= 0)	{	$rate_min_q2 = 1;	}
	$rate_min_q3= $trate * $weight_q3 / $tatal_weight;
	if ($rate_min_q3 <= 0)	{	$rate_min_q3 = 1;	}
	$rate_min_q4= $trate * $weight_q4 / $tatal_weight;
	if ($rate_min_q4 <= 0)	{	$rate_min_q4 = 1;	}

	/* TC fw policy will be:
		0: check skb->mark only. This is linux native default.
		1: check connection->mark only.
		2: Prefer connection->mark, if connection->mark==0, then check skb->mark later.
		3: Prefer skb->mark, if skb->mark==0, then check connection->mark later.
		4: check connection->mark only, and speed up TCP small packets.
	  */
	startcmd("echo 4 > /proc/sche/fw_policy");

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc. */
	startcmd($tc_qd_add." root handle 66:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 66:2 handle 2:0 sfq perturb 10");

	/* add htb qdisc. */
	startcmd($tc_qd_add." parent 66:1 handle 1:0 htb default 5");
	startcmd($tc_class_add." parent 1:0 classid 1:1 htb rate ".$trate.$unit." ceil ".$trate.$unit);
    /* For WFQ, doesn't set "prio" parameter, when it bet set, then free bandwidth will be whole get by the highest priority class.
      * Remove prio setting, then each class can get free bandwidth fair
      */
	startcmd($tc_class_add." parent 1:1 classid 1:2 htb rate ".$rate_min_q1.$unit." ceil ".$rate_max_q1.$unit);
	startcmd($tc_class_add." parent 1:1 classid 1:3 htb rate ".$rate_min_q2.$unit." ceil ".$rate_max_q2.$unit);
	startcmd($tc_class_add." parent 1:1 classid 1:4 htb rate ".$rate_min_q3.$unit." ceil ".$rate_max_q3.$unit);
	startcmd($tc_class_add." parent 1:1 classid 1:5 htb rate ".$rate_min_q4.$unit." ceil ".$rate_max_q4.$unit);

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 1:2 handle 200:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");
	startcmd($tc_qd_add." parent 1:3 handle 300:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");
	startcmd($tc_qd_add." parent 1:4 handle 400:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");
	startcmd($tc_qd_add." parent 1:5 handle 500:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");

	/* add leaf qdisc */
	startcmd($tc_qd_add." parent 200:1 handle 1000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 200:2 handle 2000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 300:1 handle 3000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 300:2 handle 4000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 400:1 handle 5000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 400:2 handle 6000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 500:1 handle 7000:0 sfq perturb 10");
	startcmd($tc_qd_add." parent 500:2 handle 8000:0 sfq perturb 10");

	/* add filter */
	startcmd($tc_filter_add." parent 66:0 protocol all prio 1 handle 32768 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 2 handle 16384 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 3 handle 8192 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 4 handle 4096 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 5 handle 2048 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 6 handle 1024 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 7 handle 512 fw classid 66:1");
	startcmd($tc_filter_add." parent 66:0 protocol all prio 8 handle 256 fw classid 66:1");
	
	startcmd($tc_filter_add." parent 1:0 protocol all prio 1 handle 32768 fw classid 1:2");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 2 handle 16384 fw classid 1:2");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 3 handle 8192 fw classid 1:3");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 4 handle 4096 fw classid 1:3");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 5 handle 2048 fw classid 1:4");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 6 handle 1024 fw classid 1:4");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 7 handle 512 fw classid 1:5");
	startcmd($tc_filter_add." parent 1:0 protocol all prio 8 handle 256 fw classid 1:5");
	
	startcmd($tc_filter_add." parent 200:0 protocol all prio 1 handle 32768 fw classid 200:1");
	startcmd($tc_filter_add." parent 300:0 protocol all prio 2 handle 8192 fw classid 300:1");
	startcmd($tc_filter_add." parent 400:0 protocol all prio 3 handle 2048 fw classid 400:1");
	startcmd($tc_filter_add." parent 500:0 protocol all prio 4 handle 512 fw classid 500:1");


	/* if LAN qos enable, then enable qos on wlan interface, too. */
	$prefix = cut($name,0,'-');
	if ($prefix == "LAN")
	{
		/* get LAN-x's phyinfp */
		$lan_phyinfp = PHYINF_getphypath($name);
		if ($lan_phyinfp != "" )
		{	
			$cnt=0;
			/* wifi physical inf */
			foreach($lan_phyinfp."/bridge/port")	{	$cnt++;	}
			foreach($lan_phyinfp."/bridge/port")
			{
				if ($InDeX > $cnt) break;
				$wlan_phyinf_uid = $VaLuE;
	
				if ( $wlan_phyinf_uid != "" )
				{
					$phyinf_name="";
					$phyinf_name = PHYINF_getifname($wlan_phyinf_uid);
					if ( $phyinf_name != "" )
					{
						$tc_qd_add		= "tc qdisc add dev ".$phyinf_name;
						$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
						$tc_class_add	= "tc class add dev ".$phyinf_name;
						$tc_filter_add	= "tc filter add dev ".$phyinf_name;				
						
						/* clean all qdisc*/
						startcmd($tc_qd_del." root 2>/dev/null");
					
						/* add root qdisc. */
						startcmd($tc_qd_add." root handle 66:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");
					
						/* add leaf qdisc */
						startcmd($tc_qd_add." parent 66:1 handle 1:0 sfq perturb 10");
						startcmd($tc_qd_add." parent 66:2 handle 2:0 sfq perturb 10");
		
						/* add filter */
						startcmd($tc_filter_add." parent 66:0 protocol all prio 1 handle 32768 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 2 handle 16384 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 3 handle 8192 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 4 handle 4096 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 5 handle 2048 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 6 handle 1024 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 7 handle 512 fw classid 66:1");
						startcmd($tc_filter_add." parent 66:0 protocol all prio 8 handle 256 fw classid 66:1");
					}				
				}
			}
			
			/* ethernet physical inf */
			foreach("/runtime/phyinf")
			{
				$phyinf_name="";
				if ( query("valid") == 1 &&  query("type") == "eth" && query("uid") == $lan_phyinf."-PHY_1" )
				{
					$phyinf_name = query("name");
				}
				
				if ( $phyinf_name != "" )
				{
					$tc_qd_add		= "tc qdisc add dev ".$phyinf_name;
					$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
					$tc_class_add	= "tc class add dev ".$phyinf_name;
					$tc_filter_add	= "tc filter add dev ".$phyinf_name;				
					
					/* clean all qdisc*/
					startcmd($tc_qd_del." root 2>/dev/null");
				
					/* add root qdisc. */
					startcmd($tc_qd_add." root handle 66:0 prio bands 2 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1");
				
					/* add leaf qdisc */
					startcmd($tc_qd_add." parent 66:1 handle 1:0 sfq perturb 10");
					startcmd($tc_qd_add." parent 66:2 handle 2:0 sfq perturb 10");
	
					/* add filter */
					startcmd($tc_filter_add." parent 66:0 protocol all prio 1 handle 32768 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 2 handle 16384 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 3 handle 8192 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 4 handle 4096 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 5 handle 2048 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 6 handle 1024 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 7 handle 512 fw classid 66:1");
					startcmd($tc_filter_add." parent 66:0 protocol all prio 8 handle 256 fw classid 66:1");
				}
			}
		}
	}
	
	$ipt_out_flush_cmd	= "iptables -t filter -F FWD.BWC.".$name;
	$ipt_out_add_prefix	= "iptables -t filter -A FWD.BWC.".$name;
	$ipt_out_insert_prefix	= "iptables -t filter -I FWD.BWC.".$name;
	$ipt_out_flush_http_cmd	= "iptables -t filter -F FWD.BWC.".$name.".HTTP";
	$ipt_out_add_http_prefix	= "iptables -t filter -A FWD.BWC.".$name.".HTTP";
	$ipt_out_insert_http_prefix	= "iptables -t filter -I FWD.BWC.".$name.".HTTP";
	$ipt_out_flush_p2p_cmd	= "iptables -t filter -F FWD.BWC.".$name.".P2P";
	$ipt_out_add_p2p_prefix	= "iptables -t filter -A FWD.BWC.".$name.".P2P";
	$ipt_out_insert_p2p_prefix	= "iptables -t filter -I FWD.BWC.".$name.".P2P";
	$ipt_out_flush_voice_cmd	= "iptables -t filter -F FWD.BWC.".$name.".VOICE";
	$ipt_out_add_voice_prefix	= "iptables -t filter -A FWD.BWC.".$name.".VOICE";
	$ipt_out_insert_voice_prefix	= "iptables -t filter -I FWD.BWC.".$name.".VOICE";

	/* set mark, use iptables/forward */
	startcmd($ipt_out_flush_cmd);
	startcmd($ipt_out_flush_http_cmd);
	startcmd($ipt_out_flush_p2p_cmd);
	startcmd($ipt_out_flush_voice_cmd);

	$bwc_main_chain_lan_rules = 0;
	$bwc_main_chain_wan_rules = 0;

	/* At FORWARD chain, try to filter some packets that from another LAN interface. 
		It imply that packets from WAN. */
	if ($prefix == "LAN")
	{
		$mode = query("/device/router/mode"); 
		if ($mode!="1W1L")
		{
			foreach ("/inf")
			{
				$uid = query("uid");
				$active = query("active");
				$inf_prefix = cut($uid,0,'-');
				if ( $inf_prefix == "LAN" && $name != $uid && $active == "1" )
				{
					$infstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $uid, 0);
					$addrtype = query($infstsp."/inet/addrtype");
					if ($addrtype=="ipv4" || $addrtype=="ppp4")
					{
						$lan_ifname = PHYINF_getruntimeifname($uid);
						if ( $lan_ifname != "" && $lan_ifname != $ifname )
						{
							startcmd($ipt_out_add_prefix." -i ".$lan_ifname." -j RETURN");
							$bwc_main_chain_lan_rules++;
						}
					}
				}
			}
		}
	}

	/* for upstream, mark all traffic that TO private network as 128(Bit8, lowest priority). */
	if ($prefix == "WAN")
	{
		$mark_val = 128;
		$mark_cmd= " -j CONNMARK --set-xmark ".$mark_val."/16777088";
        $dipstring=" -m iprange --dst-range 10.0.0.0-10.255.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
		
        $dipstring=" -m iprange --dst-range 172.16.0.0-172.31.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
		
        $dipstring=" -m iprange --dst-range 192.168.0.0-192.168.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
		
        $dipstring=" -m iprange --dst-range 169.254.0.0-169.254.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;

		/* Treat all multicast stream as ISP local access */
        $dipstring=" -m iprange --dst-range 224.0.0.0-239.255.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_wan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_wan_rules++;
	}
	else if ($prefix == "LAN")
	{
		/* for downstream, mark all traffic that FROM private network as 128(Bit8, lowest priority). */
		$mark_val = 128;
		$mark_cmd= " -j CONNMARK --set-xmark ".$mark_val."/16777088";
        $sipstring=" -m iprange --src-range 10.0.0.0-10.255.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

        $sipstring=" -m iprange --src-range 172.16.0.0-172.31.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

        $sipstring=" -m iprange --src-range 192.168.0.0-192.168.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

        $sipstring=" -m iprange --src-range 169.254.0.0-169.254.255.255";
		startcmd($ipt_out_add_prefix.$sipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$sipstring." -j RETURN");		
		$bwc_main_chain_lan_rules++;

		/* Treat all multicast stream as ISP local access */
        $dipstring=" -m iprange --dst-range 224.0.0.0-239.255.255.255";
		startcmd($ipt_out_add_prefix.$dipstring.$mark_cmd);
		$bwc_main_chain_lan_rules++;
		startcmd($ipt_out_add_prefix.$dipstring." -j RETURN");
		$bwc_main_chain_lan_rules++;
	}
	
	/* default rules:
	  1. ICMP length smaller than 128 bytes as highest
	  2. ..
	  */
	
	/* 1. ICMP length smaller than 128 bytes as highest */
	$mark_val = 16384;
	$mark_cmd= " -j CONNMARK --set-xmark ".$mark_val."/16777088";
	startcmd($ipt_out_add_prefix." -p icmp -m length --length 0:128 ".$mark_cmd);
	if ($prefix == "WAN")	{	$bwc_main_chain_wan_rules++;	}
	else if ($prefix == "LAN")	{	$bwc_main_chain_lan_rules++;	}
	startcmd($ipt_out_add_prefix." -p icmp -m length --length 0:128 -j RETURN");
	if ($prefix == "WAN")	{	$bwc_main_chain_wan_rules++;	}
	else if ($prefix == "LAN")	{	$bwc_main_chain_lan_rules++;	}

	
	$logcmd_new	= " -j LOG --log-level info --log-prefix 'ATT:002[NEW]:'\n";
	$logcmd_original	= " -j LOG --log-level info --log-prefix 'ATT:002[ORIGINAL]:'\n";
	$logcmd_match	= " -j LOG --log-level info --log-prefix 'ATT:002[MATCH]:'\n";
	$logcmd_unmatch	= " -j LOG --log-level info --log-prefix 'ATT:002[UNMATCH]:'\n";
	$logcmd_skip	= " -j LOG --log-level info --log-prefix 'ATT:002[SKIP]:'\n";

	/* if apps shall be special flag, Bit17-Bit24 will be used as special flag only.*/
	$have_flag_apps=0;
	/* Bit17 will be used as HTTP related app's special flag.*/
	$have_http_flag_apps = 0;
	/* Bit18 will be used as P2P related app's special flag.*/
	$have_p2p_flag_apps = 0;
	/* Bit19 will be used as VOICE related app's special flag.*/
	$have_voice_flag_apps = 0;
	//$entry_count = query($rtbwcp."/count");
	$entry_count = query($rtbwcp."/rules/count");
	$entry_idx = 0;

	foreach($rtbwcp."/rules/entry")
	{
		$entry_idx++;
		if (query("enable")=="1" && $entry_idx <= $entry_count )
		{
			/* does this entry is http related apps - such as Youtube */
			$entry_is_http_apps = 0;
			/* does this entry is p2p related apps - such as Bittorrent */
			$entry_is_p2p_apps = 0;
			/* does this entry is voice related apps - such as SIP */
			$entry_is_voice_apps = 0;
			$bwcqd_name = query("bwcqd");
			$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd", "entry", "uid", $bwcqd_name, 0);
			if( $bwcqdp == "" ) { continue; }

			$bwcf_name = query("bwcf");
			$bwcfp = XNODE_getpathbytarget("/bwc/bwcf", "entry", "uid", $bwcf_name, 0);
			if( $bwcfp == "" ) { continue; }

			$proto = query($bwcfp."/protocol");
			$startip = query($bwcfp."/ipv4/start");
			$endip = query($bwcfp."/ipv4/end");
			$dstartip = query($bwcfp."/dst/ipv4/start");
			$dendip = query($bwcfp."/dst/ipv4/end");
			$dporttype = query($bwcfp."/dst/port/type");
			$dportname = query($bwcfp."/dst/port/name");
			$dstartport = query($bwcfp."/dst/port/start");
			$dendport = query($bwcfp."/dst/port/end");
			
			$priority = query($bwcqdp."/priority");
			if ( $priority == "VO" )	{	$mark_val = 16384;	}
			else if ( $priority == "VI" )	{	$mark_val = 4096;	}
			else if ( $priority == "BG" )	{	$mark_val = 1024;	}
			else if ( $priority == "BE" )	{	$mark_val = 256;	}
			else 							{	$mark_val = 256;	}
			
			
			/* connection mark is unsigned int
				Bit1-Bit7 will not be used.
				Bit25-Bit32 will not be used.
				Bit8-Bit16 will be used as connection priority at tc fw classifier.
				Bit17-Bit24 will be used as special flag only.
				Now, Bit17 is used for HTTP base application only.
				When connection is HTTP(dport==80), then this flag will be enable, and it's value will be 0x10000.
				When flag enable, then this packet will not be processed by Turbonat.
				When flag enable, then this packet will be check by some layer7 match, such as Youtube.
				When layer7 matched, then this flag will be erase, then packet can be processed by turbonat.
				Note: for xmark: F indicate clear, 0 indicate keep it
				*/ 
			/* 	0x0000FF80	= 65408, 0x00FFFF80	= 16777088 */
			$mark_cmd		= " -j CONNMARK --set-xmark ".$mark_val."/65408";
			$mark2_cmd		= " -j CONNMARK --set-xmark ".$mark_val."/16777088";

			/* protocol str */
            if        ($proto=="TCP")    { $protocol=" -p tcp"; }
            else if   ($proto=="UDP")    { $protocol=" -p udp"; }
            else if   ($proto=="ICMP")    { $protocol=" -p icmp"; }
            else						 { $proto="ALL"; $protocol=" -p all"; }

			//marco, if proto is all , we need to seperate the rules to tcp and udp instead os -p all
			if($proto=="ALL")
			{
				$proto_is_all=1;	
				$index=2;
			}
			else
			{
				$index=1;
				$proto_is_all=0;
			}
			while($index>0)
			{			
				if($proto_is_all==1)
				{
					if($index==2)
					{
						$protocol=" -p tcp";
					}
					else
					{
						$protocol=" -p udp";
					}		
				}
				$index--;
			
				/* src iptype */
				$iprange = 0;
				$sipstring="";
				$in_iprange = 0;
				$in_dipstring="";
	            if ( $startip != "" && $endip == "" ) 
	            { 
	            	$sipstring=" -s ".$startip; 
	            	$in_dipstring=" -d ".$startip; 
	            }
	            else if ( $startip != "" && $startip == $endip ) 
	            { 
	            	$sipstring=" -s ".$startip; 
	            	$in_dipstring=" -d ".$startip; 
	            }
	            else if ( $startip != "" && $startip != $endip )	
	            { 
	            	$sipstring=" -m iprange --src-range ".$startip."-".$endip; 
	            	$iprange=1;
	            	$in_dipstring=" -m iprange --dst-range ".$startip."-".$endip; 
	            	$in_iprange=1;
	            }
	
				/* dst iptype */
				$dipstring="";
				$in_sipstring="";
	            if ( $dstartip != "" && $dendip == "" ) 
	            { 
	            	$dipstring=" -d ".$dstartip; 
	            	$in_sipstring=" -s ".$dstartip; 
	            }
	            else if ( $dstartip != "" && $dstartip == $dendip ) 
	            { 
	            	$dipstring=" -d ".$dstartip; 
	            	$in_sipstring=" -s ".$dstartip; 
	            }
	            else if ( $dstartip != "" && $dstartip != $dendip )	
	            { 
	            	if ($iprange == 0 )	{$dipstring=" -m iprange --dst-range ".$dstartip."-".$dendip;}
	            	else				{$dipstring=" --dst-range ".$dstartip."-".$dendip; }
	            	if ($in_iprange == 0 )	{$in_sipstring=" -m iprange --src-range ".$dstartip."-".$dendip;}
	            	else				{$in_sipstring=" --src-range ".$dstartip."-".$dendip; }
	            }
					
				$dportstring="";
				$in_sportstring="";
				if ($proto == "TCP" || $proto == "UDP" || $proto == "ALL" )
	            {
					if ( $dporttype == 1 && $dportname != "" )
					{
						if ( $dportname == "YOUTUBE" || 
								$dportname == "HTTP_AUDIO" || 
								$dportname == "HTTP_VIDEO" || 
								$dportname == "HTTP_DOWNLOAD" )	
						{	
							$have_flag_apps=1;
							$have_http_flag_apps=1;	
							$entry_is_http_apps=1; 
						}
						else if ( $dportname == "P2P" )	
						{	
							$have_flag_apps=1;
							$have_p2p_flag_apps=1;	
							$entry_is_p2p_apps=1; 
						}
						else if ( $dportname == "VOICE" )	
						{	
							$have_flag_apps=1;
							$have_voice_flag_apps=1;	
							$entry_is_voice_apps=1; 
						}
						else if ( $dportname == "HTTP" )	
						{	
			            	$dportstring=" --dport 80"; 
			            	$in_sportstring=" --sport 80";
						}
						else if ( $dportname == "FTP" )	
						{	
			            	$dportstring=" --dport 21"; 
			            	$in_sportstring=" --sport 21"; 
						}
					}
					else
					{ 
		                /* gen dst portstring */
			            if ( $dstartport != "" && $dendport == "" ) 
			            { 
			            	$dportstring=" --dport ".$dstartport; 
			            	$in_sportstring=" --sport ".$dstartport; 
			            }
			            else if ( $dstartport != "" && $dstartport == $dendport ) 
			            { 
			            	$dportstring=" --dport ".$dstartport; 
			            	$in_sportstring=" --sport ".$dstartport; 
			            }
		 	            else if ( $dstartport != "" && $dstartport != $dendport )	
		 	            { 
		 	            	$dportstring=" --dport ".$dstartport.":".$dendport; 
		 	            	$in_sportstring=" --sport ".$dstartport.":".$dendport; 
		 	            }
		 	        }
				}
	
				if ( $entry_is_http_apps == 1 )
				{
					if ( $dportname == "YOUTUBE" || 
							$dportname == "HTTP_AUDIO" || 
							$dportname == "HTTP_VIDEO" || 
							$dportname == "HTTP_DOWNLOAD" )	
					{
						if ( $dportname == "YOUTUBE" )	{	$layer7_pattern_name = "youtube";	}
						else if ( $dportname == "HTTP_AUDIO" )	{	$layer7_pattern_name = "httpaudio";	}
						else if ( $dportname == "HTTP_VIDEO" )	{	$layer7_pattern_name = "httpvideo";	}
						else if ( $dportname == "HTTP_DOWNLOAD" )	{	$layer7_pattern_name = "httpdownload";	}
						
						/* 65536 = 0x10000 */
						if ($prefix == "LAN")
						{
							startcmd($ipt_out_add_http_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 65536/65536 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							startcmd($ipt_out_add_http_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/65536 -j RETURN");
						}
						else
						{
							startcmd($ipt_out_add_http_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 65536/65536 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							startcmd($ipt_out_add_http_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/65536 -j RETURN");
						}
					}
				}
				else if ( $entry_is_voice_apps == 1 )
				{
					/* how many VOICE apps will be detected */
					$voice_apps_max = 5;
					$voice_apps_index = 1;
					
					while ( $voice_apps_index <= $voice_apps_max )
					{
						if 		( $voice_apps_index == 1 )	{	$layer7_pattern_name = "jabber";	$protocol = " -p tcp";	}
						else if ( $voice_apps_index == 2 )	{	$layer7_pattern_name = "sip";	$protocol = " -p udp";	}
						else if ( $voice_apps_index == 3 )	{	$layer7_pattern_name = "rtp_tcp";	$protocol = " -p tcp";	}
						else if ( $voice_apps_index == 4 )	{	$layer7_pattern_name = "rtp";	$protocol = " -p udp";	}
						else if ( $voice_apps_index == 5 )	{	$layer7_pattern_name = "rtcp";	$protocol = " -p udp";	}
	
						
						/* 327552 = 0x4FF80, 262144= 0x40000, 16711680 = 0xFF0000 */
						if ($prefix == "LAN")
						{
							//startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 262144/262144 ".$logcmd_original );
							startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 262144/262144 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							//startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/262144 ".$logcmd_match );
							startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/262144 -j RETURN");
							//startcmd($ipt_out_add_voice_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark ! --mark 0/262144 ".$logcmd_unmatch );
						}
						else
						{
							//startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 262144/262144 ".$logcmd_original );
							startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 262144/262144 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
							//startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/262144 ".$logcmd_match );
							startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/262144 -j RETURN");
							//startcmd($ipt_out_add_voice_prefix.$protocol.$sipstring.$dipstring." -m connmark ! --mark 0/262144  ".$logcmd_unmatch );
						}
						$voice_apps_index++;
					}
				}
				else if ( $entry_is_p2p_apps == 1 )
				{
					/* how many P2P apps will be detected */
					$p2p_max = 2;
					$p2p_index = 1;
					
					while ( $p2p_index <= $p2p_max )
					{
						if 		( $p2p_index == 1 )	{	$layer7_pattern_name = "bittorrent";	}
						else if ( $p2p_index == 2 )	{	$layer7_pattern_name = "edonkey";	}
	
						/* 131072 = 0x20000 */
						if ($prefix == "LAN")
						{
							if ( $layer7_pattern_name == "edonkey" )
							{
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark ! --mark 131072/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix." -p tcp ".$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_tcp ".$mark2_cmd);
								startcmd($ipt_out_add_p2p_prefix." -p udp ".$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_udp ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$in_dipstring.$in_sipstring." -m connmark ! --mark 0/131072 ".$logcmd_unmatch );
							}
							else
							{
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$in_dipstring.$in_sipstring." -m connmark ! --mark 0/131072 ".$logcmd_unmatch );
							}
						}
						else
						{
							if ( $layer7_pattern_name == "edonkey" )
							{
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark ! --mark 131072/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix." -p tcp ".$sipstring.$dipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_tcp ".$mark2_cmd);
								startcmd($ipt_out_add_p2p_prefix." -p udp ".$sipstring.$dipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto edonkey_udp ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$sipstring.$dipstring." -m connmark ! --mark 0/131072 ".$logcmd_unmatch );
							}
							else
							{
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 131072/131072 ".$logcmd_original );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 131072/131072 -m layer7 --l7proto ".$layer7_pattern_name." ".$mark2_cmd);
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/131072 ".$logcmd_match );
								startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark --mark 0/131072 -j RETURN");
								//startcmd($ipt_out_add_p2p_prefix.$protocol.$sipstring.$dipstring." -m connmark ! --mark 0/131072  ".$logcmd_unmatch );
							}
						}
						$p2p_index++;
					}
				}
				else
				{
					if ($prefix == "LAN")
					{
						startcmd($ipt_out_add_prefix.$protocol.$in_dipstring.$in_sipstring.$in_sportstring.$mark_cmd);
						startcmd($ipt_out_add_prefix.$protocol.$in_dipstring.$in_sipstring.$in_sportstring." -j RETURN");
					}
					else
					{
						startcmd($ipt_out_add_prefix.$protocol.$sipstring.$dipstring.$dportstring.$mark_cmd);
						startcmd($ipt_out_add_prefix.$protocol.$sipstring.$dipstring.$dportstring." -j RETURN");
					}
				}
		    }//marco
		}
	}

	/* add some command for special flag apps */
	$is_first=1;
	if ($prefix == "LAN")	{	$insert_idx= $bwc_main_chain_lan_rules + 1;		}
	else					{	$insert_idx= $bwc_main_chain_wan_rules + 1;		}
	if ( $have_flag_apps == 1 )
	{
		if ( $have_http_flag_apps == 1 )
		{
			/* careful for command sequence */
			/* for all NEW http base apps come in, give it a flag value(bit17). */
			/* 65536 = 0x10000, 130944= 0x1FF80, 16711680 = 0xFF0000, 65408=0xFF80 */
			startcmd($ipt_out_insert_http_prefix." 1 -m connmark --mark 0/65536 -j RETURN");
			startcmd($ipt_out_insert_http_prefix." 1 -m connmark --mark 0/130944 -j CONNMARK --set-xmark 65536/65536");
			/* Flag bit is set(bit17), and connection have transfer over 2k bytes, then disable the flag. */
			startcmd($ipt_out_add_http_prefix." -m connmark --mark 65536/65536 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes -j CONNMARK --set-xmark 0/65536");
			/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
			/* jump to user-define chain to process all http related apps, such as Youtube */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -p tcp --dport 80 -j FWD.BWC.".$name.".HTTP");
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -p tcp --sport 80 -j FWD.BWC.".$name.".HTTP");
			if ( $is_first == 1 )	
			{	
				/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
				startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
				$insert_idx = $insert_idx +4;	
			}
			else					
			{	
				$insert_idx = $insert_idx +3;	
			}
			$is_first++;
		}

		if ( $have_voice_flag_apps == 1 )
		{
			/* careful for command sequence */
			/* for all NEW VOICE base apps come in, give it a flag value(bit19). */
			/* 327552 = 0x4FF80, 262144= 0x40000, 16711680 = 0xFF0000, 65408=0xFF80 */
			startcmd($ipt_out_insert_voice_prefix." 1 -m connmark --mark 0/262144 -j RETURN");
			startcmd($ipt_out_insert_voice_prefix." 1 -m connmark --mark 0/327552 -j CONNMARK --set-xmark 262144/262144");
			//startcmd($ipt_out_insert_voice_prefix." 1 -m connmark --mark 0/327552 ".$logcmd_new );
			/* Flag bit is set(bit19), and connection have transfer over 2k bytes, then disable the flag. */
			//startcmd($ipt_out_add_voice_prefix." -m connmark --mark 262144/262144 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes ".$logcmd_skip);
			startcmd($ipt_out_add_voice_prefix." -m connmark --mark 262144/262144 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes -j CONNMARK --set-xmark 0/262144");
			/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
			/* jump to user-define chain to process all voice related apps, such as SIP */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -j FWD.BWC.".$name.".VOICE");
			if ( $is_first == 1 )	
			{	
				/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
				startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
				$insert_idx = $insert_idx +3;	
			}
			else					
			{	
				$insert_idx = $insert_idx +2;	
			}
			$is_first++;
		}
		
		if ( $have_p2p_flag_apps == 1 )
		{
			/* careful for command sequence */
			/* 131072 = 0x20000, 196480= 0x2FF80, 16711680 = 0xFF0000, 65408=0xFF80 */
			startcmd($ipt_out_insert_p2p_prefix." 1 -m connmark --mark 0/131072 -j RETURN");
			/* for all NEW P2P base apps come in, give it a flag value(bit18). */
			startcmd($ipt_out_insert_p2p_prefix." 1 -m connmark --mark 0/196480 -j CONNMARK --set-xmark 131072/131072");
			//startcmd($ipt_out_insert_p2p_prefix." 1 -m connmark --mark 0/196480  ".$logcmd_new );
			/* Flag bit is set(bit18), and connection have transfer over 2k bytes, then disable the flag. */
			//startcmd($ipt_out_add_p2p_prefix." -m connmark --mark 131072/131072 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes ".$logcmd_skip);
			startcmd($ipt_out_add_p2p_prefix." -m connmark --mark 131072/131072 -m connbytes --connbytes 2000: --connbytes-dir both --connbytes-mode bytes -j CONNMARK --set-xmark 0/131072");
			/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
			/* jump to user-define chain to process all http related apps, such as Youtube */
			startcmd($ipt_out_insert_prefix." ".$insert_idx." -j FWD.BWC.".$name.".P2P");
			if ( $is_first == 1 )	
			{	
				/* No flag bit be set(bit17-bit24), and have priority mark value(bit8-bit16), then return. */
				startcmd($ipt_out_insert_prefix." ".$insert_idx." -m connmark --mark 0/16711680 -m connmark ! --mark 0/65408 -j RETURN");
				$insert_idx = $insert_idx +3;	
			}
			else					
			{	
				$insert_idx = $insert_idx +2;	
			}
			$is_first++;
		}
	}

	/* Finally, mark all packets are FROM/TO internet and doesn't have connmark(0) , priority is 256 */
	$mark_val = 256;
	$mark_cmd= " -m connmark --mark 0/65408 -j CONNMARK --set-xmark ".$mark_val."/65408";
	startcmd($ipt_out_add_prefix.$mark_cmd);
	
	/* OUTPUT chain: for all traffic that device send out will be treat as the highest priority packet */
	$ipt_output_flush_cmd	= "iptables -t filter -F OUTP.BWC.".$name;
	$ipt_output_add_prefix	= "iptables -t filter -A OUTP.BWC.".$name;

	/* set mark, use iptables/output */
	startcmd($ipt_output_flush_cmd);

	/* OUTPUT chain: for all traffic mark as lowest priority, 
		it means that those traffic will not have rate limit. 
		But, it will lost traffic scheduling feature at the same time. 
		*/
	$mark_val = 128;
	$mark_cmd= " -m connmark --mark 0/65408 -j CONNMARK --set-xmark ".$mark_val."/65408";
	startcmd($ipt_output_add_prefix.$mark_cmd);
}

function bwc_tc_wfq_stop($rtbwcp, $name, $ifname)
{
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	
	$tc_qd_del		= "tc qdisc del dev ".$ifname;

	/* clean all qdisc*/
	stopcmd($tc_qd_del." root 2>/dev/null");

	$prefix = cut($name,0,'-');
	if ($prefix == "LAN")
	{
		/* get LAN-x's phyinfp */
		$lan_phyinfp = PHYINF_getphypath($name);
		if ($lan_phyinfp != "" )
		{	
			$cnt=0;
			/* wifi physical inf */
			foreach($lan_phyinfp."/bridge/port")	{	$cnt++;	}
			foreach($lan_phyinfp."/bridge/port")
			{
				if ($InDeX > $cnt) break;
				$wlan_phyinf_uid = $VaLuE;
	
				if ( $wlan_phyinf_uid != "" )
				{
					$phyinf_name="";
					$phyinf_name = PHYINF_getifname($wlan_phyinf_uid);
					if ( $phyinf_name != "" )
					{
						$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
						
						/* clean all qdisc*/
						stopcmd($tc_qd_del." root 2>/dev/null");
					}				
				}
			}
			
			/* ethernet physical inf */
			foreach("/runtime/phyinf")
			{
				$phyinf_name="";
				if ( query("valid") == 1 &&  query("type") == "eth" && query("uid") == $lan_phyinf."-PHY_1" )
				{
					$phyinf_name = query("name");
				}
				
				if ( $phyinf_name != "" )
				{
					$tc_qd_del		= "tc qdisc del dev ".$phyinf_name;
					
					/* clean all qdisc*/
					stopcmd($tc_qd_del." root 2>/dev/null");
				}
			}
		}
	}

	/* cleann all iptables/forward/subchain rules */
	$ipt_out_flush_cmd	= "iptables -t filter -F FWD.BWC.".$name;
	stopcmd($ipt_out_flush_cmd);
	$ipt_out_flush_http_cmd	= "iptables -t filter -F FWD.BWC.".$name.".HTTP";
	stopcmd($ipt_out_flush_http_cmd);
	$ipt_out_flush_p2p_cmd	= "iptables -t filter -F FWD.BWC.".$name.".P2P";
	stopcmd($ipt_out_flush_p2p_cmd);
	$ipt_out_flush_voice_cmd	= "iptables -t filter -F FWD.BWC.".$name.".VOICE";
	stopcmd($ipt_out_flush_voice_cmd);


	/* cleann all iptables/output/subchain rules */
	$ipt_output_flush_cmd	= "iptables -t filter -F OUTP.BWC.".$name;
	stopcmd($ipt_output_flush_cmd);
}	

function bwc_tc_adb_start($rtbwcp, $name, $ifname)
{
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");

	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	if($trate <= 0 || $trate == "")
	{
		$trate = 1024000;	/* 1000Mbps */
	}

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc. */
	startcmd($tc_qd_add." root handle 1:0 htb default 1");
	
	/* add htb qdisc. */
	startcmd($tc_class_add." parent 1:0 classid 1:1 htb rate ".$trate.$unit." ceil ".$trate.$unit);
	startcmd($tc_qd_add." parent 1:1 handle 10:0 sfq perturb 10");
	
	/* add filter */
	$prefix = cut($name,0,'-');
	if($prefix == "LAN")				{$hash_function = "dst";}	
	else if($prefix == "WAN")		{$hash_function = "nfct-src";}
	startcmd($tc_filter_add." parent 10: protocol all handle 1 flow hash keys ".$hash_function." divisor 1024");

	$bwc_main_chain_lan_rules = 0;
	$bwc_main_chain_wan_rules = 0;

	/* At FORWARD chain, try to filter some packets that from another LAN interface. 
		It imply that packets from WAN. */
	if ($prefix == "LAN")
	{
		$mode = query("/device/router/mode"); 
		if ($mode!="1W1L")
		{
			foreach ("/inf")
			{
				$uid = query("uid");
				$active = query("active");
				$inf_prefix = cut($uid,0,'-');
				if ( $inf_prefix == "LAN" && $name != $uid && $active == "1" )
				{
					$infstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $uid, 0);
					$addrtype = query($infstsp."/inet/addrtype");
					if ($addrtype=="ipv4" || $addrtype=="ppp4")
					{
						$lan_ifname = PHYINF_getruntimeifname($uid);
						if ( $lan_ifname != "" && $lan_ifname != $ifname )
						{
							startcmd("iptables -t filter -A FWD.BWC.".$name." -i ".$lan_ifname." -j RETURN");
							$bwc_main_chain_lan_rules++;
						}
					}
				}
			}
		}
	}

}

function bwc_tc_adb_stop($rtbwcp, $name, $ifname)
{
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	
	/* clean all qdisc*/
	stopcmd("tc qdisc del dev ".$ifname." root 2>/dev/null");
}

function bwc_tc_spq_2013gui_start($rtbwcp, $name, $ifname)
{
	//#[$rtbwcp=/bwc:2/entry:2 $name=LAN-1 $ifname=br0]   
	startcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	stopcmd("#[rtbwcp=".$rtbwcp." name=".$name." ifname=".$ifname."]");
	
	$tc_qd_add		= "tc qdisc add dev ".$ifname;
	$tc_qd_del		= "tc qdisc del dev ".$ifname;
	$tc_class_add	= "tc class add dev ".$ifname;
	$tc_class_del	= "tc class del dev ".$ifname;
	$tc_filter_add	= "tc filter add dev ".$ifname;
	$tc_filter_del	= "tc filter del dev ".$ifname;
	$ipt_add_prefix	= "iptables -t filter -A FWD.BWC.".$name;
	$ipt_out_add_prefix	= "iptables -t filter -A FWD.BWC.".$name;

	$unit = "kbit";

	/* trate: total rate (bandwidth) */
	$trate = query($rtbwcp."/bandwidth");
	if($name=="LAN-1")
	{
		if($trate < 200) $trate=200;
	}	
	$trate = $trate.$unit;

	/* TC fw policy will be:
		0: check skb->mark only. This is linux native default.
		1: check connection->mark only.
		2: Prefer connection->mark, if connection->mark==0, then check skb->mark later.
		3: Prefer skb->mark, if skb->mark==0, then check connection->mark later.
		4: check connection->mark only, and speed up TCP small packets.
	  */
	startcmd("echo 4 > /proc/sche/fw_policy");

	/* clean all qdisc*/
	startcmd($tc_qd_del." root 2>/dev/null");

	/* add root qdisc */
	startcmd($tc_qd_add." root handle 66:0 prio bands 2 priomap 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0");
	startcmd($tc_qd_add." parent 66:1 handle 1:0 htb default 1"); //rate queue
	startcmd($tc_qd_add." parent 66:2 handle 2:0 sfq perturb 10"); //rateless queue, for packets to device
		
	/* limit total rate */
	$trate = "97280".$unit; //FIXME: temporary limit to 95M
	startcmd($tc_class_add." parent 1:0 classid 1:1 htb rate ".$trate." ceil ".$trate);

	/* add qDisc PRIO */
	startcmd($tc_qd_add." parent 1:1 handle 20:0 prio bands 4 priomap 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3"); //default priority: best effort
	startcmd($tc_qd_add." parent 20:1 handle 1000:0 sfq limit 127 perturb 10");
	startcmd($tc_qd_add." parent 20:2 handle 2000:0 sfq limit 127 perturb 10");
	startcmd($tc_qd_add." parent 20:3 handle 3000:0 sfq limit 127 perturb 10");			
	startcmd($tc_qd_add." parent 20:4 handle 4000:0 sfq limit 127 perturb 10");

	/* add filter */
	startcmd($tc_filter_add." parent 20: protocol all prio 1 handle 0x100 fw classid 20:1"); //Highest
	startcmd($tc_filter_add." parent 20: protocol all prio 2 handle 0x200 fw classid 20:2"); //Higher
	startcmd($tc_filter_add." parent 20: protocol all prio 3 handle 0x300 fw classid 20:3"); //Normal
	startcmd($tc_filter_add." parent 20: protocol all prio 4 handle 0x400 fw classid 20:4"); //Best Effort

	/* set mark, use iptables/forward */
	startcmd("iptables -t filter -F FWD.BWC.".$name);
	
	/* we don't want to limit the rate for packets to router, set conntrack mark 0xFF00 */
	if($name=="LAN-1")
	{
		$path_run_inf_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0);
		$lanip = get("",$path_run_inf_lan1."/inet/ipv4/ipaddr");	
		startcmd($tc_filter_add." parent 66:0 protocol all prio 1 handle 0xFF00 fw classid 66:2");
		startcmd("iptables -t nat -I PRE.LAN-1 -d ".$lanip." -j CONNMARK --set-xmark 0xFF00/0xFF00");
		stopcmd("iptables -t nat -D PRE.LAN-1 -d ".$lanip." -j CONNMARK --set-xmark 0xFF00/0xFF00");
	}
	
	$bwc_main_chain_lan_rules = 0;

	/* At FORWARD chain, try to filter some packets that from another LAN interface. 
		It imply that packets from WAN. */
	$prefix = cut($name,0,'-');		
	if ($prefix == "LAN")
	{
		$mode = query("/device/router/mode"); 
		if ($mode!="1W1L")
		{
			foreach ("/inf")
			{
				$uid = query("uid");
				$active = query("active");
				$inf_prefix = cut($uid,0,'-');
				if ( $inf_prefix == "LAN" && $name != $uid && $active == "1" )
				{
					$infstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $uid, 0);
					$addrtype = query($infstsp."/inet/addrtype");
					if ($addrtype=="ipv4" || $addrtype=="ppp4")
					{
						$lan_ifname = PHYINF_getruntimeifname($uid);
						if ( $lan_ifname != "" && $lan_ifname != $ifname )
						{
							startcmd($ipt_out_add_prefix." -i ".$lan_ifname." -j RETURN");
							$bwc_main_chain_lan_rules++;
						}
					}
				}
			}
		}
	}

	foreach($rtbwcp."/rules/entry")
	{
		if (query("enable")=="1")
		{
			$bwcf_name = query("bwcf");
			$bwcfp = XNODE_getpathbytarget("/bwc/bwcf", "entry", "uid", $bwcf_name, 0);
			if($bwcfp == "" ) { continue; }
			$bwcqd_name = query("bwcqd");
			$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd", "entry", "uid", $bwcqd_name, 0);
			if($bwcqdp == "" ) { continue; }	
			$startip = query($bwcfp."/ipv4/start");
			$endip = query($bwcfp."/ipv4/end");

			/* priority */
			if		 (query($bwcqdp."/priority") == "VO") { $hex_mark_base = "0x100"; }          //Highest    
			else if(query($bwcqdp."/priority") == "VI") { $hex_mark_base = "0x200"; }          //Higher     
			else if(query($bwcqdp."/priority") == "BG") { $hex_mark_base = "0x300"; }          //Normal     
			else if(query($bwcqdp."/priority") == "BE") { $hex_mark_base = "0x400"; } 				 //Best Effort
			else { startcmd("echo bwcsvcs.php: Unknown Traffic Control priority...ERROR!!!"); }		
			
			if( $name == "WAN-1" )   /* Upload bandwidth control */
			{
				startcmd("#=========================================================================================");
				startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j CONNMARK --set-xmark ".$hex_mark_base."/0xFF00");
				startcmd($ipt_add_prefix." -m iprange --src-range ".$startip."-".$endip." -j RETURN");				
			}
			if( $name == "LAN-1" ) /* Download bandwidth control */
			{
				startcmd("#=========================================================================================");
				startcmd($ipt_add_prefix." -m iprange --dst-range ".$startip."-".$endip." -j CONNMARK --set-xmark ".$hex_mark_base."/0xFF00");
				startcmd($ipt_add_prefix." -m iprange --dst-range ".$startip."-".$endip." -j RETURN");	
			}
		}
	}
}

function bwc_tc_spq_2013gui_stop($rtbwcp, $name, $ifname)
{
	/* clean all qdisc*/
	stopcmd("tc qdisc del dev ".$ifname." root 2>/dev/null");

	/* cleann all iptables/forward/subchain rules */
	stopcmd("iptables -t filter -F FWD.BWC.".$name);
}

function check_qos_version()
	{
	if(query("/bwc:1/entry:1/enable")=="1" && query("/bwc:1/entry:2/enable")=="1") $old_qos_enable="1";
	if(query("/bwc:2/entry:1/enable")=="1" && query("/bwc:2/entry:2/enable")=="1") $new_qos_enable="1";
	if($old_qos_enable=="1" && $new_qos_enable=="1")
		{
		TRACE_error("check_qos_version: error, enable 2 traffic control system at same time!");
		return;
			}
	else if($old_qos_enable=="1") return 1;
	else if($new_qos_enable=="1") return 2;
	else													return 0;
}

function bwc_setup($name)
{
	if (query("/runtime/bwc/rules/count")=="") set("/runtime/bwc/rules/count", 0);
	
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
	if ($infp=="" || $stsp=="")
	{
		SHELL_info($_GLOBALS["START"], "bwc_setup: (".$name.") no interface.");
		SHELL_info($_GLOBALS["STOP"],  "bwc_setup: (".$name.") no interface.");
		bwc_error("9");
		return;
	}
	$bwc_profile_name = query($infp."/bwc");
	if ($bwc_profile_name=="")
	{
		SHELL_info($_GLOBALS["START"], "bwc_setup: (".$name.") no bwc_profile_name, service stop.");
		SHELL_info($_GLOBALS["STOP"],  "bwc_setup: (".$name.") no bwc_profile_name, service stop.");
		bwc_error("8");
		return;
	}
	$bwcid = check_qos_version();
	if ($bwcid=="")
	{
		SHELL_info($_GLOBALS["START"], "bwc_setup: (".$name.") error qos version conflict, service stop.");
		SHELL_info($_GLOBALS["STOP"],  "bwc_setup: (".$name.") error qos version conflict, service stop.");
		return;
	}
	$bwcp = XNODE_getpathbytarget("/bwc:".$bwcid, "entry", "uid", $bwc_profile_name, 0);
	if ($bwcp=="")
	{
		SHELL_info($_GLOBALS["START"], "bwc_setup: (".$name.") bwc node/profile not exist, service stop.");
		SHELL_info($_GLOBALS["STOP"],  "bwc_setup: (".$name.") bwc node/profile not exist, service stop.");
		bwc_error("8");
		return;
	}

	/* fill in runtime/inf:$InDeX/bwc */
	copy_bwc_entry($bwcp, $stsp);

	/* clean runtime node */
	stopcmd("sh /etc/scripts/delpathbytarget.sh "."/runtime "."inf "."uid ".$name." bwc");

	$ifname = PHYINF_getruntimeifname($name);

	$bwcp_flag = query($bwcp."/flag");
	
	if($bwcp_flag == "BC")
	{
		/* before everything start, trigger other services*/
		service_pre_trigger();	

		/* ex: $name=> WAN-1, $ifname=>eth2.2 */
		bwc_bc_start($stsp."/bwc", $name, $ifname);
		bwc_bc_stop($stsp."/bwc", $name, $ifname);

		/* everything is done, trigger other services*/
		service_post_trigger();	
    }
	else if($bwcp_flag == "TC")
	{
    	startcmd("echo ".$name." Start Traffic Control system ...");
    	stopcmd("echo ".$name." Stop Traffic Control system ...");
		if( query($bwcp."/enable") == "1")
   	 	{
			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			bwc_tc_start($bwcp, $name, $ifname);
			bwc_tc_stop($bwcp, $name, $ifname);
		}
		else
		{
			startcmd("echo ".$name." Traffic Control is disabled.\n"); 
			stopcmd("echo ".$name." Traffic Control is disabled.\n"); 
			return; 
    	}
    	
	}
	else if($bwcp_flag == "AQC")
	{
    	startcmd("echo ".$name." Start Auto Qos Control system ...\n");
    	stopcmd("echo ".$name." Stop Auto Qos Control system ...\n");
		if( query($bwcp."/enable") == "1")
   	 	{
			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			aqc_tc_start($bwcp, $name, $ifname);
			aqc_tc_stop($bwcp, $name, $ifname);
		}
		else
		{
			startcmd("echo ".$name." Auto Qos Control is disabled.\n"); 
			stopcmd("echo ".$name." Auto Qos Control is disabled.\n"); 
			return; 
    	}

	}
	else if($bwcp_flag == "TC_WFQ")
	{
    	startcmd("echo ".$name." Start Traffic Control system ...");
    	stopcmd("echo ".$name." Stop Traffic Control system ...");
		if( query($bwcp."/enable") == "1")
   	 	{
			// Stop ralink HW_NAT 
			startcmd("service HW_NAT stop");

			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			bwc_tc_wfq_start($bwcp, $name, $ifname);
			bwc_tc_wfq_stop($bwcp, $name, $ifname);
			
			// Restart ralink HW_NAT 
			stopcmd("service HW_NAT restart");

			setup_conntrack_max();

			/* when qos enable, smaller netdev_budget and backlog */
			/*
			startcmd("echo 64 > /proc/sys/net/core/netdev_max_backlog");
			startcmd("echo 16 > /proc/sys/net/core/netdev_budget");
			stopcmd("echo 200 > /proc/sys/net/core/netdev_max_backlog");
			stopcmd("echo 32 > /proc/sys/net/core/netdev_budget");
			*/
		}
		else
		{
			startcmd("echo ".$name." Traffic Control is disabled.\n"); 
			stopcmd("echo ".$name." Traffic Control is disabled.\n"); 
			return; 
    	}
    	
	}
	else if($bwcp_flag == "TC_SPQ")
	{
    	startcmd("echo ".$name." Start Traffic Control system ...");
    	stopcmd("echo ".$name." Stop Traffic Control system ...");
		if( query($bwcp."/enable") == "1")
   	 	{
			// Stop ralink HW_NAT 
			startcmd("service HW_NAT stop");

			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			bwc_tc_spq_start($bwcp, $name, $ifname);
			bwc_tc_spq_stop($bwcp, $name, $ifname);
			
			// Restart ralink HW_NAT 
			stopcmd("service HW_NAT restart");
			
			setup_conntrack_max();
	
			/* when qos enable, smaller netdev_budget and backlog */
			/*
			startcmd("echo 64 > /proc/sys/net/core/netdev_max_backlog");
			startcmd("echo 16 > /proc/sys/net/core/netdev_budget");
			stopcmd("echo 200 > /proc/sys/net/core/netdev_max_backlog");
			stopcmd("echo 32 > /proc/sys/net/core/netdev_budget");
			*/
				}	
		else
				{
			startcmd("echo ".$name." Traffic Control is disabled.\n"); 
			stopcmd("echo ".$name." Traffic Control is disabled.\n"); 
			return; 
				}			
    	
	}
	else if($bwcp_flag == "TC_CONNMARK")
	{
		/* "TC_CONNMARK" is similar to "TC" but use conntrack mark so it can coexist with fastnat.
			 In this option, allow user to setup N rules, each rule contains [client ip] with [downlink rate/ceil] 
			 either [uplink ceil], and each rule will correspond to one tc class, therefor we have N+1 class at the
			 end(+1 default class), packets enter the classes according to conntrack mark, Sammy */
			 
    startcmd("echo ".$name." Start Traffic Control system ...");
    stopcmd("echo ".$name." Stop Traffic Control system ...");
    
		if( query($bwcp."/enable") == "1")
				{
			startcmd("service HW_NAT stop"); // Stop ralink HW_NAT
   		
			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			bwc_tc_connmark_start($bwcp, $name, $ifname);
			bwc_tc_connmark_stop($bwcp, $name, $ifname);
			
			stopcmd("service HW_NAT restart"); // Restart ralink HW_NAT
			
			setup_conntrack_max();
				}	
		else
				{
			startcmd("echo ".$name." Traffic Control is disabled.\n"); 
			stopcmd("echo ".$name." Traffic Control is disabled.\n"); 
			return; 
				}			
	}	
	else if($bwcp_flag == "TC_ADB")
	{
		/* "TC_ADB" automatic distribute bandwidth to N clients,
			 each client will has bandwidth equal to trate/N when they send/receive packets,
			 this method use sfq + external filter, make sure enable relative kernel config 
			 before use it:	Networking options--->QoS and/or fair queueing--->Flow classifier, Sammy */
		
		startcmd("echo ".$name." Start Automatic Distribute Bandwidth ...\n");
		stopcmd("echo ".$name." Stop Automatic Distribute Bandwidth ...\n");		
		if( query($bwcp."/enable") == "1")
   	{
			// Stop ralink HW_NAT 
			startcmd("service HW_NAT stop");

			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			bwc_tc_adb_start($bwcp, $name, $ifname);
			bwc_tc_adb_stop($bwcp, $name, $ifname);
	
			// Restart ralink HW_NAT 
			stopcmd("service HW_NAT restart");

			setup_conntrack_max();
		}
		else
		{
			startcmd("echo ".$name." Automatic Distribute Bandwidth is disabled.\n"); 
			stopcmd("echo ".$name." Automatic Distribute Bandwidth is disabled.\n"); 
			return; 
    	}
	}
	else if($bwcp_flag == "TC_SPQ_2013GUI")
	{
		/* "TC_SPQ_2013GUI" is a strict priority queue construct by PRIO and shaping by HTB.
			 In HTB, limit total rate to 95M temporary, and in PRIO, has 4 different priority bands.
			 After rate limitation, each client allocate to one of the bands according to UI settings.
			 Priority from high to low is: Highest, Higher, Normal and Best Effort,
			 default priority is Best Effort, and UI has only Highest, Higher, Normal settings, Sammy
			 
			Figure:
			 
							[66:0 PRIO]
							 	 /  	\
							66:1 	  66:2
							 |			  |
					[1:0 HTB]  [2:0 sfq]
							 |
							1:1	 
						rate=95M
						   |
					 [20:0 PRIO]
				 /    |		\    \
			20:1	20:2  20:3 20:4 
		*/
			 
    startcmd("echo ".$name." Start Traffic Control system: TC_SPQ_2013GUI ...");
    stopcmd("echo ".$name." Stop Traffic Control system: TC_SPQ_2013GUI ...");
    
		if( query($bwcp."/enable") == "1")
		{
			startcmd("service HW_NAT stop"); // Stop ralink HW_NAT
   		
			/* ex: $name=> WAN-1, $ifname=>ppp0 */
			bwc_tc_spq_2013gui_start($bwcp, $name, $ifname);
			bwc_tc_spq_2013gui_stop($bwcp, $name, $ifname);
			
			stopcmd("service HW_NAT restart"); // Restart ralink HW_NAT
			
			setup_conntrack_max();
		}	
		else
		{
			startcmd("echo ".$name." Traffic Control TC_SPQ_2013GUI is disabled.\n"); 
			stopcmd("echo ".$name." Traffic Control TC_SPQ_2013GUI is disabled.\n"); 
			return; 
		}			
	}	
	else
	{
		SHELL_info($_GLOBALS["START"], "bwc_setup: (".$name.") bwc unknown flag, service stop.");
		SHELL_info($_GLOBALS["STOP"],  "bwc_setup: (".$name.") bwc unknown flag, service stop.");
		bwc_error("7");
		return;
	}
}

?>
