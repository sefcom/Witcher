#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
	/* PPTP/L2TP connection */
	if ($INF!="")
	{
		$infp = XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
		
		$inet = query($infp."/inet");
		
		$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
		if (query($inetp."/ppp4/over")=="pptp" || query($inetp."/ppp4/over")=="l2tp")
		{
			$L_INF = query($infp."/lowerlayer");
			$rl_infp= XNODE_getpathbytarget("/runtime", "inf", "uid", $L_INF, 0);
			$l_ip = query($rl_infp."/inet/ipv4/ipaddr");
			$l_mask = query($rl_infp."/inet/ipv4/mask");
			$l_gw = query($rl_infp."/inet/ipv4/gateway");
			$l_dev = query($rl_infp."/devnam");

			if (query($inetp."/ppp4/over")=="pptp")
			{
				$server = query($inetp."/ppp4/pptp/server");
				$domain = query($inetp."/ppp4/pptp/olddomainip");
				$overtype = "pptp";
			}
			else if (query($inetp."/ppp4/over")=="l2tp")
			{
				$server = query($inetp."/ppp4/l2tp/server");
				$domain = query($inetp."/ppp4/l2tp/olddomainip");
				$overtype = "l2tp";
			}
			
			if(INET_validv4addr($server) != 1)
			{
				echo "xmldbc -X ".$inetp."/ppp4/".$overtype."/olddomainip\n";
				echo "for i in 0 1 2\n";
				echo "do\n";
				echo "sip=`gethostip -d ".$server."`\n";
				echo "if [ \"$sip\" != \"\" ]; then\n";
				echo "sed -i \"s/".$server."/$sip/g\" /etc/ppp/options.".$INF."\n";
				echo "phpsh /etc/scripts/vpnroute.php PATH=".$inetp."/ppp4/".$overtype."/olddomainip INF=".$INF." DOMAINIP=".$domain." IP=".$l_ip." SERVER=$sip"." MASK=".$l_mask." DEV=".$l_dev." GW=".$l_gw."\n";
				echo "break\n";
				echo "else\n";
				echo "sleep 1\n";
				echo "fi\n";
				echo "done\n";
			}
			else
			{
				if (INET_validv4network($l_ip, $server, $l_mask) == 1)
				{
					echo "ip route add ".$server." dev ".$l_dev."\n";
				}
				else
				{
					echo "ip route add ".$server." via ".$l_gw." dev ".$l_dev."\n";
				}
			}
		}
	}
?>
