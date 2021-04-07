<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

include "/etc/services/INET/options_ppp4.php";

function START($cmd)	{ fwrite(a,$_GLOBALS["START"], $cmd."\n"); }
function STOP($cmd)		{ fwrite(a,$_GLOBALS["STOP"],  $cmd."\n"); }
function DIALUP($cmd)	{ fwrite(a,$_GLOBALS["dialupsh"], $cmd."\n"); }
function HANGUP($cmd)	{ fwrite(a,$_GLOBALS["hangupsh"], $cmd."\n"); }

function HANGUP_waitfile($file)
{
	HANGUP("while [ -f ".$file." ]; do");
	HANGUP("	echo [$0]: ".$file." exist, wait ... > /dev/console");
	HANGUP("	sleep 1");
	HANGUP("done");
}

function lower_static($infp, $l_infp, $l_inetp)
{
	$inf = $_GLOBALS["LOWER"];
	anchor($l_inetp."/ipv4");
	$ipaddr	= query("ipaddr");
	$mask	= query("mask");
	$gw		= query("gateway");
	$mtu	= query("mtu");
	$defrt	= query("defaultroute");
	$phyinf	= query($l_infp."/phyinf");
	$ifname	= PHYINF_getifname($phyinf);

	/* Get DNS setting */
	$cnt = query("dns/count")+0;
	foreach("dns/entry")
	{
		if ($InDeX > $cnt) break;
		if ($dns=="") $dns = $VaLuE;
		else $dns = $dns." ".$VaLuE;
	}

	/* Dialup */
	DIALUP("phpsh /etc/scripts/IPV4.INET.php ACTION=ATTACH".
		" INF=".$inf.
		" DEVNAM=".$ifname.
		" IPADDR=".$ipaddr.
		" MASK=".$mask.
		" GATEWAY=".$gw.
		" MTU=".$mtu.
		' "DNS='.$dns.'"'
		);
	/* Hangup */
	HANGUP("/etc/scripts/killpid.sh ".$_GLOBALS["pppd_pid"]);
	HANGUP_waitfile($_GLOBALS["sfile"]);
	HANGUP("phpsh /etc/scripts/IPV4.INET.php ACTION=DETACH INF=".$inf);
}

function lower_dhcp($infp, $l_infp, $l_inetp)
{
	$inf = $_GLOBALS["LOWER"];
	$phyinf = query($l_infp."/phyinf");
	$ifname = PHYINF_getifname($phyinf);
	/* Get Setting */
	$hostname	= get("s", "/device/hostname");
	$mtu		= query($l_inetp."/mtu");
	/* Get DNS setting */
	$cnt = query($l_inetp."/dns/count")+0;
	foreach($l_inetp."/dns/entry")
	{
		if ($InDeX > $cnt) break;
		$dns = $VaLuE." ";
	}
	/* The files */
	$udhcpc_helper	= "/var/servd/".$inf."-udhcpc.sh";
	$udhcpc_pid		= "/var/servd/".$inf."-udhcpc.pid";
	$hlper			= "/etc/services/INET/inet4_dhcpc_helper.php";

	/* Generate the callback script for udhcpc. */
	fwrite(w,$udhcpc_helper,
		'#!/bin/sh\n'.
		'echo [$0]: $1 $interface $ip $subnet $router $lease $domain $scope $winstype $wins... > /dev/console\n'.
		'phpsh '.$hlper.' ACTION=$1'.
			' INF='.$inf.
			' INET='.$_GLOBALS["L_INET"].
			' MTU='.$mtu.
			' INTERFACE=$interface'.
			' IP=$ip'.
			' SUBNET=$subnet'.
			' BROADCAST=$broadcast'.
			' LEASE=$lease'.
			' "DOMAIN=$domain"'.
			' "ROUTER=$router"'.
			' "DNS='.$dns.'$dns"'.
			' "CLSSTROUT=$clsstrout"'.
			' "SSTROUT=$sstrout"'.
			' "SCOPE=$scope"'.
			' "WINSTYPE=$winstype"'.
			' "WINS=$wins"\n'.
		'exit 0\n'
		);
	
	/*dhcpc always unicast*/
	$unicast = "";
	if(query("/dhcpc4/unicast")=="yes") $unicast = "-u ";
	
	/* set MTU */	
	if ($mtu!="") DIALUP('ip link set '.$ifname.' mtu '.$mtu);
	DIALUP('event '.$inf.'.DHCP.RENEW     add "kill -SIGUSR1 \\`cat '.$udhcpc_pid.'\\`"');
	DIALUP('event '.$inf.'.DHCP.RELEASE   add "kill -SIGUSR2 \\`cat '.$udhcpc_pid.'\\`"');
	DIALUP('chmod +x '.$udhcpc_helper);
	DIALUP('udhcpc '.$unicast.'-i '.$ifname.' -H '.$hostname.' -p '.$udhcpc_pid.' -s '.$udhcpc_helper.' &');

	HANGUP("event ".$inf.".DHCP.RELEASE flush");
	HANGUP("event ".$inf.".DHCP.RENEW flush");
	HANGUP("/etc/scripts/killpid.sh ".$_GLOBALS["pppd_pid"]);
	HANGUP_waitfile($_GLOBALS["sfile"]);
	HANGUP("/etc/scripts/killpid.sh ".$udhcpc_pid);
	HANGUP_waitfile($udhcpc_pid);
	HANGUP("sleep 3"); // wait 3 sec. the PID file will disapear earlier.
}

