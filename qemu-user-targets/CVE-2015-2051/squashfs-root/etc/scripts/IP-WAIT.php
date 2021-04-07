#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

function cmd($cmd)	{echo $cmd."\n";}
function msg($msg)	{cmd("echo [IP-WAIT]: ".$msg." > /dev/console");}
function error($m)	{cmd("echo [IP-WAIT]: ERROR: ".$m); return 9;}

/***********************************************************************/
function main_entry($inf, $phyinf, $devnam, $dns, $me)
{
	/* INF status path. */
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	if ($stsp=="") return error($inf." has not runtime nodes !");

	$infprev = query($stsp."/infprevious");
	if($infprev!="")
	{
		$prevstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $infprev, 0);
		$prevdevnam = query($prevstsp."/devnam");
		$prevphyinf = query($prevstsp."/phyinf");
	}

	/* Preparing & Get M flag */
	if(strstr($prevdevnam,"ppp")=="") $conf	= "/var/run/".$devnam;
	else $conf = "/var/run/".$prevdevnam;
	$mflag	= fread("e", $conf.".ra_mflag");

	/* generate callback script */
	$hlp = "/var/servd/".$inf."-rdisc6.sh";
	$pid = "/var/servd/".$inf."-rdisc6.pid";

	$child = query($stsp."/child/uid");

	if($mflag=="") /* we don't receive ra */
	{
		//msg("mflag is null, kill rdisc6");
		/* kill daemon */
		cmd("/etc/scripts/killpid.sh ".$pid);
	
		/* check if interface if ppp */
		if(strstr($prevdevnam,"ppp")=="")
		{
			if(strstr($prevdevnam,"sit")=="")
				cmd("rdisc6 -c ".$hlp." -p ".$pid." -q ".$devnam." &");	
			else
			{
				//msg("SIT-Autoconf mode");
				/* auto in sit mode */
				$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $prevphyinf, 0);
				if($p!="") $ipaddr = query($p."/ipv6/link/ipaddr");
				else return error("SIT tunnel need exist!! ");
				msg("ipaddr: ".$ipaddr." and devnam: ".$prevdevnam);
				cmd("rdisc6 -c ".$hlp." -p ".$pid." -q -e ".$ipaddr." ".$prevdevnam." &");	
			}
		}
		else
		{
			//msg("PPP-Autoconf mode");
			/* need infprev */
			$infprev = query($stsp."/infprevious");
			$prevstsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $infprev, 0);
			/* need infprev */
			$atype = query($prevstsp."/inet/addrtype");
			if($atype == "ipv6")
			{
				$ipaddr = query($prevstsp."/inet/ipv6/ipaddr");/* share session with ipv4*/
			}
			else if($atype == "ppp6")
			{  	
				$ipaddr = query($prevstsp."/inet/ppp6/local");/* only ipv6 session */
			}
			else return error($inf." has wrong addrtype of infprevious");
	
			cmd("rdisc6 -c ".$hlp." -p ".$pid." -q -e ".$ipaddr." ".$prevdevnam." &");	
		}
		//cmd("sleep 5");
		cmd('xmldbc -t "ra.iptest.'.$inf.':10:'.$me.'"');
		return 0;
	}

	/* Preparing & Get M flag */
	$mflag	= fread("e", $conf.".ra_mflag");
	$oflag	= fread("e", $conf.".ra_oflag"); 
	$rdnss  = fread("e", $conf.".ra_rdnss");
	$dnssl  = fread("e", $conf.".ra_dnssl");
	$mtu    = fread("e", $conf.".ra_mtu");
	$routerlft = fread("e", $conf.".ra_routerlft");
	
	TRACE_error("----------- IP-WAIT.php inf=".$inf.", conf=".$conf.", mtu=".$mtu.", routerlft=".$routerlft.", mflag=".$mflag);
	
	if (isdigit($routerlft) == "1")
	{
		if ($routerlft < 60)	{ $routerlft = 60; }
		if ($routerlft > 90*60)	{ $routerlft = 90*60; }
		XNODE_set_var($inf."_ROUTERLFT",$routerlft);
	}
	if (isdigit($mtu) == "1")
	{
		if ($mtu < 1280)	{ $mtu = 1280; }
		if ($mtu > 1492)	{ $mtu = 1492; }	// for IPv6 over ethernet case.
		XNODE_set_var($inf."_MTU",$mtu);
	}

	/* need dnssl info */
	if($dnssl!="")
	{
		msg("DNSSL :".$dnssl);
		XNODE_set_var($child."_DOMAIN",$dnssl);
	}
	
	if($mflag!="")
	{
		if(strstr($prevdevnam,"ppp")=="") msg($inf."/".$devnam.", M=[".$mflag."], O=[".$oflag."]");
		else msg($inf."/".$prevdevnam.", M=[".$mflag."], O=[".$oflag."]");
	}

	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	if ($p=="") return error($phyinf." has not runtime nodes!");

	if ($mflag!="")
	{
		/* we got ra */
		$mac = PHYINF_getphymac($inf);
		$hostid = ipv6eui64($mac);
		$ra_prefix = fread("e", $conf.".ra_prefix");
		$prefix = fread("e", $conf.".ra_prefix_len");
		$ra_saddr = fread("e", $conf.".ra_saddr");
		$ipaddr = ipv6ip($ra_prefix, $prefix, $hostid, 0, 0);
		$router	= fread("e", $conf.".ra_saddr");

		if(strstr($prevdevnam,"ppp")!="")
		{
			$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $prevphyinf, 0);
			$llipaddr = query($p."/ipv6/link/ipaddr");
			$hostid = ipv6hostid($llipaddr,64);
			$ipaddr = ipv6ip($ra_prefix, $prefix, $hostid, 0, 0);
		}
		//if ($ipaddr!="")
		if ($ra_saddr!="")
		{
			if ($oflag=="0" && $dns=="" && $rdnss!="") {$dns=$rdnss;}

			$defrt = query($stsp."/defaultroute");

			if($defrt!="" && $defrt>0 && $router!="")
			{
				if(isfile("/var/run/wan_ralft_zero")!=1)
				{
					/* add default route and runtime node*/
					$gateway = query($stsp."/inet/ipv6/gateway");
					if($gateway!="")
					{  
						cmd("ip -6 route del default via ".$gateway." dev ".$devnam." metric ".$defrt);
						del($stsp."/inet/ipv6/gateway");
					}
					cmd("ip -6 route add default via ".$router." dev ".$devnam." metric ".$defrt);
				}

				//set($stsp."/inet/ipv6/gateway",$router);
				setattr($stsp."/inet/ipv6/gateway", "get", "ip -6 route show default | scut -p via");

				/* Replace runtime dynamic route */
				/*
				$base = "/runtime/dynamic/route6";
				$cnt = query($base."/entry#");
				$i=1;
				$found=0;
				while($i<=$cnt)
				{
					$dest  = query($base."/entry:".$i."/ipaddr");
					$pfx   = query($base."/entry:".$i."/prefix");
					$dname = query($base."/entry:".$i."/inf");
					if($dest=="::" && $pfx=="0" && $dname==$inf)
					{
						set($base."/entry:".$i."/gateway", $router);
						$found=1;
						break;
					}
					$i++;
				}

				if($found=="0")
				{
					set($base."/entry:".$i."/ipaddr", "::");
					set($base."/entry:".$i."/prefix", "0");
					set($base."/entry:".$i."/gateway", $router);
					set($base."/entry:".$i."/metric", $defrt);
					set($base."/entry:".$i."/inf", $inf);
				}
				*/

				if($child!="")
				{
					set($stsp."/stateless/ipaddr", $ipaddr);
					set($stsp."/stateless/prefix", $prefix);
					//set($stsp."/stateless/gateway", $router);
					setattr($stsp."/stateless/gateway", "get", "ip -6 route show default | scut -p via");
					set($stsp."/stateless/dns",     $dns);
				}
				if($child=="" && isfile("/var/run/".$inf.".UP")!=1)
				{
					cmd("phpsh /etc/scripts/IPV6.INET.php ACTION=ATTACH".
							" MODE=STATELESS".
							" INF=".$inf.
							" DEVNAM=".$devnam.
							" IPADDR=".$ipaddr.
							" PREFIX=".$prefix.
							" GATEWAY=".$router.
							' "DNS='.$dns.'"');
				}
			}

		}
		return 0;
	}

	/* Not configured, try later. */
	//cmd("killall rdisc6");
	//cmd("/etc/scripts/killpid.sh /var/servd/".$inf."-rdisc6.pid");
	//msg("mflag is null, kill rdisc6-part2");
	//cmd("/etc/scripts/killpid.sh ".$pid);
	//cmd("sleep 1");
	//cmd('xmldbc -t "ra.iptest.'.$inf.':5:'.$me.'"');
	//cmd('xmldbc -t "ra.iptest.'.$inf.':10:'.$me.'"');

	//return 0;
}

/* Main entry */
main_entry(
	$_GLOBALS["INF"],
	$_GLOBALS["PHYINF"],
	$_GLOBALS["DEVNAM"],
	$_GLOBALS["DNS"],
	$_GLOBALS["ME"]
	);
?>
