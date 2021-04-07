<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";

function addwifienhance($ifname, $infp)
{
	$phyinf = query($infp."/phyinf");
	$brname = query($infp."/devnam");
	if($brname=="") return;
	if($phyinf=="") return;
	
	$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	if ($phyp=="") return;
	foreach ($phyp."/bridge/port") 
	{
		if ($VaLuE!="")
		{
			$s = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $VaLuE, 0);
			$wlaninf = query($s."/name");
			if($wlaninf=="") continue;
			fwrite("a",$_GLOBALS["START"],	'echo "enable_wl_enhance '.$wlaninf.'" > /proc/alpha/multicast_'.$brname.'\n');
			fwrite("a",$_GLOBALS["STOP"],	'echo "disable_wl_enhance '.$wlaninf.'" > /proc/alpha/multicast_'.$brname.'\n');
		}
	}
}

$cfile	= "/var/run/igmpproxy.conf";
$sfile	= "/etc/scripts/igmpproxy_helper.sh";
$iproxy = query("/device/multicast/igmpproxy");
$we		= query("/device/multicast/wifienhance");
$layout = query("/runtime/device/layout");

$mldcfile	= "/var/run/mldproxy.conf";
$mldsfile	= "/etc/scripts/mldproxy_helper.sh";
$mldproxy   = query("/device/multicast/mldproxy");
$we6		= query("/device/multicast/wifienhance6");

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
fwrite("w",$cfile, "");
fwrite("w",$mldcfile, "");

if ($we == "1" || $we6 == "1")
{
	if ($layout == "router")
	{
		$i = 1;
		while ($i>0)
		{
			$ifname = "LAN-".$i;
			$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);
			if($p=="")	{ break; }
			addwifienhance($ifname, $p);
			$i++;
		}
	}
	else
	{
		$ifname = "BRIDGE-1";
		$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);
		if($p!="")	
		{
			addwifienhance($ifname, $p);
			fwrite("a", $cfile, $phyinf." downstream 1 0\n");
		}
		else { TRACE_error(" Addwifienhance failed. Can't find ".$ifname); }
	}
}

if ($layout=="router" && $mldproxy=="1")
{
	fwrite("a", $START, "mldproxy -c ".$mldcfile." -s ".$mldsfile." &\n");
	fwrite("a",$STOP,
			'killall mldproxy\n'.
			'rm -f '.$mldcfile.'\n'
		  );
	$i = 1;
	while ($i>0)
	{
		$ifname = "WAN-".$i;
		$infp = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if($infp =="") break;
		$inet = query($infp."/inet");
		$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
		$addrtype = query($inetp."/addrtype");
		if($addrtype!="ipv6") { $i++; continue; }
		$ipv6mode = query($inetp."/".$addrtype."/mode");
		if($ipv6mode == "LL") { $i++; continue; }
		$phyinf = PHYINF_getruntimeifname($ifname);
		if ($phyinf=="") break;
		$rinfp = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);

		$addrtype = query($rinfp."/inet/addrtype");
		if($addrtype=="ipv6" || $addrtype=="ppp6" || $addrtype=="ppp10"){ fwrite("a",$mldcfile, $phyinf." upstream 1 0\n");}
		$i++;
	}
	$i = 1;
	while ($i>0)
	{
		$ifname = "LAN-".$i;
		$infp = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if($infp =="") break;
		$inet = query($infp."/inet");
		$active = query($infp."/active");
		$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
		if($active!="1") { $i++; continue; }
		if($inetp!="")
		{
			$addrtype = query($inetp."/addrtype");
			if($addrtype!="ipv6") { $i++; continue; }
			$ipv6mode = query($inetp."/".$addrtype."/mode");
			if($ipv6mode == "LL") { $i++; continue; }
		}

		$phyinf = PHYINF_getruntimeifname($ifname);
		if ($phyinf=="") break;
		if ($phyinf!="br0")
		{
			$i++;
			continue;
		}
		$rinfp = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);

		$addrtype = query($rinfp."/inet/addrtype");
		if($addrtype=="ipv6"){ fwrite("a", $mldcfile, $phyinf." downstream 1 0\n");}

		$i++;
	}

	if($iproxy != "1")
	{
		//igmp_snoop, hendry
		fwrite("a", $START, 'echo "enable_switch_snoop" > /proc/alpha/multicast_br0\n');
		fwrite("a", $STOP, 'echo "disable_switch_snoop" > /proc/alpha/multicast_br0\n');		
		fwrite("a",$STOP, "sleep 2\n");
	}
}

if ($layout=="router" && $iproxy=="1")
{
	fwrite("a", $START, "igmpproxy -c ".$cfile." -s ".$sfile." &\n");
	fwrite("a",$STOP,
			'killall igmpproxy\n'.
			'rm -f '.$cfile.'\n'
		  );
	$i = 1;
	while ($i>0)
	{
		$ifname = "WAN-".$i;
		$phyinf = PHYINF_getruntimeifname($ifname);
		if ($phyinf=="") break;
		$rinfp = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);
		$addrtype = query($rinfp."/inet/addrtype");
		if($addrtype=="ipv4" || $addrtype=="ppp4" || $addrtype=="ppp10"){ fwrite("a",$cfile, $phyinf." upstream 1 0\n");}
		$i++;
	}
	$i = 1;
	while ($i>0)
	{
		$ifname = "LAN-".$i;
		$phyinf = PHYINF_getruntimeifname($ifname);
		if ($phyinf=="") break;
		if ($phyinf!="br0")
		{
			$i++;
			continue;
		}
		$rinfp = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);
		$addrtype = query($rinfp."/inet/addrtype");
		if($addrtype=="ipv4"){ fwrite("a", $cfile, $phyinf." downstream 1 0\n");}
		$i++;
	}
	$i = 1;

	while ($i>0)
	{
		$waninf = "WAN-".$i;
		$path = XNODE_getpathbytarget("/runtime", "inf", "uid", $waninf, 0);
		if ($path == "") break;
		fwrite("a",$START,"service IPT.".$waninf." restart\n");
		fwrite('a', $STOP,"service IPT.".$waninf." restart\n");
		$i++;
	}
	
	//igmp_snoop, hendry
	fwrite("a", $START, 'echo "enable_switch_snoop" > /proc/alpha/multicast_br0\n');
	fwrite("a", $STOP, 'echo "disable_switch_snoop" > /proc/alpha/multicast_br0\n');		
	fwrite("a",$STOP, "sleep 2\n");
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
