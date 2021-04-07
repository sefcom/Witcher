#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/inf.php";

function startcmd($cmd)	{if($_GLOBALS["ACTION"]=="DSLITE_START") echo $cmd."\n";}
function stopcmd($cmd)	{if($_GLOBALS["ACTION"]=="DSLITE_STOP") echo $cmd."\n";}

function inet_ipv4_dslite($inf, $ifname, $inet, $inetp, $infprev)
{
	startcmd("# inet_start_ipv4_dslite(".$inf.",".$ifname.",".$inet.",".$inetp.",".$infprev.")");
	startcmd("echo $$ > /var/run/dslite_start.pid");

	/* Get INET setting */
	$b4addr = query($inetp."/ipv4/ipaddr");
	anchor($inetp."/ipv4/ipv4in6");
	$remote = query("remote");

	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	if($b4addr!="") set($stsp."/inet/ipv4/ipaddr", $b4addr);
	else set($stsp."/inet/ipv4/ipaddr","");
	$devnam = "ip4ip6.".$inf;
	//if($local!="")  $lcmd = " local ".$local;
	
	$mtu = query($inetp."/ipv4/mtu");
	if($mtu=="") $mtu = 1452;
	set($stsp."/inet/ipv4/mtu", $mtu);

	if($remote!="") /* static */
	{
		if($infprev!="") 
		{
			$local = INF_getcurripaddr($infprev);
			if($local!="")  $lcmd = " local ".$local;
		}
		$rcmd = " remote ".$remote;
		startcmd("ip -6 tunnel add ".$devnam." mode ip4ip6".$rcmd.$lcmd);
		set($stsp."/inet/ipv4/ipv4in6/remote",	$remote);
		startcmd("ip link set dev ".$devnam." up");
		if($b4addr!="")
			startcmd("ip -4 addr add ".$b4addr." dev ".$devnam);
		startcmd("ip route add default dev ".$devnam);
		$uptime = query("/runtime/device/uptime");
		set($stsp."/inet/uptime",	query("/runtime/device/uptime"));
		set($stsp."/inet/ipv4/valid", "1");
		startcmd("event ".$inf.".UP");
		startcmd("echo 1 > /var/run/".$inf.".UP");
		
		stopcmd("ip -6 tunnel del ".$devnam);		
		stopcmd("ip route del default dev ".$devnam);
		stopcmd("xmldbc -s ".$stsp."/inet/ipv4/valid 0");
		stopcmd("event ".$inf.".DOWN");
		stopcmd("rm -f /var/run/".$inf.".UP");
	}
	else /* dynamic */
	{
		/* Get the IPv6 address of the previous interface */
		if($infprev!="") 
		{
			$local = INF_getcurripaddr($infprev);
			if($local!="")  $lcmd = " local ".$local;
			$prevstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $infprev, 0);
			$remote = query($prevstsp."/inet/ipv4/ipv4in6/remote");
			startcmd("sip=`gethostip -6 ".$remote."`");

			/* check if remote is acquired */
			startcmd("if [ \"$remote\" == \"\" ]; then");
			startcmd("	sleep 1");
			startcmd("	remote=`xmldbc -w ".$prevstsp."/inet/ipv4/ipv4in6/remote`");
			startcmd("	while [ \"$remote\" == \"\" ]; do" );
			startcmd("		sleep 1");
			startcmd("		remote=`xmldbc -w ".$prevstsp."/inet/ipv4/ipv4in6/remote`");
			startcmd("	done");
			startcmd("	sip=`gethostip -6 $remote`");
			startcmd("fi");

			//startcmd("sip=`gethostip -6 ".$remote."`");
		}
		startcmd("b4addr=`xmldbc -w ".$inetp."/ipv4/ipaddr`");
	
		startcmd("if [ \"$sip\" != \"\" ]; then");
		startcmd("	ip -6 tunnel add ".$devnam." mode ip4ip6 remote $sip".$lcmd);
		startcmd("	xmldbc -s ".$stsp."/inet/ipv4/ipv4in6/remote $sip");
		startcmd("	ip link set dev ".$devnam." up");
		startcmd("	if [ \"$b4addr\" != \"\" ]; then");
		startcmd("		ip -4 addr add $b4addr dev ".$devnam);
		startcmd("	fi");
		startcmd("	ip route add default dev ".$devnam);
		$uptime = query("/runtime/device/uptime");
		startcmd("	xmldbc -s ".$stsp."/inet/uptime ".$uptime);
		startcmd("	xmldbc -s ".$stsp."/inet/ipv4/valid 1");
		startcmd("	event ".$inf.".UP");
		startcmd("	echo 1 > /var/run/".$inf.".UP");
		startcmd("else");
		startcmd("	echo Cannot resolve aftr server name > /dev/console");
		startcmd("	xmldbc -s ".$stsp."/inet/ipv4/valid 0");
		startcmd("fi");

		/* Start script */
		/*
		startcmd("phpsh /etc/scripts/IPV4.INET.php ACTION=ATTACH".
			" STATIC=1".
			" INF=".$inf.
			" DEVNAM=".$ifname.
			" IPADDR=".$ipaddr.
			" MASK=".$mask.
			" GATEWAY=".$gw.
			" MTU=".$mtu.
			' "DNS='.$dns.'"'
		);
		*/
		//startcmd("event ".$inf.".UP");
		//startcmd("echo 1 > /var/run/".$inf.".UP");
		//startcmd("fi");

		/* Stop script */
		stopcmd("if [ -e /var/run/".$inf.".UP ]; then");
		stopcmd("	ip -6 tunnel del ".$devnam);		
		stopcmd("	ip route del default dev ".$devnam);
		stopcmd("	xmldbc -s ".$stsp."/inet/ipv4/valid 0");
		stopcmd("	event ".$inf.".DOWN");
		stopcmd("	rm -f /var/run/".$inf.".UP");
		stopcmd("fi");
		//stopcmd("phpsh /etc/scripts/IPV4.INET.php ACTION=DETACH INF=".$inf);
	}
	
	stopcmd("if [ -f /var/run/dslite_start.pid ]; then");
	stopcmd("	pid=`pfile -f /var/run/dslite_start.pid`");
	stopcmd("	[ \"$pid\" != \"0\" ] && kill $pid > /dev/console 2>&1");
	stopcmd("	rm -rf /var/run/dslite_start.pid");
	stopcmd("fi"); 
}

function main_entry()
{
	if ($_GLOBALS["INF"]=="") return "No INF !!";
	if ($_GLOBALS["ACTION"]=="DSLITE_START" || $_GLOBALS["ACTION"]=="DSLITE_STOP") 
		return inet_ipv4_dslite($_GLOBALS["INF"],$_GLOBALS["IFNAME"],$_GLOBALS["INET"],$_GLOBALS["INETP"],$_GLOBALS["INFPREV"]);
	return "Unknown action - ".$_GLOBALS["ACTION"];
}

/*****************************************/
$ret = main_entry();
if ($ret != "") echo "# ".$ret."\nexit 9\n";
else echo "exit 0\n";
/*****************************************/
?>
