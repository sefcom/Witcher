#!/bin/sh
#Usage: sh usbmount_fsid.sh sda1 
sfdisk -l 2>/dev/null|scut -p$1|grep "*" > /dev/null
if [ $? -eq 0 ]; then
	echo `sfdisk -l 2>/dev/null|scut -p$1 -f6`
else
	echo `sfdisk -l 2>/dev/null|scut -p$1 -f5`
fi