/* PPP IPv4 *****************************************************/
START("# INFNAME = [".$_GLOBALS["INET_INFNAME"]."]");
STOP( "# INFNAME = [".$_GLOBALS["INET_INFNAME"]."]");

/* The config of the PPP interface (the upperlayer). */
$INF	= $INET_INFNAME;
$INFP	= XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$LOWER	= query($INFP."/lowerlayer");
$INET	= query($INFP."/inet");
$DEFRT	= query($INFP."/defaultroute");
$PHYINF	= query($INFP."/phyinf");
$DEVNAM	= PHYINF_getifname($PHYINF);
$INETP	= XNODE_getpathbytarget("/inet", "entry", "uid", $INET, 0);

/* DIAL mode */
$DIAL = XNODE_get_var($INF.".DIALUP");
if ($DIAL=="") $DIAL=query($INETP."/ppp4/dialup/mode");

/* Files *******************************************/
/* generate option file */
$optfile	= create_pppoptions($INF, $DEVNAM, $INETP."/ppp4", $DEFRT, $DAIL);
$sfile		= "/var/run/ppp-".$INF.".status";
$pppd_pid	= "/var/run/ppp-".$INF.".pid";
$dialupsh	= "/var/run/ppp-".$INF."-dialup.sh";
$hangupsh	= "/var/run/ppp-".$INF."-hangup.sh";
$runpppsh	= "/var/run/ppp-".$INF."-run.sh";

START("# optfile = [".$optfile."]");

/* Initial the script files. */
fwrite(w, $dialupsh, "#!/bin/sh\n");
fwrite(w, $hangupsh, "#!/bin/sh\n");
fwrite(w, $runpppsh,
	"#!/bin/sh\n".
	"phpsh /etc/scripts/SETVPNSRRT.php INF=".$INF."\n".
	"pppd file ".$optfile."\n".
	$hangupsh."\n"
	);

/* Flush this event handler, just in case... */
START('event '.$INF.'.PPP.AUTHFAILED flush');

/* Lower layer */
$L_INFP		= XNODE_getpathbytarget("", "inf", "uid", $LOWER, 0);
$L_INET		= query($L_INFP."/inet");
$L_INETP	= XNODE_getpathbytarget("/inet", "entry", "uid", $L_INET, 0);
$L_ADDRTYPE	= query($L_INETP."/addrtype");

//++++++ hendry
/* Create the runtime inf. Set phyinf. */
$phyinf	= query($L_INFP."/phyinf");
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $LOWER, 1);
set($stsp."/phyinf", $phyinf);
//------ hendry
set($stsp."/upperlayer", query($L_INFP."/upperlayer"));
set($stsp."/lowerlayer", query($L_INFP."/lowerlayer"));
set($stsp."/inet/uid", $L_INET);
set($stsp."/inet/addrtype", $L_ADDRTYPE);

if ($L_ADDRTYPE!="ipv4")
{
	START('# The lower must be native IPv4 !!');
	START('exit 9');
	STOP('exit 9');
}
else
{
	if (query($L_INETP."/ipv4/static")=="1")
		lower_static($INFP, $L_INFP, $L_INETP);
	else  lower_dhcp($INFP, $L_INFP, $L_INETP);

	/* Setup events */
	START('event '.$LOWER.'.UP insert "runpppd:'.$runpppsh.' &"');
	START('event '.$LOWER.'.DOWN insert "killpppd:/etc/scripts/killpid.sh '.$pppd_pid.'"');

	/* Prepare ip-up ip-down & events */
	if ($DIAL=="ondemand")	$dialcmd = 'ping 10.112.113.'.cut($INF, 1, '-');
	else					$dialcmd = $dialupsh;
	START('cp /etc/scripts/ip-up /etc/ppp/.');
	START('cp /etc/scripts/ip-down /etc/ppp/.');
	START('cp /etc/scripts/ppp-status /etc/ppp/.');
	START('chmod +x '.$dialupsh.' '.$hangupsh.' '.$runpppsh);
	START('event '.$INF.'.PPP.DIALUP add "'.$dialcmd.'"');
	START('event '.$INF.'.PPP.HANGUP add "'.$hangupsh.'"');
	/* For backward compatible, we still create COMBO style events */
	START('event '.$INF.'.COMBO.DIALUP add "'.$dialcmd.'"');
	START('event '.$INF.'.COMBO.HANGUP add "'.$hangupsh.'"');

	START('event WANPORT.LINKUP insert WANPORTLINKUP:"phpsh /etc/events/WANPORT_LINKUP.sh"');

	/* If the dial mode is manual, DO NOT run the loop script. */
	if ($DIAL!="manual")
	{
		START($dialupsh);
		START('event '.$LOWER.'.DOWN insert "redial:xmldbc -t redial.'.$INF.':5:'.$dialupsh.'"');
		STOP( 'event '.$LOWER.'.DOWN remove "redial"');
	}

	/* Stop script *************************************/
	STOP('xmldbc -k redial.'.$INF);
	STOP('event '.$INF.'.PPP.DIALUP flush');
	STOP('event '.$INF.'.PPP.HANGUP flush');
	STOP('event '.$INF.'.COMBO.DIALUP flush');
	STOP('event '.$INF.'.COMBO.HANGUP flush');
	STOP('event '.$LOWER.'.DOWN remove "killpppd"');
	STOP('event '.$LOWER.'.UP remove "runpppd"');
	STOP('rm -f '.$dialupsh.' '.$runpppsh.' '.$optfile);
	STOP($hangupsh);
	STOP('rm -f '.$hangupsh);
	STOP('event WANPORT.LINKUP remove WANPORTLINKUP');
}
?>
