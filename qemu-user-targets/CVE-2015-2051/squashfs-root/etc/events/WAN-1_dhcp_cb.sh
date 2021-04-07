#!/bin/sh
echo [$0] [$1] ... > /dev/console
if [ "$1" = "bound" ]; then
	xmldbc -X /runtime/services/wandetect/dhcp/WAN-1
	if [ -n "$ip" ]; then
		xmldbc -s /runtime/services/wandetect/dhcp/WAN-1/ip "$ip"
		if [ -n "$subnet" ]; then
			xmldbc -s /runtime/services/wandetect/dhcp/WAN-1/subnet "$subnet"
		fi
		if [ -n "$router" ]; then
		for i in $router ; do
			xmldbc -a /runtime/services/wandetect/dhcp/WAN-1/router	"$i"
		done
		fi
		if [ -n "$dns" ]; then
		for i in $dns ; do
			xmldbc -a /runtime/services/wandetect/dhcp/WAN-1/dns "$i"
		done
		fi
	fi
fi
exit 0
