#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	[ -d /var/home ] || mkdir /var/home
	echo -n > /var/etc/passwd
	echo -n > /var/etc/group
	echo -n > /var/etc/shadow
	service DEVICE.ACCOUNT start
else
	service DEVICE.ACCOUNT stop
fi
