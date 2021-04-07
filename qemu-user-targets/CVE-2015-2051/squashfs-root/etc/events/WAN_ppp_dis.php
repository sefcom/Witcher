<?
include "/htdocs/phplib/xnode.php";

echo '#!/bin/sh\n';

if($ACT=="START") 
{
	//Try IO.GUEST and register next parse.
	echo 'cp /etc/scripts/ip-up /etc/ppp/.\n';
	echo 'cp /etc/scripts/ip-down /etc/ppp/.\n';
	echo 'cp /etc/scripts/ppp-status /etc/ppp/.\n';
	echo 'xmldbc -P /etc/events/option.discover.php -V INF='.$INF.' > /var/run/option.discover\n';
	echo 'pppd file /var/run/option.discover &\n';
	echo 'xmldbc -t ppp.dis.guest:7:"sh /etc/events/WAN_ppp_dis.sh WAN-1 DISCOVER"\n';
	echo 'event INFSVCS.'.$INF.'.UP add true\n';
}
else if($ACT=="DISCOVER")
{
	$PADO = query("/runtime/services/wandetect/ppp/".$ACT."/PADO");
	$connected = query("/runtime/services/wandetect/ppp/".$ACT."/connected");
	$authFail = query("/runtime/services/wandetect/ppp/".$ACT."/authFail");

	if($connected=="1" || $PADO =="1" || $authFail=="1" )
	{
		echo 'if [ -f /var/run/ppp-'.$ACT.'.pid ]; then\n';
		echo '	pid=`pfile -f /var/run/ppp-'.$ACT.'.pid`\n';
		echo '	[ "$pid" != "0" ] && kill $pid > /dev/console 2>&1\n';
		echo '	rm -rf /var/run/ppp-'.$ACT.'.pid\n';
		echo 'fi\n';
		set("/runtime/detect/pppoe", "yes");
	}

	if($PADO!="1")
	{
		set("/runtime/detect/pppoe", "no");		
	}
}

echo 'exit 0\n';
?>
