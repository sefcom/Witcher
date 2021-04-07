<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';
$stsp		= XNODE_getpathbytarget("/runtime", "inf", "uid", $INF, 0);
$aftrname	= query($stsp."/inet/ipv4/ipv4in6/remote");
$v4infp		= XNODE_getpathbytarget("", "inf", "uid", $V4ACTUID, 0);
$v4inet		= query($v4infp."/inet");
$v4inetp	= XNODE_getpathbytarget("/inet", "entry", "uid", $v4inet, 0);
$v6actinfp	= XNODE_getpathbytarget("", "inf", "uid", $V6ACTUID, 0);
$v6actinet	= query($v6actinfp."/inet");
$v6actinetp	= XNODE_getpathbytarget("/inet", "entry", "uid", $v6actinet, 0); 
$infp		= XNODE_getpathbytarget("", "inf", "uid", $INF, 0);
$inet		= query($infp."/inet");
$child		= query($infp."/child");
$inetp		= XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0); 
$pdns		= query($inetp."/ipv6/dns/entry:1");
$sdns		= query($inetp."/ipv6/dns/entry:2");
$dnscnt		= query($inetp."/ipv6/dns/entry:2");

if($aftrname != "")
{
	/* Setup 4 , DS-Lite & Autoconfiguration */
	if($AUTOSET=="1")
	{
		echo 'echo AUTODETECT change to Autoconfiguration mode > /dev/console\n';
		echo 'echo \[ Setup 4 - DS-Lite and Autoconfiguration \]-AUTOSET > /dev/console\n';

		/* DS-Lite */
		echo 'xmldbc -s '.$v4infp.'/infprevious \"'.$V6ACTUID.'\"\n';
		echo 'xmldbc -s '.$v4infp.'/nat \"\"\n';
		echo 'xmldbc -s '.$v4inetp.'/ipv4/ipaddr \"\"\n';
		echo 'xmldbc -s '.$v4inetp.'/addrtype \"ipv4\"\n';
		echo 'xmldbc -s '.$v4inetp.'/ipv4/static \"0\"\n';
		echo 'xmldbc -s '.$v4inetp.'/ipv4/ipv4in6/mode \"dslite\"\n';
		echo 'xmldbc -s '.$v4inetp.'/ipv4/mtu \"1452\"\n';
		echo 'xmldbc -s '.$v4inetp.'/ipv4/ipv4in6/remote \"\"\n';

		/* Autoconfiguration */
		echo 'xmldbc -s '.$v6actinfp.'/infprevious \"'.$INF.'\"\n';                                         
		echo 'xmldbc -s '.$v6actinfp.'/child \"'.$child.'\"\n';                                             
		echo 'xmldbc -s '.$v6actinfp.'/infnext \"'.$V4ACTUID.'\"\n';                                            
		echo 'xmldbc -s '.$v6actinfp.'/defaultroute \"1\"\n';                                            
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/mode \"AUTO\"\n';                                              
		if($pdns!="")
		{
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:1 \"'.$pdns.'\"\n';                                  
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/count \"'.$dnscnt.'\"\n';                                  
		}
		if($sdns!="")
		{
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:2 \"'.$sdns.'\"\n';
		}
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/count \"'.$dnscnt.'\"\n';                                  

		echo 'service INET.'.$V4ACTUID.' stop \n';               
		echo 'echo "service INET.'.$V6ACTUID.' restart" >> /var/servd/INET.'.$INF.'_start.sh\n';               
		echo 'echo "event DBSAVE" >> /var/servd/INET.'.$inf.'_start.sh\n';                                    
		echo 'echo "service INET.'.$V6ACTUID.' stop" > /var/servd/INET.'.$INF.'_stop.sh\n';                   
		echo 'echo "xmldbc -X /runtime/services/wandetect6" >> /var/servd/INET.'.$INF.'_stop.sh\n';           
		echo 'echo "rm -f /var/run/'.$INF.'.UP" >> /var/servd/INET.'.$INF.'_stop.sh\n';           
		echo 'service INET.'.$V6ACTUID.' restart\n';      
	}
	else
	{
		echo 'echo AUTODETECT change to Autoconfiguration mode > /dev/console\n';
		echo 'echo \[ Setup 4 - DS-Lite and Autoconfiguration \] > /dev/console\n';
	}
	set("/runtime/services/wandetect6/wantype",	"DSLITE");
	set("/runtime/services/wandetect6/desc",		"Normal");
}
else
{
	/* clear wandetect6 result */
	//echo 'xmldbc -X /runtime/services/wandetect6\n';

	if($AUTOSET=="1")
	{
		echo 'echo AUTODETECT change to Autoconfiguration mode > /dev/console\n';
		echo 'echo \[ Setup 3 - N/A and Autoconfiguration \]-AUTOSET > /dev/console\n';

		/* Autoconfiguration */
		echo 'xmldbc -s '.$v6actinfp.'/infprevious \"'.$INF.'\"\n';                                        
		echo 'xmldbc -s '.$v6actinfp.'/child \"'.$child.'\"\n';                                             
		echo 'xmldbc -s '.$v6actinfp.'/defaultroute \"1\"\n';                                             
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/mode \"AUTO\"\n';                                              
		if($pdns!="")
		{
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:1 \"'.$pdns.'\"\n';                                  
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/count \"'.$dnscnt.'\"\n';                                  
		}
		if($sdns!="")
		{
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:2 \"'.$sdns.'\"\n';
		}
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/count \"'.$dnscnt.'\"\n';                                  
		echo 'echo "service INET.'.$V6ACTUID.' restart" > /var/servd/INET.'.$INF.'_start.sh\n';               
		echo 'echo "event DBSAVE" >> /var/servd/INET.'.$inf.'_start.sh\n';                                    
		echo 'echo "service INET.'.$V6ACTUID.' stop" > /var/servd/INET.'.$INF.'_stop.sh\n';                   
		echo 'echo "xmldbc -X /runtime/services/wandetect6" >> /var/servd/INET.'.$INF.'_stop.sh\n';           
		echo 'echo "rm -f /var/run/'.$INF.'.UP" >> /var/servd/INET.'.$INF.'_stop.sh\n';           
		echo 'service INET.'.$V6ACTUID.' restart\n';      
	}
	else
	{
		echo 'echo AUTODETECT change to Autoconfiguration mode > /dev/console\n';
		echo 'echo \[ Setup 3 - N/A and Autoconfiguration \] > /dev/console\n';
	}
	set("/runtime/services/wandetect6/wantype",	"STATEFUL");
	set("/runtime/services/wandetect6/desc",		"Normal");
}

echo 'exit 0\n';
?>
