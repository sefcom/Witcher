#!/bin/sh

#add wireless interface name to proc for management multicast packet data to unicast data
if [ -f "/proc/alpha/m2u" ]; then
	echo "wifig0 wifia0 wifig0.1 wifia0.1" > /proc/alpha/m2u
fi