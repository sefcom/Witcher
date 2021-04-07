<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");} 

fwrite(w,$_GLOBALS["START"], "");
fwrite(w,$_GLOBALS["STOP"], "");

/* Setup nameresolv info into runtime nodes */
function netbios_setup($name)
{
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	
	if ($infp=="")
	{
		SHELL_info($_GLOBALS["START"], "nameresolv_setup: (".$name.") not exist.");
		SHELL_info($_GLOBALS["STOP"],  "nameresolv_setup: (".$name.") not exist.");
		return;
	}
	
	/* Get the "runtime" physical interface */
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
	if ($stsp!="")
	{
		$phy = query($stsp."/phyinf");
		if ($phy!="")
		{
			$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy, 0);
			if ($phyp!="" && query($phyp."/valid")=="1")
				$ifname = query($phyp."/name");
		}
	}
	if ( $ifname == "" )	{ 	$ifname = query($stsp."/devnam");	}
	
	/* Get address family & IP address */
	$atype	= query($stsp."/inet/addrtype");
	if		($atype=="ipv4" || $atype=="ppp4") {$af="inet";}
	else if	($atype=="ipv6" || $atype=="ppp6") {$af="inet6";}

	if ($ifname==""||$atype=="")
	{
		SHELL_info($_GLOBALS["START"], "nameresolv_setup: (".$name.") no phyinf.");
		SHELL_info($_GLOBALS["STOP"],  "nameresolv_setup: (".$name.") no phyinf.");
		return;
	}

	/* Setup related info to runtime nodes */
	$stsp = XNODE_getpathbytarget("/runtime/services/nameresolv", "entry", "uid", "NAMERESOLV.".$name, 0);
	if ($stsp=="")
	{
		$dirty++;
		$stsp = XNODE_getpathbytarget("/runtime/services/nameresolv", "entry", "uid", "NAMERESOLV.".$name, 1);
		set($stsp."/inf",	$name);
		set($stsp."/ifname",$ifname);
		set($stsp."/af",	$af);
	}
	else
	{
		if (query($stsp."/inf")!=$name)			{ $dirty++; set($stsp."/inf", $name); }
		if (query($stsp."/ifname")!=$ifname)	{ $dirty++; set($stsp."/ifname", $ifname); }
		if (query($stsp."/af")!=$af)			{ $dirty++; set($stsp."/af", $af); }
	}	

	/* If something changes, then set dirty flag */
	if ( $dirty > 0 )
	{
		$stsp = XNODE_getpathbytarget("/runtime/services/nameresolv", "ifname", "uid", $ifname, 0);
		if ($stsp=="")
		{
			$stsp = XNODE_getpathbytarget("/runtime/services/nameresolv", "ifname", "uid", $ifname, 1);
			set($stsp."/dirty",	1);
		}
		else
		{
			set($stsp."/dirty",	1);
		}	
	}

	/* Start service */
	if ($dirty>0) $action="restart"; else $action="start";
	startcmd("service NAMERESOLV ".$action);
	startcmd("exit 0");

	/* Stop service */
	stopcmd('sh /etc/scripts/delpathbytarget.sh runtime/services/nameresolv entry uid NAMERESOLV.'.$name);
	stopcmd("xmldbc -P /etc/services/NAMERESOLV/nameresolv_del.php -V IFNAME=".$ifname." > /var/run/nameresolv.sh");
	stopcmd("rm -f /var/run/nameresolv.sh");
	stopcmd("service NAMERESOLV restart");
	stopcmd("exit 0");
}


/*start netbios and llmnr for each interface */
function nameresolv_start()
{
	$hostname = query("/device/hostname");
	startcmd("hostname ".$hostname."\n");
	foreach ("/runtime/services/nameresolv/entry") { $cnt++; }
	
	/* Start/stop nameresolv daemon by interface*/
	foreach ("/runtime/services/nameresolv/ifname")
	{
		/* This interface have changes or not */
		if ( query("dirty") != 1 ) continue;
		$ifname = query("uid");
		if ( $ifname == "" ) continue;

		/* clear dirty bit */
		set("dirty", 0);
		
		$i=0;
		$netbios_enable=0;
		$llmnresp_enable=0;
		$ahostname="";
		$param="";
		$active=0;
		/* Check this interface shall enable netbios/llmnr or not. 
		  * By default, if have IPv4 interface, then enable netbios and llmnr both.
		  *             else if IPv6 interface only, then enable llmnr only 
		  */
		while ($i < $cnt)
		{
			$i++;
			if ( query("/runtime/services/nameresolv/entry:".$i."/ifname" ) == $ifname )
			{ 
				if ( query("/runtime/services/nameresolv/entry:".$i."/af" ) == "inet" )
				{ 
					$netbios_enable=1;
					$llmnresp_enable=1;
					$active++;
				}
				else if ( query("/runtime/services/nameresolv/entry:".$i."/af" ) == "inet6" )
				{ 
					$llmnresp_enable=1;
					$active++;
				}	
				
				/* add alias hostname */
				/* hostnameWXYZ , WXYZ is the latest 4 digits of MAC address */
				if ( $ahostname == "" )
				{
					//$name = query("/runtime/services/nameresolv/entry:".$i."/inf" );
					//$mac = PHYINF_getmacsetting($name);
					//use LAN mac as suffix (tom, 20130711)
					$mac = PHYINF_getmacsetting("LAN-1");
					if ( $mac != "" )
					{
						$macstr = cut($mac, 4, ":").cut($mac, 5, ":");
						$ahostname = $hostname.$macstr;
					}
				}
			}
		}
		
		/* Try to kill current nameresolv service */
		startcmd("/etc/scripts/killpid.sh /var/run/nameresolv-".$ifname.".pid\n");

		/* This interface will run nameresolv service */
		if ($active > 0 )
		{
			/* enable netbios function support */
			if ( $netbios_enable == 1 )		{ $param = $param." -n ";	}
			/* enable llmnr function support */
			if ( $llmnresp_enable == 1 )	{ $param = $param." -l ";	}
	
			
//jef add +   for support use shareport.local to access shareportmobile
			$web_file_access = query("/webaccess/enable");
			if($web_file_access == 1)
				$param = $param." -i ".$ifname." -r ".$hostname." -r ".$ahostname."  -r shareport.local -r shareport ";		
			else
				$param = $param." -i ".$ifname." -r ".$hostname." -r ".$ahostname;
//jef add -
			startcmd("nameresolv ".$param." &\n");
			startcmd("echo $! >  /var/run/nameresolv-".$ifname.".pid\n");
		}
	}

	startcmd("exit 0");
	stopcmd("exit 0");
}
?>
