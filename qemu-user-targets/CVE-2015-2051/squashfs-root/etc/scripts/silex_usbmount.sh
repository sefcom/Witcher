#!/bin/sh
echo [$0] $1 $2 ... > /dev/console

case "$1" in
add)
	/usr/sbin/alpha_sxmount $1 $2
	if [ -f /sys/block/$2/queue/nr_requests ]; then
		echo "64" > /sys/block/$2/queue/nr_requests
		echo "512" > /sys/block/$2/queue/read_ahead_kb
	fi
	echo $2 > /var/usbdev
    ;;
remove)
	/usr/sbin/alpha_sxmount $1 $2
	rm -rf /var/usbdev
	;;
*)
	echo "not support [$1] ..."
    ;;
esac

exit 0


