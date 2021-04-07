<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

function IP6T_flushall($S)
{
	/* flush default chains */
	fwrite("a",$S, "ip6tables -F; ip6tables -F -t mangle; ");
	/* delete user-defined chains */
	fwrite("a",$S, "ip6tables -X; ip6tables -X -t mangle; ");
	/* set default policy */
	fwrite("a",$S, "ip6tables -P INPUT ACCEPT; ip6tables -P OUTPUT ACCEPT; ip6tables -P FORWARD ACCEPT; ");
}

function IP6T_newchain($S,$tbl,$name)
{
	if ($tbl=="")	fwrite("a",$S, "ip6tables -N ".$name."\n");
	else			fwrite("a",$S, "ip6tables -t ".$tbl." -N ".$name."\n");
}

function IP6T_saverun($S,$script)		{ fwrite("a",$S, "[ -f ".$script." ] && ".$script."\n"); }
function IP6T_setfile($S,$file,$value)	{ fwrite("a",$S, "echo \"".$value."\" > ".$file."\n"); }
function IP6T_killall($S,$app)			{ fwrite("a",$S, "killall ".$app."\n"); }

function IP6T_build_time_command($uid)
{
	$sch = XNODE_getpathbytarget("/schedule", "entry", "uid", $uid, 0);
	if ($sch == "") return "";

	$days   = XNODE_getscheduledays($sch);
	$start  = query($sch."/start");
	$end    = query($sch."/end");
	if ($start=="" || $end=="" || $days=="") return "";
	return "-m time --timestart ".$start." --timestop ".$end." --days ".$days;
}

function IP6T_scan_lan()
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
			if ($addrtype == "ipv6" && query($path."/inet/ipv6/valid")==1)
			{
				$ipaddr = query($path."/inet/ipv6/ipaddr");
				$mask	= query($path."/inet/ipv6/mask");
				XNODE_set_var($name.".IPADDR", $ipaddr);
				XNODE_set_var($name.".MASK", $mask);
			}
		}
		$i++;
	}
}

?>
