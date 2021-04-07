#!/bin/sh
# script_name, action, devname, part num, fs_type, path
echo [$0] $1 $2 $3 $4 $5 > /dev/console
suffix="`echo $2|tr "[a-z]" "[A-Z]"`$3"
if [ "$3" = "0" ]; then
	dev=$2
else
	dev=$2$3
fi
if [ "$1" = "add" ]; then
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="add" -V prefix=$2 -V pid=$3 -V fs=$4 -V mntp="$5"
	# we run df then update extened node to avoid stuck by df while browser is required disk nodes
	df > /dev/null
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="update" -V prefix=$2 -V pid=$3 -V size=`df|scut -p$dev -f1` -V size_used=`df|scut -p$dev -f2` -V size_ava=`df|scut -p$dev -f3`

	event MOUNT.$suffix add "usbmount mount $dev"
	event MOUNT.ALL add "phpsh /etc/events/MOUNT.ALL.php action=MOUNT"
	event UNMOUNT.$suffix add "usbmount unmount $dev"
	event UNMOUNT.ALL add "phpsh /etc/events/MOUNT.ALL.php action=UNMOUNT"
	event FDISK.`echo $2|tr [a-z] [A-Z]` add "sfdisk /dev/$2 < /var/run/`echo $2|tr [a-z] [A-Z]`.conf"
	event FORMAT.$suffix add "phpsh /etc/events/FORMAT.php dev=$dev action=try_unmount counter=30"
	event DISKUP $suffix
	#in spin down mode, paragon driver will crash, we try to keep disk awake (tom, 20140124)
	/etc/scripts/keepawake.sh keepawake_$2$3 $5 &
elif [ "$1" = "remove" ]; then
	event MOUNT.$suffix add true
	event UNMOUNT.$suffix add true
	event FORMAT.$suffix add true
	event FDISK.`echo $2|tr "[a-z]" "[A-Z]"` add true
	event DISKDOWN $suffix
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="remove" -V prefix=$2 -V pid=$3
	#in spin down mode, paragon driver will crash, we try to keep disk awake (tom, 20140124)
	kill -9 `cat /tmp/keepawake_$2$3.pid`
	rm -f /tmp/keepawake_$2$3.pid
elif [ "$1" = "mount" ]; then
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="mount" -V prefix=$2 -V pid=$3 -V fs=$4
	# we run df then update extened node to avoid stuck by df while browser is required disk nodes
	df > /dev/null
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="update" -V prefix=$2 -V pid=$3 -V size=`df|scut -p$dev -f1` -V size_used=`df|scut -p$dev -f2` -V size_ava=`df|scut -p$dev -f3`

	event DISKUP $suffix
elif [ "$1" = "unmount" ]; then
	event DISKDOWN $suffix
	phpsh /etc/scripts/usbmount_helper.php action="detach" prefix=$2 pid=$3
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="unmount" -V prefix=$2 -V pid=$3
elif [ "$1" = "detach" ]; then
	phpsh /etc/scripts/usbmount_helper.php action="detach" prefix=$2 pid=$3 mntp="$4"
fi
	phpsh /etc/scripts/webaccess_map.php
exit 0
