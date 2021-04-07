#!/bin/sh
<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";
include "/htdocs/webinc/config.php";

if($ACTION=="break") // If the router is not longer factory default.
{
	//Free the DNS request.
	echo 'iptables -t nat -D PREROUTING -p tcp --dport 53 -j REDIRECT --to-ports 53\n';
	echo 'iptables -t nat -D PREROUTING -p udp --dport 53 -j REDIRECT --to-ports 53\n';
	echo 'iptables -t nat -D PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 80\n';
	echo 'service DNS restart\n';	
}	
else
{
	//If the router is factory default, any DNS request should return router LAN IP address and then access the wizard page.
	// /etc/service/DNS.php should build dnsmasq --address=/#/router IP addrress
	echo "iptables -t nat -D PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 80\n";
	echo "iptables -t nat -I PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 80\n";
	
   	echo "iptables -t nat -D PREROUTING -p udp --dport 53 -j REDIRECT --to-ports 53\n";
    echo "iptables -t nat -I PREROUTING -p udp --dport 53 -j REDIRECT --to-ports 53\n";
    
	echo "iptables -t nat -D PREROUTING -p tcp --dport 53 -j REDIRECT --to-ports 53\n";
	echo "iptables -t nat -I PREROUTING -p tcp --dport 53 -j REDIRECT --to-ports 53\n";	
}	
?>
exit 0
