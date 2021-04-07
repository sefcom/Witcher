#!/bin/sh
if [ ! -f "/var/run/chat_xmlnode.conf" ]; then
	devname=`xmldbc -g /runtime/tty/entry:1/cmdport/devname`
	if [ "$devname" != "" ]; then
		for i in 0 1 2 3
		do
			chat -e -v -c -D $devname OK-AT+CIMI-OK 
			if [ -f "/var/run/chat_xmlnode.conf" ]; then
				break
			fi
			echo [$0]... No get Auto config infor,try again[$i]... > /dec/console
		done
	fi	
fi
sim_status=`cat /var/run/chat_xmlnode.conf | grep SIM`

if [ "$sim_status" = "" ];then
	mcc=`cat /var/run/chat_xmlnode.conf | grep mcc=   | scut -f 2`
	mnc1=`cat /var/run/chat_xmlnode.conf | grep mnc_1= | scut -f 2`
	mnc2=`cat /var/run/chat_xmlnode.conf | grep mnc_2= | scut -f 2`

	if [ "$mcc" != "" ] && [ "$mnc1" != "" ] && [ "$mnc2" != "" ];then
		xmldbc -P /etc/events/AUTOCONFIG.php -V mcc=$mcc -V mnc1=$mnc1 -V mnc2=$mnc2 
        fi
fi
exit 0;
