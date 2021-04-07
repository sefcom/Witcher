#!/bin/sh
# Netfilter must be modified in this mechanism.
echo [$0][start] > /dev/console
PROC=/proc/sys/net/ipv4/netfilter/ip_conntrack_icmp_timeout
ICMP=`cat /proc/sys/net/ipv4/netfilter/ip_conntrack_icmp_timeout`
echo 0 > $PROC
sleep 5
echo $ICMP > $PROC
echo [$0][finish] > /dev/console
exit 0
