<?
/* DMZ is depends on LAN services.
 * Be sure to start LAN services first. */
include "/htdocs/phplib/trace.php";

include "/etc/services/IPTABLES/iptlib.php";
include "/htdocs/phplib/phyinf.php";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");

/* Get all the LAN interface IP address */
IPT_scan_lan();

/* refresh the chain of LAN interfaces */
$j = 1;
while ($j>0)
{
	$ifname = "LAN-".$j;
	$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
	if ($ifpath == "") { $j = 0; break; }

	$CHAIN	= "URLF.".$ifname;

	fwrite("a", $START, "iptables -t filter -F ".$CHAIN."\n");		
	fwrite("a", $STOP,  "iptables -t filter -F ".$CHAIN."\n");
	
	XNODE_set_var($CHAIN.".USED", "0");

	if(query("/acl/applications/kaixin/action")=="DENY")
	{
		fwrite("a", $START, "iptables -A ".$CHAIN." -m string --url www.kaixin.com -j LOG --log-level notice --log-prefix 'DRP:007:' \n");
		fwrite("a", $START, "iptables -A ".$CHAIN." -m string --url www.kaixin.com -j DROP \n");
		fwrite("a", $START, "iptables -A ".$CHAIN." -m string --url www.kaixin001.com -j LOG --log-level notice --log-prefix 'DRP:007:' \n");
		fwrite("a", $START, "iptables -A ".$CHAIN." -m string --url www.kaixin001.com -j DROP \n");
		XNODE_set_var($CHAIN.".USED", "1");
	}

	/*Add rule to ifname chain */
	$i = 0;
	$poli = query("/acl/urlctrl/policy");
	if ($poli == "ACCEPT") $policy = "DROP";
	else if ($poli == "DROP") $policy = "ACCEPT";
	$cnt = query("/acl/urlctrl/count");
	if ($cnt=="") $cnt = 0;
	while ($i < $cnt)
	{
		if ($poli == "DISABLE") break;

		$i++;
		anchor("/acl/urlctrl/entry:".$i);
	
		if (query("enable")!="1") continue;

		$url	= query("url");		
		$sch	= query("schedule"); 

		if ($url!="")
		{
			$tmpurl = cut($url, 0, "/");
			if($tmpurl == "http:")   $url = scut($url, 0, "http://");

			if ($sch=="") $timecmd = "";
			else $timecmd = IPT_build_time_command($sch);
			if ($poli == "ACCEPT")
			{

				fwrite("a", $START, "iptables -A ".$CHAIN." ".$timecmd." -m string --url ".$url." -j LOG --log-level notice --log-prefix 'DRP:007:' \n");
				fwrite("a", $START, "iptables -A ".$CHAIN." ".$timecmd." -m string --url ".$url." -j ".$policy." \n");			
			}
			else if ($poli == "DROP")
			{
				/*add  accept rule*/
				fwrite("a", $START, "iptables -A ".$CHAIN." ".$timecmd." -m string --url ".$url." -j RETURN \n");
			}
			XNODE_set_var($CHAIN.".USED", "1");
		}
	}
	if ($poli == "DROP")
	{
		/*add  drop rule*/
		fwrite("a", $START, "iptables -A ".$CHAIN." -m string --http_req -j LOG --log-level notice --log-prefix 'DRP:007:' \n");
		fwrite("a", $START, "iptables -A ".$CHAIN." -m string --http_req -j ".$poli." \n");
	}
	$j++;

}
fwrite("a", $START, "exit 0\n");
fwrite("a", $STOP,  "exit 0\n");
?>
