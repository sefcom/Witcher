#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ -d "/proc/alpha/" ]; then
	if [ -d "/var/proc/" ]; then
		echo "/var/proc already exists. Cancel create symbolic link.. please check ..!! " > /dev/console 
	else
		echo "Create /var/proc/alpha symbolic link..." > /dev/console 
		mkdir /var/proc
		mkdir /var/proc/alpha
		ln -s /proc/alpha/multicast_br0 /var/proc/alpha
		ln -s /proc/alpha/multicast_br1 /var/proc/alpha
		ln -s /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat /var/proc/alpha
		ln -s /proc/alpha/hnat /var/proc/alpha
		ln -s /proc/nf_conntrack_flush /var/proc/alpha
	fi
fi 

