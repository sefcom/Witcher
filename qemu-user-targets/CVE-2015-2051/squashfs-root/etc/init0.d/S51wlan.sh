#!/bin/sh
#the event EXECUTE is for helping execute a script. Check "webincl/body/bsc_wlan.php"
echo [$0]: $1 ... > /dev/console
case "$1" in
start|stop)
	service WIFI.PHYINF $1
	event EXECUTE add "sh /var/run/exec.sh"
	;;
*)
	echo [$0]: invalid argument - $1 > /dev/console
	;;
esac
phpsh etc/scripts/wlan_get_chanlist.php
exit 0
