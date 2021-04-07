<?
include "/htdocs/phplib/xnode.php";

echo '#!/bin/sh\n';

if($ACT=="START")
{
	//Try IO.GUEST and register next parse.
	echo 'cp /etc/scripts/ip-up /etc/ppp/.\n';
	echo 'cp /etc/scripts/ip-down /etc/ppp/.\n';
	echo 'cp /etc/scripts/ppp6-status /etc/ppp/.\n';
	echo 'xmldbc -P /etc/events/option6.discover.php -V INF='.$INF.' -V INFV4='.$INFV4.' > /var/run/option6.discover\n';
	echo 'pppd file /var/run/option6.discover &\n';
	echo 'xmldbc -t ppp.dis.guest:30:"sh /etc/events/WANV6_ppp_dis.sh '.$INF.' DISCOVER"\n';
	//echo 'event INFSVCS.'.$INF.'.UP add true\n';
}
else if($ACT=="DISCOVER")	// IPv6CP negotiation.
{
	$PADO 		= query("/runtime/services/wandetect6/ppp/".$ACT."/PADO");
	$authFail 	= query("/runtime/services/wandetect6/ppp/".$ACT."/authFail");
	$connected 	= query("/runtime/services/wandetect6/ppp/".$ACT."/connected");		// write by ppp6_status.php

	if($connected == "1")
	{
		/* Detect broadband network */
		echo 'echo IPv6CP is successful > /dev/console\n';
		echo '/etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' START\n';
		//echo 'sleep 15\n';
		
	}
	else	// Add Successful IPv6CP = No, by Jerry_Kao.
	{
		/* Detect WAN v6 type is Auto or not ? For Setup 5, added by Jerry_Kao. */
		echo 'echo IPv6CP is not successful > /dev/console\n';
		echo '/etc/events/WANV6_PPP_AUTOCONF_DETECT.sh '.$INF.' NO_DHCP6_START\n';
	}

	/*
	if($PADO!="1")
	{
		echo 'event WANV6.AUTOCONF.DETECT\n';	
	}
	*/
}

echo 'exit 0\n';
?>
