#!/bin/sh
# ensure the file existing
touch /var/run/hotplug_net.txt

# do some records
#date >> /var/run/hotplug_net.txt
#echo [$0] [$1] [$2] [$3] [$4] [$5] [$6] [$7] [$8] [$9] >> /var/run/hotplug_net.txt

# enable interface to associate with AP
if [ "$2" == "add" ]; then
#	echo tell eapd about new interface $1 >> /var/run/hotplug_net.txt
	/usr/sbin/hotplug_net add $1
fi

if [ "$2" == "remove" ]; then
#	echo tell eapd to remove interface $1 >> /var/run/hotplug_net.txt
	/usr/sbin/hotplug_net remove $1
fi
