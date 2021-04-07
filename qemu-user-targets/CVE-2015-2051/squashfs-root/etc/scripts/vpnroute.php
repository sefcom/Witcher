#!/bin/sh
<?
include "/htdocs/phplib/inet.php";
echo "sleep 1\n";
echo "sed -i \"s/".$DOMAINIP."/".$SERVER."/g\" /etc/ppp/options.".$INF."\n";
echo "xmldbc -s ".$PATH." ".$SERVER."\n";
if (INET_validv4network($IP, $SERVER, $MASK) == 1)
{
	echo "ip route add ".$SERVER." dev ".$DEV."\n";
}
else
{
	echo "ip route add ".$SERVER." via ".$GW." dev ".$DEV."\n";
}
?>
