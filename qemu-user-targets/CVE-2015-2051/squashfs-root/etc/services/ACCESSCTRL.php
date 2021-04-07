<?
/* VSVR & PFWD are depends on LAN services.
 * Be sure to start LAN services first. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inf.php";
include "/etc/services/IPTABLES/iptlib.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

/* Get all the LAN interface IP address */
IPT_scan_lan();
$lanip	= XNODE_get_var("LAN-1.IPADDR");

if(query("/acl/accessctrl/enable")=="1")
{
	fwrite("a",$START, "iptables -t filter -F FOR_POLICY\n");
	$i=0;
	foreach ("/acl/accessctrl/entry")
	{
		if(query("enable")=="1")
		{
			$i++;
			fwrite("a",$START, "iptables -t filter -N FOR_POLICY_RULE".$i."\n");
			fwrite("a",$START, "iptables -t filter -N FOR_POLICY_FILTER".$i."\n");
			fwrite("a",$STOP,  "iptables -t filter -F FOR_POLICY\n");
			fwrite("a",$STOP,  "iptables -t filter -F FOR_POLICY_RULE".$i."\n");
			fwrite("a",$STOP,  "iptables -t filter -F FOR_POLICY_FILTER".$i."\n");
			fwrite("a",$STOP,  "iptables -t filter -X FOR_POLICY_RULE".$i."\n");
			fwrite("a",$STOP,  "iptables -t filter -X FOR_POLICY_FILTER".$i."\n");		 
			fwrite("a",$START, "iptables -t filter -F FOR_POLICY_RULE".$i."\n");
			fwrite("a",$START, "iptables -t filter -F FOR_POLICY_FILTER".$i."\n");			
			fwrite("a",$START, "iptables -t filter -A FOR_POLICY -j FOR_POLICY_RULE".$i."\n");
			
			foreach ("machine/entry")
			{
				if(query("type")=="IP")	
				{		
					fwrite("a",$START, "iptables -t filter -A FOR_POLICY_RULE".$i." -s ".query("value")." -j FOR_POLICY_FILTER".$i."\n");
					fwrite("a",$START, "iptables -t filter -A FOR_POLICY_RULE".$i." -s ".query("value")." -j ACCEPT\n");
				}
				else if(query("type")=="MAC")	
				{
					fwrite("a",$START, "iptables -t filter -A FOR_POLICY_RULE".$i." -m mac --mac-source ".query("value")." -j FOR_POLICY_FILTER".$i."\n");
					fwrite("a",$START, "iptables -t filter -A FOR_POLICY_RULE".$i." -m mac --mac-source ".query("value")." -j ACCEPT\n");
				}
				else							fwrite("a",$START, "iptables -t filter -A FOR_POLICY_RULE".$i." -j FOR_POLICY_FILTER".$i."\n");
			}	
			
			/* time */
			$sch = query("schedule");
			if ($sch=="") $timecmd = "";
			else $timecmd = IPT_build_time_command($sch);
			
			$iptcmd = "iptables -t filter -A FOR_POLICY_FILTER".$i." ".$timecmd;
			
			/* web access logging */
			$WebLogEnable = query("webfilter/logging");
			$WebLog = " -j LOG --log-level notice --log-prefix 'DRP:008:'";
			
			if(query("portfilter/enable") == "1")
			{
				foreach ("portfilter/entry")
				{
					if(query("enable") == "1")
					{
						$match_iprange = "-m iprange --dst-range ".query("startip")."-".query("endip");
						$dstport = " --dport ".query("startport").":".query("endport");
						if(query("protocol")=="TCP")	$protocol = " -p tcp ";
						else if(query("protocol")=="UDP")	$protocol = " -p udp ";	
						else if(query("protocol")=="ICMP")
						{
							$protocol = " -p icmp ";
							$dstport = "";			
						}	
						else
						{
							$protocol = " -p all ";
							$dstport = "";
						}	
						
						if($WebLogEnable=="1")	fwrite("a",$START, $iptcmd.$protocol.$match_iprange.$dstport.$WebLog."\n");
						fwrite("a",$START, $iptcmd.$protocol.$match_iprange.$dstport." -j DROP\n");
					}
				}
			}
			if(query("webfilter/enable")=="1")
			{
				$webf_policy = query("/acl/accessctrl/webfilter/policy"); //ACCEPT|DROP
				foreach ("/acl/accessctrl/webfilter/entry")
				{	
					$url = query("url");
					if ($url!="")
					{
						$tmpurl = cut($url, 0, "/");
						if($tmpurl == "http:")   $url = scut($url, 0, "http://");
					}
					$urlcmd = " -p tcp --dport 80 -m string --url ".$url;

					//if($WebLogEnable=="1" && $webf_policy=="DROP")	fwrite("a",$START, $iptcmd.$urlcmd.$WebLog."\n");
					//fwrite("a",$START, $iptcmd.$urlcmd." -j ".$webf_policy."\n");
					if($webf_policy=="DROP")
					{
						//log dropped ??
						if($WebLogEnable=="1") fwrite("a",$START, $iptcmd.$urlcmd.$WebLog."\n");
						fwrite("a",$START, $iptcmd.$urlcmd." -j HIJACK --to-url ".$lanip."/info/blockedPage.html\n");
					}
					else 
						fwrite("a",$START, $iptcmd.$urlcmd." -j ACCEPT\n");
				}
				
				//log all ??
				if($WebLogEnable=="1")
				{
					$WebLog = " -j LOG --log-level info --log-prefix 'LOGURL:'";
					fwrite("a",$START, $iptcmd." -p tcp --dport 80 -m string --http_req".$WebLog."\n");
				}

				if($webf_policy == "ACCEPT")
				{
					if($lanip!="")
						fwrite("a",$START, $iptcmd." -p tcp --dport 80 -m string --http_req -j HIJACK --to-url ".$lanip."/info/blockedPage.html\n");
					else
						fwrite("a",$START, $iptcmd." -p tcp --dport 80 -m string --http_req -j DROP\n");
				}
			}
			
			if(query("action")=="LOGWEBONLY")
			{
				$WebLog = " -j LOG --log-level info --log-prefix 'LOGURL:'";
				fwrite("a",$START, $iptcmd." -p tcp --dport 80 -m string --http_req".$WebLog."\n");	  
			}
			else if(query("action")=="BLOCKALL")
			{
				fwrite("a",$START, $iptcmd." -p all -j DROP\n");
			}	
		}	
	}	
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
