#!/bin/sh
# kernel > 2.6.22, it (may)will use nf_conntrack_max
CONNTRACK_MAX=`xmldbc -g /runtime/device/conntrack_max`
if [ "$CONNTRACK_MAX" = ""  ]; then
	MEMTOTAL=`cat /proc/meminfo | grep MemTotal | scut -f 2`
	if [ $MEMTOTAL -le 16384 ]; then
	    CONNTRACK_MAX=2048
	elif [ $MEMTOTAL -le 32768 ]; then
	    CONNTRACK_MAX=4096
	elif [ $MEMTOTAL -le 65536 ]; then
	    CONNTRACK_MAX=8192
	elif [ $MEMTOTAL -le 131072 ]; then
	    CONNTRACK_MAX=30000
	elif [ $MEMTOTAL -le 262144 ]; then
	    CONNTRACK_MAX=65536
	elif [ $MEMTOTAL -le 524288 ]; then
	    CONNTRACK_MAX=65536
	fi

	xmldbc -s /runtime/device/conntrack_max $CONNTRACK_MAX
	echo "CONNTRACK_MAX=$CONNTRACK_MAX"
fi
CONNTRACK_MIN=`xmldbc -g /runtime/device/conntrack_min`
if [ "$CONNTRACK_MIN" = ""  ]; then
	CONNTRACK_MIN=`expr $CONNTRACK_MAX / 2`
	xmldbc -s /runtime/device/conntrack_min $CONNTRACK_MIN
	echo "CONNTRACK_MIN=$CONNTRACK_MIN"
fi

if [ -f /proc/sys/net/netfilter/nf_conntrack_max ]; then
	echo $CONNTRACK_MAX > /proc/sys/net/netfilter/nf_conntrack_max
else
	echo $CONNTRACK_MAX > /proc/sys/net/ipv4/ip_conntrack_max
fi

# For non-NAPI dev(such as ralink wireless interface, may can smaller )
#echo 200 > /proc/sys/net/core/netdev_max_backlog
# For NAPI dev(such as ralink ethernet interface, may can smaller )
#echo 32 > /proc/sys/net/core/netdev_budget

if [ -f /lib/modules/sw_tcpip.ko ]; then
	insmod /lib/modules/sw_tcpip.ko
fi

if [ -f /lib/modules/ifresetcnt.ko ]; then
	insmod /lib/modules/ifresetcnt.ko
fi
