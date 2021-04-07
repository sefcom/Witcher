<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';

$layout = query("/runtime/device/layout");
del("/runtime/services/wandetect6");

if ($layout=="bridge")
{
	/* There is no wan when Device works on bridge mode. */
	set("/runtime/services/wandetect6/wantype", "None");
	set("/runtime/services/wandetect6/desc", "Bridge Mode");
}
else
{
	$v4infp = XNODE_getpathbytarget("", "inf", "uid", $INFV4, 0);
	$phyinf = query($v4infp."/phyinf");
	$phyinfp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phyinf, 0);
	$linkstatus = query($phyinfp."/linkstatus");
	$v4inet = query($v4infp."/inet");
	$v4inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $v4inet, 0);
	$v4mode = query($v4inetp."/addrtype");			
	
	if($v4mode=="ppp4") $over = query($v4inetp."/ppp4/over");
	if($v4mode=="ppp4" && $over=="eth") $V4isPPP = 1;
	if ($linkstatus == "")
	{
		/* We can't determine wan type if linkdown. */
		set("/runtime/services/wandetect6/wantype", "None");
		set("/runtime/services/wandetect6/status", "Link Down");
	}
	else
	{				
		// Set flag "wizard" (= 1) that represent the mode is determined by wizard in web.
		set("/runtime/services/wizard6", "1");		
		
		$inf 	= $INFV6AUTO;	// $inf -> $INFV6AUTO (WAN-5), by Jerry Kao.
		$v4actuid 	= $INFV4;	// used in wizard, 大M.
		$v6actuid 	= $INFV6;
				
		$infp   = XNODE_getpathbytarget("", "inf", "uid", $inf, 0);	
		$inet   = query($infp."/inet");
		$inetp  = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
						
		//$v4actuid = query($inetp."/ipv6/detectuid/v4actuid");
		//$v6actuid = query($inetp."/ipv6/detectuid/v6actuid");												
		$v4stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $v4actuid, 0);					
		
		if($V4isPPP==1 || $v4mode=="ppp10")		// $INFV4 = WAN-1, $AUTOINFV6 = WAN-5
		{
			//$llinfp = XNODE_getpathbytarget("", "inf", "uid", $INFLL, 0);															
			$llinfp = $INFLL;	// 網頁會用到 for ppp10, by 大M.																																					    			
			
			TRACE_debug("== Jerry_inf_ppp = ".$inf);			
			
			/* Detect PPPOE server */
			echo 'sh /etc/events/WANV6_ppp_dis.sh '.$inf.' START '. $INFV4 .'\n'; // WANV6_ppp_dis.sh 			                                                           	  			
						
			$pppautodetsh   = "/var/run/".$inf."-autodetect.sh";	
			
			fwrite(w, $pppautodetsh, "#!/bin/sh\n");	
			fwrite(a, $pppautodetsh,
				'result=`xmldbc -w /runtime/services/wandetect6/wantype`\n'.
				'desc=`xmldbc -w /runtime/services/wandetect6/desc`\n'.
				'echo pppoedetect result is $result > /dev/console\n'.
				'if [ "$result" == "PPPoE" ]; then\n'.							// if IPv6 $result" == "PPPoE"
				'	echo AUTODETECT change to ppp10 mode > /dev/console\n'.
				'	echo Setup 6 - PPPoE and PPPoEv6 > /dev/console\n'.	
				'fi\n'.
				'if [ "$desc" == "No RA DHCP6" ]; then\n'.
				'	echo Not receive RA or DHCP-PD !! > /dev/console\n'.		
				'	sh /etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$inf.' NO_DHCP6_START\n'.
				'fi\n'
			);
			
			echo 'chmod +x '.$pppautodetsh.'\n';
			echo 'xmldbc -t pppdetect.'.$inf.':80:'.$pppautodetsh.'\n';		// 秒數拉長, 接到原來的 v4 PPP = Yes.			
		}
		else
		{																					
			TRACE_debug("== Jerry_inf_notppp = ".$inf);											
			
			echo 'echo IPv4 is not PPPoE Mode > /dev/console\n';			
	
			echo 'sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$inf.' START\n'; 
			
			$autodetsh   = "/var/run/".$inf."-autodetect.sh";
			
			fwrite(w, $autodetsh, "#!/bin/sh\n");	
			fwrite(a, $autodetsh,
				'result=`xmldbc -w /runtime/services/wandetect6/wantype`\n'.
	
				'echo First check if we receive RA or not > /dev/console\n'.
				'echo result is $result > /dev/console\n'.
	
				'if [ "$result" != "unknown" ]; then\n'.						// If Received RA.
				'	echo RA detected!! > /dev/console\n'.			
				'	ipaddr=`xmldbc -w '.$v4stsp.'/inet/ipv4/ipaddr`\n'.
				'	if [ $ipaddr ]; then\n'.										// if get $ipaddr of IPv4.
				'		echo Have IPv4 address !! > /dev/console\n'.
				'		sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$inf.' DHCP6START\n'.		// $ACT = DHCP6START
				'		echo "#!/bin/sh"											 									 > /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "result1=`xmldbc -w /runtime/services/wandetect6/wantype`" 									>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "echo result is \\"$result1\\" > /dev/console" 											>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "if [ \\"$result1\\" != \\"unknown\\" ]; then" 											>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "		echo AUTODETECT change to AUTO mode > /dev/console"									>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "		echo \[ Setup 2 - Native IPv4 and Autoconfiguration \]	> /dev/console"				>> /var/run/'.$inf.'-dhcp6det.sh\n'.																
				'		echo "else"									 													>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "		echo Not receive DHCP-PD for Setup 1 > /dev/console"								>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "		sh /etc/events/WANV6_6RD_DETECT.sh '.$inf.' '.$v4actuid.' '.$v6actuid.' 0"			>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		echo "fi" 																						>> /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		chmod +x /var/run/'.$inf.'-dhcp6det.sh\n'.
				'		xmldbc -t dhcp6det.'.$inf.':40:/var/run/'.$inf.'-dhcp6det.sh\n'.
				'	else\n'.
						//Setup 3 & Setup 4 - DS-Lite
				'		echo Have no IPv4 address !! > /dev/console\n'.
				'		echo Send DHCPv6 Solicit with DS-Lite option > /dev/console\n'.
				'		sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$inf.' DHCP6DSSTART\n'.		// $ACT = DHCP6DSSTART with dslite option
				'		echo "#!/bin/sh"											 									 > /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "result2=`xmldbc -w /runtime/services/wandetect6/wantype`"	 									>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "echo result is \\"$result2\\" > /dev/console" 												>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "if [ \\"$res\\" != \\"unknown\\" ]; then" 												>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "		echo Receive DHCP-PD - Check DS-Lite option > /dev/console"							>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "		sh /etc/events/WANV6_DSLITE_DETECT.sh '.$inf.' '.$v4actuid.' '.$v6actuid.' 0"		>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "else"									 													>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "		echo Not receive DHCP-PD - detection failed !! > /dev/console"						>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "		xmldbc -s /runtime/services/wandetect6/wantype \"unknown\""							>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		echo "		xmldbc -s /runtime/services/wandetect6/desc	  \"No Response\""						>> /var/run/'.$inf.'-dslitedet.sh\n'.			
				'		echo "fi" 																						>> /var/run/'.$inf.'-dslitedet.sh\n'.
				'		chmod +x /var/run/'.$inf.'-dslitedet.sh\n'.
				'		xmldbc -t dslitedet.'.$inf.':30:/var/run/'.$inf.'-dslitedet.sh\n'.
				'	fi\n'.
			
				'else\n'.
				// Get RA after 10s = No (in Cable network).
				'	echo Cannot detect RA for 10 seconds, for Setup 1 !! \n'.
				'	sh /etc/events/WANV6_6RD_DETECT.sh '.$inf.' '.$v4actuid.' '.$v6actuid.' 0\n'.			
				'fi\n'
			);

			echo 'chmod +x '.$autodetsh.'\n';
			echo 'xmldbc -t autodetect.'.$inf.':80:'.$autodetsh.'\n';				
		}
	}
}
echo 'exit 0\n';
?>
