<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

function IPT_flushall($S)
{
	/* flush default chains */
	fwrite("a",$S, "iptables -F; iptables -F -t nat; iptables -F -t mangle; ");
	/* delete user-defined chains */
	fwrite("a",$S, "iptables -X; iptables -X -t nat; iptables -X -t mangle; ");
	/* set default policy */
	fwrite("a",$S, "iptables -P INPUT ACCEPT; iptables -P OUTPUT ACCEPT; iptables -P FORWARD ACCEPT; ");
	fwrite("a",$S, "iptables -t nat -P PREROUTING ACCEPT; iptables -t nat -P POSTROUTING ACCEPT\n");
}

function IPT_newchain($S,$tbl,$name)
{
	if ($tbl=="")	fwrite("a",$S, "iptables -N ".$name."\n");
	else			fwrite("a",$S, "iptables -t ".$tbl." -N ".$name."\n");
}

function IPT_saverun($S,$script)		{ fwrite("a",$S, "[ -f ".$script." ] && ".$script."\n"); }
function IPT_setfile($S,$file,$value)	{ fwrite("a",$S, "echo \"".$value."\" > ".$file."\n"); }
function IPT_killall($S,$app)			{ fwrite("a",$S, "killall ".$app."\n"); }

function IPT_build_time_command($uid)
{
	$sch = XNODE_getpathbytarget("/schedule", "entry", "uid", $uid, 0);
	if ($sch == "") return "";

	$days   = XNODE_getscheduledays($sch);
	$start  = query($sch."/start");
	$end    = query($sch."/end");
	if ($start=="" || $end=="" || $days=="") return "";
	return "-m time --timestart ".$start." --timestop ".$end." --days ".$days;
}

function IPT_scan_lan()
{
	$count = 0;
	foreach ("/runtime/inf")	$count++;

	$i=1;
	while ($i<=$count)
	{
		$name = "LAN-".$i;
		XNODE_del_var($name.".IPADDR");
		XNODE_del_var($name.".MASK");

		$path = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
		if ($path != "")
		{
			$addrtype = query($path."/inet/addrtype");
			if ($addrtype == "ipv4" && query($path."/inet/ipv4/valid")==1)
			{
				$ipaddr = query($path."/inet/ipv4/ipaddr");
				$mask	= query($path."/inet/ipv4/mask");
				XNODE_set_var($name.".IPADDR", $ipaddr);
				XNODE_set_var($name.".MASK", $mask);
			}
		}
		$i++;
	}
}

function IPT_build_inbound_filter($start_path)
{
	foreach ("/acl/inbfilter/entry")
	{
		$uid=query("uid");
		$action=query("act");
		$used="0";
		$IN_VSVR="0";
		$IN_PFWD="0";
		foreach ("/nat/entry/virtualserver/entry")
		{
			if($uid == query("inbfilter"))
			{
				$used="1";
				$IN_VSVR="1";
				break;
			}
		}
		if($IN_VSVR=="0")
		{
			foreach ("/nat/entry/portforward/entry")
			{
				if($uid == query("inbfilter"))
				{
					$used="1";
					$IN_PFWD="1";
					break;
				}
			}
		}
		if($IN_VSVR=="0" && $IN_PFWD=="0")
		{
			foreach ("/inf")
			{
				if($uid == query("inbfilter"))
				{
					$used="1";
					break;
				}
			}
		}
		//$logcmd = " -j LOG --log-level info --log-prefix 'DRP:003:'";
		$logcmd = " -j LOG --log-level notice --log-prefix 'DRP:009:'";
		if($used=="1")
		{
			$inbf = cut($uid, 1, "-");
			fwrite("a",$start_path, "iptables -t nat -N CK_INBOUND".$inbf."\n");
			fwrite("a",$start_path, "iptables -t nat -F CK_INBOUND".$inbf."\n");
			if($action=="allow")
			{
				//log for INBOUND FILTER
				fwrite("a",$start_path, "iptables -t nat -A CK_INBOUND".$inbf." ".$logcmd."\n");
				fwrite("a",$start_path, "iptables -t nat -A CK_INBOUND".$inbf." -j DROP "."\n");
			}
			else	fwrite("a",$start_path, "iptables -t nat -A CK_INBOUND".$inbf." -j RETURN "."\n");
			foreach ("iprange/entry")
			{
				if(query("enable")=="1")
				{
					$iprange = query("startip")."-".query("endip");
					if($action=="allow")	fwrite("a",$start_path, "iptables -t nat -I CK_INBOUND".$inbf." -m iprange --src-range ".$iprange." -j RETURN "."\n");
					else
					{
						fwrite("a",$start_path, "iptables -t nat -I CK_INBOUND".$inbf." -m iprange --src-range ".$iprange." -j DROP "."\n");
						fwrite("a",$start_path, "iptables -t nat -I CK_INBOUND".$inbf." -m iprange --src-range ".$iprange." ".$logcmd."\n");
					}
				}
			}
		}
	}
}

function IPT_fwrite_schedule($act, $path, $iptcmd, $sch_uid)
{
	$sch_path = XNODE_getpathbytarget("/schedule", "entry", "uid", $sch_uid, 0);
	foreach($sch_path."/entry")
	{
		$day = get("", "date");
		if($day == 1)		{$day = "Mon";}
		else if($day == 2)	{$day = "Tue";}
		else if($day == 3)	{$day = "Wed";}
		else if($day == 4)	{$day = "Thu";}
		else if($day == 5)	{$day = "Fri";}
		else if($day == 6)	{$day = "Sat";}
		else if($day == 7)	{$day = "Sun";}
		$start = get("", "start");
		$end = get("", "end");

		//If the end time is 24:00, it means the last minute of the day is included. Discuss with D-Link Timmy.
		if($end=="24:00") {$end = "0:00";}

		fwrite($act, $path, $iptcmd." -m time --timestart ".$start." --timestop ".$end." --days ".$day."\n");
	}
}
?>
