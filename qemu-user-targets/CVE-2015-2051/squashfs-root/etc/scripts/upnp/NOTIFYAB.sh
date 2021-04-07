#!/bin/sh
UPNPMSG=`xmldbc -g /runtime/upnpmsg`
[ "$UPNPMSG" = "" ] && UPNPMSG="/dev/null"
echo "[$0]: [$1] [$2] [$3] [$4] [$5] [$6] ..." > $UPNPMSG
if [ $# -ne 6 ]; then
	echo "[$0] need 6 arguments !!" > /dev/null
	exit 9;
fi

if [ ! -f /var/run/notify.$1.$2.sh ]; then
	xmldbc -P /etc/scripts/upnp/NOTIFYAB.php -V NTS=$1 -V PHYINF=$3 -V IPADDR=$4 -V IPTYPE=$6 > /var/run/notify.$1.$2.sh
fi
xmldbc -k upnp_alive_$2
sh /var/run/notify.$1.$2.sh
if [ "$1" = "ssdp:alive" ]; then
	xmldbc -t "upnp_alive_$2:$5:event UPNP.ALIVE.$2"
else
	rm /var/run/notify.*.$2.sh
fi
