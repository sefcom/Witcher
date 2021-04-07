#!/bin/sh
echo "Reboot in 3 seconds ..."
sleep 1
echo "Reboot in 2 seconds ..."
sleep 1
echo "Reboot in 1 seconds ..."
sleep 1
echo "Rebooting ..."

#The DB save should be finished before reboot.
if [ -f "/var/run/db_saving" ]; then
	for i in 1 2 3 4 5; do
		if [ ! -f "/var/run/db_saving" ]; then
			break
		fi
		sleep 1
	done
fi

if [ "`xmldbc -g /runtime/device/layout`" != "router" ]; then
	reboot
else
	event WAN-1.DOWN add reboot
	event STATUS.CRITICAL
	killall radvd
	service INET.WAN-2 stop
	service INET.WAN-1 stop
	xmldbc -t "reboot:10:reboot"
fi
