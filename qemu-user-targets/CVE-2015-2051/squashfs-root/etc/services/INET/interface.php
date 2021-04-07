<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function startcmd($cmd)	{ fwrite(a,$_GLOBALS["START"], $cmd."\n"); }
function stopcmd($cmd)	{ fwrite(a,$_GLOBALS["STOP"],  $cmd."\n"); }
function error($errno)	{ startcmd("exit ".$errno); stopcmd("exit ".$errno); }

//Broadcom's CTF module has problems sometimes,
//We use this to disable CTF module.
function suppress_ctf_acceleration()
{
	startcmd('if [ -f /proc/net/ipct_hndctf ]; then');
	startcmd('    echo 0 > /proc/net/ipct_hndctf');
	startcmd('fi');

	stopcmd('if [ -f /proc/net/ipct_hndctf ]; then');
	stopcmd('    echo 1 > /proc/net/ipct_hndctf');
	stopcmd('fi');
}

function ifsetup($name)
{
	/* Get the interface */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	if ($infp == "")
	{
		SHELL_info($_GLOBALS["START"], "ifsetup: (".$name.") no interface.");
		SHELL_info($_GLOBALS["STOP"],  "ifsetup: (".$name.") no interface.");
		error("9");
		return;
	}

	/* Is this interface active ? */
	$disable= query($infp."/disable");
	$active	= query($infp."/active");
	$inet	= query($infp."/inet");
	$phyinf	= query($infp."/phyinf");
	$upper	= query($infp."/upperlayer");
	$lower	= query($infp."/lowerlayer");
	/*if ($disable==1 || $active!=1 || $inet=="")*/
	if ($disable==1 || $active!=1)
	{
		SHELL_info($_GLOBALS["START"], "ifsetup: (".$name.") not active.");
		SHELL_info($_GLOBALS["STOP"],  "ifsetup: (".$name.") not active.");
		error("8");
		return;
	}
	/* Get the physical interface */
	if ($phyinf == "")
	{
		SHELL_info($_GLOBALS["START"], "ifsetup: (".$name.") no phyinf.");
		SHELL_info($_GLOBALS["STOP"],  "ifsetup: (".$name.") no phyinf.");
		error("9");
		return;
	}

	if ($upper!="")
	{
		SHELL_info($_GLOBALS["START"], "ifsetup: (".$name.") has upperlayer.");
		SHELL_info($_GLOBALS["STOP"],  "ifsetup: (".$name.") has upperlayer.");
		error("9");
		return;
	}

	/*+++, Get devname/devnum/vid/pid if usb 3G is found.*/
	if (substr($phyinf, 0, 3)==TTY)
	{
		$phyinfp= XNODE_getpathbytarget("", "phyinf", "uid", $phyinf, 0);
		$slot	= query($phyinfp."/slot");
		$ttyp	= XNODE_getpathbytarget("/runtime/tty", "entry", "slot", $slot, 0);
		if ($ttyp=="")
		{
			SHELL_info($_GLOBALS["START"], "ifsetup: (".$name.") no phyinf.");
			SHELL_info($_GLOBALS["STOP"],  "ifsetup: (".$name.") no phyinf.");
			error("9");
			return;
		}
	}
	/*+++*/
	/* Get the profile. */
	$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
	/*
	if ($inetp == "")
	{
		SHELL_info($_GLOBALS["START"], "ifsetup: (".$name.") no inet profile.");
		SHELL_info($_GLOBALS["STOP"],  "ifsetup: (".$name.") no inet profile.");
		error("9");
		return;
	}
	*/
	
	if ($inetp == "")
	{
		$_GLOBALS["CHILDIF"]=$name;
		dophp("load", "/etc/scripts/IPV6.CHILD.php");
		return;
	}
	
	
	startcmd("echo [$0]: starting ".$name."... > /dev/console");
	stopcmd("echo [$0]: stopping ".$name."... > /dev/console");

	$_GLOBALS["INET_INFNAME"] = $name;
	$addrtype = query($inetp."/addrtype");
	if ($lower=="")
	{
		//for ppp we disable CTF and HNAT, because it has problems
		if		($addrtype == "ipv4")
		{
			dophp("load", "/etc/services/INET/inet_ipv4.php");
		}
		else if	($addrtype == "ipv6")
		{
			dophp("load", "/etc/services/INET/inet_ipv6.php");
		}
		else if	($addrtype == "ppp4") 
		{
			dophp("load", "/etc/services/INET/inet_ppp4.php");
			//suppress_ctf_acceleration();
		}
		else if	($addrtype == "ppp6" || $addrtype == "ppp10") 
		{
			dophp("load", "/etc/services/INET/inet_ppp6.php");
			suppress_ctf_acceleration();
		}
	}
	else
	{
		/* Only PPTP/L2TP will use COMBO interface. */
		if ($addrtype == "ppp4") 
		{
			dophp("load", "/etc/services/INET/inet_ppp4_combo.php");
			suppress_ctf_acceleration();
		}
	}
	startcmd("echo [$0]: starting ".$name." done !!! > /dev/console");
	stopcmd("echo [$0]: stopping ".$name." done !!! > /dev/console");
	error("0");
}

