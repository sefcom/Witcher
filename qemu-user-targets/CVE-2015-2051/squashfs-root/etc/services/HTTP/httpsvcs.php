<?
/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/mdnsresponder.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function http_error($errno)
{
	fwrite("a", $_GLOBALS["START"], "exit ".$errno."\n");
	fwrite("a", $_GLOBALS["STOP"],  "exit ".$errno."\n");
}

function upnpsetup($name)
{
	/* Get the interface */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	if ($infp=="")
	{
		SHELL_info($_GLOBALS["START"], "httpsetup: (".$name.") not exist.");
		SHELL_info($_GLOBALS["STOP"],  "httpsetup: (".$name.") not exist.");
		http_error("9");
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
	/* Get address family & IP address */
	$atype = query($stsp."/inet/addrtype");
	if		($atype=="ipv4") {$af="inet"; $ipaddr=query($stsp."/inet/ipv4/ipaddr");}
	else if	($atype=="ppp4") {$af="inet"; $ipaddr=query($stsp."/inet/ppp4/local");}
	else if	($atype=="ipv6") {$af="inet6";$ipaddr=query($stsp."/inet/ipv6/ipaddr");}
	else if	($atype=="ppp6") {$af="inet6";$ipaddr=query($stsp."/inet/ppp6/local");}

	if ($ifname==""||$af==""||$ipaddr=="")
	{
		SHELL_info($_GLOBALS["START"], "httpsetup: (".$name.") no phyinf.");
		SHELL_info($_GLOBALS["STOP"],  "httpsetup: (".$name.") no phyinf.");
		http_error("9");
		return;
	}

	/* Get configuration */
	$upnp	= query($infp."/upnp/count");
	$dirty	= 0;
	$port	= "49152";

	$stsp = XNODE_getpathbytarget("/runtime/services/http", "server", "uid", "UPNP.".$name, 0);
	$ssdp = XNODE_getpathbytarget("/runtime/services/http", "server", "uid", "SSDP.".$name, 0);
	if ($stsp=="")
	{
		if ($upnp==0)
		{
			SHELL_info($_GLOBALS["START"], "upnpsetup: (".$name.") not active.");
			SHELL_info($_GLOBALS["STOP"],  "upnpsetup: (".$name.") not active.");
			http_error("8");
			return;
		}
		else
		{
			$dirty++;
			$ssdp = XNODE_getpathbytarget("/runtime/services/http","server","uid","SSDP.".$name,1);
			$stsp = XNODE_getpathbytarget("/runtime/services/http","server","uid","UPNP.".$name,1);
			set($stsp."/mode",	"UPNP");
			set($stsp."/inf",	$name);
			set($stsp."/ifname",$ifname);
			set($stsp."/ipaddr",$ipaddr);
			set($stsp."/port",	$port);
			set($stsp."/af",	$af);
			set($ssdp."/mode",	"SSDP");
			set($ssdp."/inf",	$name);
			set($ssdp."/ifname",$ifname);
			set($ssdp."/ipaddr",$ipaddr);
			set($ssdp."/af",	$af);
		}
	}
	else
	{
		if ($upnp==0) { $dirty++; del($stsp); del($ssdp); }
		else
		{
			if (query($stsp."/mode")!="UPNP")		{ $dirty++; set($stsp."/mode", "UPNP"); }
			if (query($stsp."/inf")!=$name)			{ $dirty++; set($stsp."/inf", $name); }
			if (query($stsp."/ifname")!=$ifname)	{ $dirty++; set($stsp."/ifname", $ifname); }
			if (query($stsp."/ipaddr")!=$ipaddr)	{ $dirty++; set($stsp."/ipaddr", $ipaddr); }
			if (query($stsp."/port")!=$port)		{ $dirty++; set($stsp."/port", $port); }
			if (query($stsp."/af")!=$af)			{ $dirty++; set($stsp."/af", $af); }
			if (query($ssdp."/mode")!="SSDP")		{ $dirty++; set($ssdp."/mode", "SSDP"); }
			if (query($ssdp."/inf")!=$name)			{ $dirty++; set($ssdp."/inf", $name); }
			if (query($ssdp."/ifname")!=$ifname)	{ $dirty++; set($ssdp."/ifname", $ifname); }
			if (query($ssdp."/ipaddr")!=$ipaddr)	{ $dirty++; set($ssdp."/ipaddr", $ipaddr); }
			if (query($ssdp."/af")!=$af)			{ $dirty++; set($ssdp."/af", $af); }
		}
	}
	//fwrite("a",$_GLOBALS["STOP"], "xmldbc -X ".$stsp."\n");
	//fwrite("a",$_GLOBALS["STOP"], "xmldbc -X ".$ssdp."\n");
	stopcmd('sh /etc/scripts/delpathbytarget.sh runtime/services/http server uid SSDP.'.$name);
	stopcmd('sh /etc/scripts/delpathbytarget.sh runtime/services/http server uid UPNP.'.$name);

	/* Create the device description files */
	$vdir = "/var/htdocs/upnp/".$name;
	$i = 0;
	while ($i < $upnp)
	{
		$i++;
		$value = query($infp."/upnp/entry:".$i);
		$devp = XNODE_getpathbytarget("/runtime/upnp", "dev", "deviceType", $value, 0);
		if ($devp!="")
		{
			startcmd("[ ! -f ".$vdir." ] && mkdir -p ".$vdir);
			foreach ($devp."/xmldoc")
			{
				startcmd('xmldbc -P /htdocs/upnpdevdesc/'.$VaLuE.'.php -V "INF='.$name.'" > '.$vdir.'/'.$VaLuE);
			}
			$mkln++;
			if ($value == "urn:schemas-wifialliance-org:device:WFADevice:1") $mkln_wfa++;
		}
	}
	/* make symbolic links */
	if ($mkln > 0)
	{
		startcmd("ln -s /htdocs/cgibin ".$vdir."/soap.cgi");
		startcmd("ln -s /htdocs/cgibin ".$vdir."/gena.cgi");
	}
	if ($mkln_wfa > 0) startcmd("ln -s /htdocs/cgibin ".$vdir."/wfadev.cgi");

	stopcmd("rm -rf ".$vdir);

	/* UPnP events */
	/* Notify alive */
	$maxage		= 1800;
	$abs		= "/etc/scripts/upnp/NOTIFYAB.sh";
	$alivecmd	= $abs.' ssdp:alive '.$name.' '.$ifname.' '.$ipaddr.' '.$maxage.' '.$af; //$af add by sam_pan, for check ipv6.
	$byebyecmd	= $abs.' ssdp:byebye '.$name.' '.$ifname.' '.$ipaddr.' '.$maxage.' '.$af; 
	/* Add Notify events */
	startcmd('event UPNP.ALIVE.'.$name.' add "'.$alivecmd.'"');
	startcmd('event UPNP.BYEBYE.'.$name.' add "'.$byebyecmd.'"');
	startcmd('event UPNP.IGD.NOTIFY.WANIPCONN1 add "sh /htdocs/upnp/NOTIFY.WANIPConnection.1.sh"');
	/* Send Notify alive */
	startcmd("event UPNP.ALIVE.".$name);

	stopcmd("event UPNP.BYEBYE.".$name);
	stopcmd("event UPNP.ALIVE."	.$name." add true");
	stopcmd("event UPNP.BYEBYE.".$name." add true");
	stopcmd("event UPNP.IGD.NOTIFY.WANIPCONN1 add true");
	stopcmd("xmldbc -X /runtime/services/upnp");

	/* start HTTP service */
	if ($dirty>0) $action="restart"; else $action="start";
	startcmd("service HTTP ".$action);
	startcmd("exit 0");

	stopcmd("service HTTP restart");
	stopcmd("exit 0");
}

function httpsetup($name)
{
	/* Get the interface */
	$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
	if ($infp=="")
	{
		SHELL_info($_GLOBALS["START"], "httpsetup: (".$name.") not exist.");
		SHELL_info($_GLOBALS["STOP"],  "httpsetup: (".$name.") not exist.");
		http_error("9");
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
	/* Get address family & IP address */
	$atype	= query($stsp."/inet/addrtype");
	if		($atype=="ipv4") {$af="inet"; $ipaddr=query($stsp."/inet/ipv4/ipaddr");}
	else if	($atype=="ppp4") {$af="inet"; $ipaddr=query($stsp."/inet/ppp4/local");}
	else if	($atype=="ipv6") {$af="inet6";$ipaddr=query($stsp."/inet/ipv6/ipaddr");}
	else if	($atype=="ppp6") {$af="inet6";$ipaddr=query($stsp."/inet/ppp6/local");}

	if ($ifname==""||$atype==""||$ipaddr=="")
	{
		SHELL_info($_GLOBALS["START"], "httpsetup: (".$name.") no phyinf.");
		SHELL_info($_GLOBALS["STOP"],  "httpsetup: (".$name.") no phyinf.");
		http_error("9");
		return;
	}

	/* Get configuration */
	$port	= query($infp."/web");
	/*for new ui,when web enable,the hnap also need enable.*/
	if($port!="")
	{
		$hnap = 1;
	}
	else
	{
		$hnap	= query($infp."/hnap");
	}
	
	$dirty	= 0;

	$stsp = XNODE_getpathbytarget("/runtime/services/http", "server", "uid", "HTTP.".$name, 0);
	if ($stsp=="")
	{
		if ($port=="")
		{
			SHELL_info($_GLOBALS["START"], "httpsetup: (".$name.") not active.");
			SHELL_info($_GLOBALS["STOP"],  "httpsetup: (".$name.") not active.");
			http_error("8");
			return;
		}
		else
		{
			$dirty++;
			$stsp = XNODE_getpathbytarget("/runtime/services/http", "server", "uid", "HTTP.".$name, 1);
			set($stsp."/mode",	"HTTP");
			set($stsp."/inf",	$name);
			set($stsp."/ifname",$ifname);
			set($stsp."/ipaddr",$ipaddr);
			set($stsp."/port",	$port);
			set($stsp."/hnap",	$hnap);
			set($stsp."/af",	$af);
		}
	}
	else
	{
		if ($port=="") { $dirty++; del($stsp); }
		else
		{
			if (query($stsp."/mode")!="HTTP")		{ $dirty++; set($stsp."/mode", "HTTP"); }
			if (query($stsp."/inf")!=$name)			{ $dirty++; set($stsp."/inf", $name); }
			if (query($stsp."/ifname")!=$ifname)	{ $dirty++; set($stsp."/ifname", $ifname); }
			if (query($stsp."/ipaddr")!=$ipaddr)	{ $dirty++; set($stsp."/ipaddr", $ipaddr); }
			if (query($stsp."/port")!=$port)		{ $dirty++; set($stsp."/port", $port); }
			if (query($stsp."/hnap")!=$hnap)		{ $dirty++; set($stsp."/hnap", $hnap); }
			if (query($stsp."/af")!=$af)			{ $dirty++; set($stsp."/af", $af); }
		}
	}

	/* info for mdnsresponder */
	$port	= "80";
	$product = query("/runtime/device/modelname");
	$srvname = "D-Link ".$product;
	$srvcfg = "_http._tcp. local.";
	$mdirty = setup_mdns("MDNSRESPONDER.HTTP",$port,$srvname,$srvcfg);
	$ret=0;
	
	/* info for iphone app SharePort */
	if(query("webaccess/enable")=="1") 
		$wfaport=query("/webaccess/httpport");
	else
		$wfaport=0;
	$ret = setup_mdns("MDNSRESPONDER.WFA", $wfaport, "D-Link SharePort Web Access", "_http._tcp. local.");
	$mdirty = $mdirty + $ret;
	
	/* info for QRS Mobile */
	if($hnap > 0)
	{
		$ret = setup_mdns("MDNSRESPONDER.HNAP", "80", "D-Link HNAP Service", "_dhnap._tcp. local.");
		$mdirty = $mdirty + $ret;
	}	


	if ($dirty>0) $action="restart"; else $action="start";
	startcmd("service HTTP ".$action);
	if (query($stsp."/hnap")!="0")
	{startcmd("xmldbc -P /etc/services/HTTP/hnapasswd.php > /var/etc/hnapasswd");}
	if ($mdirty>0)
	{
		startcmd("service MDNSRESPONDER restart");
		stopcmd('sh /etc/scripts/delpathbytarget.sh /runtime/services/mdnsresponder server uid MDNSRESPONDER.HTTP');
		stopcmd("service MDNSRESPONDER restart");
	}
	startcmd("exit 0");

	//fwrite("a",$_GLOBALS["STOP"], "xmldbc -X ".$stsp."\n");
	stopcmd('sh /etc/scripts/delpathbytarget.sh runtime/services/http server uid HTTP.'.$name);
	stopcmd("service HTTP restart");
	stopcmd("exit 0");
}
?>
