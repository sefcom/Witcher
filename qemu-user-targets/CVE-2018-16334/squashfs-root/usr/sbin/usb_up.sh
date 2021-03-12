#!/bin/sh
	number_of_mount=0
	is_mounted=0
	while [ $is_mounted -eq 0 -a $number_of_mount -lt 3 ]
	do
		if [ $number_of_mount -ne 0 ] ; then
			echo "try to mount $1 again....." > /dev/console
		fi
		
		cfm post netctrl 51?op=1,string_info=$1
		echo "3" > /proc/sys/vm/drop_caches;
		if [ $? -ne 0 ];then 
			echo " mount $1 failed ." > /dev/console
		else
			is_mounted=1
		fi
		number_of_mount=$(($number_of_mount+1))
		if [ $number_of_mount -eq 2 ] ; then
			sleep 2
		fi
	done

	if [ $is_mounted -eq 0 ] ; then
		echo "mount $1 failed." > /dev/console
		exit 1
	fi
	
