#!/bin/sh
	cfm post netctrl 51?op=2,string_info=$1
	echo "usb umount $1" > /dev/console
exit 1