function ifinetsetup($name, $infp)
{
	startcmd('service INET.'.$name.' alias INF.'.$name);

	/* Get master's schedule setting. Backup will follow master's schedule. */
	$masterp = XNODE_getpathbytarget("", "inf", "backup", $name, 0);
	if ($masterp != "")
	{
		/* Get master's schedule setting.        */
		/* Backup will follow master's schedule. */
		$sch = XNODE_getschedule($masterp);
	}
	else
	{
		/* Get schedule setting */
		$sch = XNODE_getschedule($infp);
	}
	if ($sch=="") $cmd = "start";
	else
	{
		$days = XNODE_getscheduledays($sch);
		$start = query($sch."/start");
		$end = query($sch."/end");
		if (query($sch."/exclude")=="1") $cmd = 'schedule!';
		else $cmd = 'schedule';
		$cmd = $cmd.' "'.$days.'" "'.$start.'" "'.$end.'"';
	}
	fwrite(a, $_GLOBALS["START"], 'service INF.'.$name.' '.$cmd.'\n');
	fwrite(a, $_GLOBALS["STOP"],  'service INF.'.$name.' stop\n');
}

function ifinetsetupall($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		startcmd('# '.$ifname);
		stopcmd( '# '.$ifname);

		fwrite("a", $_GLOBALS["START"], "rm -f /var/run/".$ifname.".UP\n");
		$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }

		/* If this INF has previous, do not start it now,
		 * the previous interface will bring it up. */
		if (query($ifpath."/infprevious")=="") ifinetsetup($ifname, $ifpath);

		$i++;
	}
}

function srviptsetupall($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		fwrite("a",$_GLOBALS["START"], "service IPT.".$ifname." start\n");
		fwrite("a",$_GLOBALS["STOP"],  "service IPT.".$ifname." stop\n");
		if (isfile("/proc/net/if_inet6")==1)
		{
			fwrite("a",$_GLOBALS["START"], "service IP6T.".$ifname." start\n");
			fwrite("a",$_GLOBALS["STOP"],  "service IP6T.".$ifname." stop\n");
		}
		$i++;
	}

}

function chkconnsetupall($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		$active	= query($ifpath."/active");
		$disable= query($ifpath."/disable");
		$backup = query($ifpath."/backup");
		if ($active=="1" && $disable=="0" && $backup!="")
		{
			/* Backup ip_conntrack counter. */
			fwrite("a", $_GLOBALS["START"], 'PROC=/proc/sys/net/ipv4/netfilter/\n');
			fwrite("a", $_GLOBALS["START"], 'cd $PROC\n');
			fwrite("a", $_GLOBALS["START"], 'timeouts=`ls ip_conntrack_* | grep timeout`\n');
			fwrite("a", $_GLOBALS["START"], 'for i in $timeouts\n');
			fwrite("a", $_GLOBALS["START"], 'do\n');
			fwrite("a", $_GLOBALS["START"], 'cnt=`cat $i`\n');
			fwrite("a", $_GLOBALS["START"], 'xmldbc -s /runtime/services/conntrack/$i $cnt\n');
			fwrite("a", $_GLOBALS["START"], 'done\n');

			/* Get schedule setting */
			$sch = XNODE_getschedule($ifpath);
			if ($sch=="") $cmd = "start";
			else
			{
				$days = XNODE_getscheduledays($sch);
				$start = query($sch."/start");
				$end = query($sch."/end");
				if (query($sch."/exclude")=="1") $cmd = 'schedule!';
				else $cmd = 'schedule';
				$cmd = $cmd.' "'.$days.'" "'.$start.'" "'.$end.'"';
			}
			fwrite("a", $_GLOBALS["START"], 'service CHKCONN.'.$ifname.' '.$cmd.'\n');
			fwrite("a", $_GLOBALS["STOP"],  'service CHKCONN.'.$ifname.' stop\n');
		}
		$i++;
	}
}

function ifchildsetup($name)
{
	/* Get the runtime interface */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	startcmd('# ifchildsetup: infp='.$infp);
	if ($infp == "")
	{
		SHELL_info($_GLOBALS["START"], "ifchildsetup: (".$name.") no interface.");
		SHELL_info($_GLOBALS["STOP"],  "ifchildsetup: (".$name.") no interface.");
		error("9");
		return;
	}

	/* Is this interface active ? */
	$disable= query($infp."/disable");
	$active	= query($infp."/active");
	$phyinf	= query($infp."/phyinf");
	if ($disable==1 || $active!=1)
	{
		SHELL_info($_GLOBALS["START"], "ifchildsetup: (".$name.") not active.");
		SHELL_info($_GLOBALS["STOP"],  "ifchildsetup: (".$name.") not active.");
		error("8");
		return;
	}

	/* Get the physical interface */
	if ($phyinf == "")
	{
		SHELL_info($_GLOBALS["START"], "ifchildsetup: (".$name.") no phyinf.");
		SHELL_info($_GLOBALS["STOP"],  "ifchildsetup: (".$name.") no phyinf.");
		error("9");
		return;
	}

	$_GLOBALS["CHILD_INFNAME"] = $name;
	dophp("load", "/etc/services/INET/inet_child.php");
}
?>
