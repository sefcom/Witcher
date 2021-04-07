#!/bin/sh
xmldbc -s /runtime/mydlink/userlist/suspend 1
sleep 1
cat /proc/net/arp | grep br0 > /var/run/arp
entry=`cat /var/run/arp`
while [ -n "$entry" ]
do
	ipaddr=`echo $entry | cut -d" " -f1`
	xmldbc -P /etc/scripts/mydlink/useradd.php -V IPADDR="$ipaddr"
	sed -i "1d" /var/run/arp
	entry=`cat /var/run/arp`
done

idx=1
cnt=`xmldbc -g /runtime/mydlink/userlist/entry#`
if [ -n "$cnt" ]; then
while [ $idx -le $cnt ]
do
	ipaddr=`xmldbc -g /runtime/mydlink/userlist/entry:$idx/ipaddr`
	macaddr=`xmldbc -g /runtime/mydlink/userlist/entry:$idx/macaddr`
	newmacaddr=`arpping -i br0 -t $ipaddr`
	if [ "$newmacaddr" == "no" ]; then
		xmldbc -X /runtime/mydlink/userlist/entry:$idx/
		cnt=`expr $cnt - 1`
	else
			xmldbc -s /runtime/mydlink/userlist/entry:$idx/macaddr $newmacaddr
#			if [ -z "$macaddr" ]; then
#				hostname=`xmldbc -g /runtime/mydlink/userlist/entry:$idx/hostname`
#				usockc /var/mydlinkeventd_usock NEW_DEVICE,$newmacaddr,"$hostname"
#			fi
	idx=`expr $idx + 1`
	fi
done
fi
xmldbc -s /runtime/mydlink/userlist/suspend 0
xmldbc -t userlookup:300:"sh /etc/scripts/mydlink/userlookup.sh"
