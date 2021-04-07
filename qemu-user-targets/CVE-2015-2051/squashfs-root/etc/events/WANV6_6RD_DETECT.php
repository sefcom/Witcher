<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

echo '#!/bin/sh\n';
$v4stsp		= XNODE_getpathbytarget("/runtime", "inf", "uid", $V4ACTUID, 0);
$sixrdpfx	= query($v4stsp."/udhcpc/sixrd_pfx");
$v4infp		= XNODE_getpathbytarget("", "inf", "uid", $V4ACTUID, 0);
$v4inet		= query($v4infp."/inet");
$v4inetp	= XNODE_getpathbytarget("/inet", "entry", "uid", $v4inet, 0);
$v6actinfp	= XNODE_getpathbytarget("", "inf", "uid", $V6ACTUID);
$v6actinet	= query($v6actinfp."/inet");
$v6actinetp	= XNODE_getpathbytarget("/inet", "entry", "uid", $v6actinet, 0); 
$infp		= XNODE_getpathbytarget("", "inf", "uid", $INF);
$inet		= query($infp."/inet");
$child		= query($infp."/child");
$inetp		= XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0); 
$pdns		= query($inetp."/ipv6/dns/entry:1");
$sdns		= query($inetp."/ipv6/dns/entry:2");
$dnscnt		= query($inetp."/ipv6/dns/entry:2");

if($sixrdpfx != "")
{
	/* Setup 1 , Native IPv4 & 6rd */
	if($AUTOSET=="1")
	{
		echo 'echo AUTODETECT change to 6RD mode > /dev/console\n';
		echo 'echo \[ Setup 1 - Native IPv4 and 6rd \] - Autoset > /dev/console\n';

		echo 'xmldbc -s '.$v4infp.'/infprevious \"'.$INF.'\"\n';
		echo 'xmldbc -s '.$v4infp.'/infnext \"'.$V6ACTUID.'\"\n';
		echo 'xmldbc -s '.$v6actinfp.'/infprevious \"'.$V4ACTUID.'\"\n';
		echo 'xmldbc -s '.$v6actinfp.'/child \"'.$child.'\"\n';
		echo 'xmldbc -s '.$v6actinfp.'/defaultroute \"1\"\n';
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/mode "6RD"\n';
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/ipv6in4/rd/ipaddr ""\n';
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/ipv6in4/rd/prefix ""\n';
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/ipv6in4/rd/v4mask ""\n';
		echo 'xmldbc -s '.$v6actinetp.'/ipv6/ipv6in4/rd/relay ""\n';
		if($pdns!="")
		{
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:1 '.$pdns.'\n';
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/count '.$dnscnt.'\n';
		}
		if($sdns!="")
		{
			echo 'xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:2 '.$sdns.'\n';
		}
		echo 'event DBSAVE\n';

		echo 'echo "#!/bin/sh" > /var/servd/INET.'.$INF.'_start.sh\n';
		echo 'echo "#!/bin/sh" > /var/servd/INET.'.$INF.'_stop.sh\n';
		echo 'echo "service INET.'.$V4ACTUID.' restart" > /var/servd/INET.'.$INF.'_start.sh\n';
		echo 'echo "service INET.'.$V4ACTUID.' stop" > /var/servd/INET.'.$INF.'_stop.sh\n';
		echo 'echo "xmldbc -X /runtime/services/wandetect6" >> /var/servd/INET.'.$INF.'_stop.sh\n';
		echo 'service INET.'.$V4ACTUID.' restart\n';
	}
	else
	{
		echo 'echo AUTODETECT change to 6RD mode > /dev/console\n';
		echo 'echo \[ Setup 1 - Native IPv4 and 6rd \] > /dev/console\n';
	}
	set("/runtime/services/wandetect6/wantype",	"6RD");
	set("/runtime/services/wandetect6/desc",		"Normal");
}
else
{
	/* clear wandetect6 result */
	echo 'xmldbc -X /runtime/services/wandetect6\n';

	if($AUTOSET=="1")
	{
		$setmsg = 'result=`xmldbc -W /runtime/services/wandetect6/wantype`\n'.
			'echo result is $result > /dev/console\n'.
			//'if [ \"$result\" = \"STATELESS\" || \"$result\" = \"STATEFUL\" ]; then\n'.
			'if [ \"$result\" = \"STATEFUL\" ]; then\n'.
			'	echo AUTODETECT change to AUTO mode > /dev/console\n'.
			
			'	echo \[ Setup 2 - Native IPv4 and Autoconfiguration \] > /dev/console\n'.
            		'   	xmldbc -s '.$v6actinfp.'/infprevious \"'.$INF.'\"\n'.
            		'   	xmldbc -s '.$v6actinfp.'/child \"'.$child.'\"\n'.
            		'   	xmldbc -s '.$v6actinfp.'/defaultroute \"1\"\n'.
            		'   	xmldbc -s '.$v6actinetp.'/ipv6/mode \"AUTO\"\n'.
            		'   	xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:1 \"'.$pdns.'\"\n'.
            		'   	xmldbc -s '.$v6actinetp.'/ipv6/dns/entry:2 \"'.$sdns.'\"\n'.
            		'   	xmldbc -s '.$v6actinetp.'/ipv6/dns/count \"'.$dnscnt.'\"\n'.
            		'   	echo service INET.'.$V4ACTUID.' stop > /var/servd/INET.'.$INF.'_start.sh\n'.
            		'   	echo service INET.'.$V6ACTUID.' restart >> /var/servd/INET.'.$INF.'_start.sh\n'.
            		'   	echo event DBSAVE >> /var/servd/INET.'.$inf.'_start.sh\n'.
            		'   	echo service INET.'.$V6ACTUID.' stop > /var/servd/INET.'.$INF.'_stop.sh\n'.
            		'   	echo xmldbc -X /runtime/services/wandetect6 >> /var/servd/INET.'.$INF.'_stop.sh\n'.
            		'   	service INET.'.$V6ACTUID.' restart\n'.
			'else\n'.
			'	echo AUTODETECT mode : Cannot detect !! > /dev/console\n'.
			'	xmldbc -s /runtime/services/wandetect6/wantype	"unknown"\n'.
			'	xmldbc -s /runtime/services/wandetect6/desc	"No Response"\n'.
			
				/* if result is "unknown", restart INET.WAN-5 */
			//'	wizard=`xmldbc -W /runtime/services/wizard6`\n'.
			//'	if [ \"$wizard\" != \"1\" ]; then\n'.
			//'   	xmldbc -s /inf:12/active \"1\"\n'.
			//'		echo service INET.WAN-5 restart\n'.
			//'	fi\n'.						
            		'fi\n';                                   
	}
	else {$setmsg="";}

	/* wait for RA then send DHCPv6 Solicit */
	echo 'sh /etc/events/WANV6_AUTOCONF_DETECT.sh '.$INF.' CABLESTART\n';	// WANV6_AUTOCONF_DETECT
	$radetsh = "/var/run/".$INF."-radetect.sh";
	fwrite(w, $radetsh, "#!/bin/sh\n");
	fwrite(a, $radetsh,$setmsg);
	echo 'chmod +x '.$radetsh.'\n';
	//echo 'xmldbc -t radet.'.$INF.':40:'.$radetsh.'\n';
	echo 'xmldbc -t radet.'.$INF.':50:'.$radetsh.'\n';
}

echo 'exit 0\n';
?>
