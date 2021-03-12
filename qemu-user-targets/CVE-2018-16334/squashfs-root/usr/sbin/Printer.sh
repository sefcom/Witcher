#!/bin/sh
	echo $1 >/dev/console
	if [ !$(grep -m 1 "Cls=07" /proc/bus/usb/devices) ] ; then
                if [ $(cat /etc/printer_switch) == 1 -a $1 == "remove" ] ; then
			echo 0 > /etc/printer_switch
			echo "usb printer remove." > /dev/console
			cfm post netctrl 51?op=9
		fi
		exit 1
        else
                if [ $1 == "add" ] ; then
			echo "usb printer add." > /dev/console
			echo 1 > /etc/printer_switch
			cfm post netctrl 51?op=8
		else
			echo "usb printer remove." > /dev/console
			echo 0 > /etc/printer_switch
			cfm post netctrl 51?op=9
		fi	
                exit 1
        fi
        exit 1	