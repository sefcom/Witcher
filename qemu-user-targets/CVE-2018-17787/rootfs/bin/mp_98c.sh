#!/bin/sh
#
# script file to setup  network
#
#
HW_DIR=/hw_setting
HW_PATH=/hw_setting/hw.bin

#GETMIB="flash gethw"
#ELAN_MAC_ADDR="000000000000"
#eval `$GETMIB HW_NIC0_ADDR`
#if [ "$HW_NIC0_ADDR" = "000000000000" ]; then
      #  eval `$GETMIB HW_NIC0_ADDR`
        ELAN_MAC_ADDR="56aaa55a7de8"
#else
#	ELAN_MAC_ADDR=$HW_NIC0_ADDR
#fi
if [  -d "$HW_DIR" ]; then
	if [ ! -e "$HW_PATH" ]; then
		flash default
fi
fi

echo "$ELAN_MAC_ADDR"
ifconfig lo   127.0.0.1
ifconfig eth0 hw ether $ELAN_MAC_ADDR
ifconfig eth1 hw ether $ELAN_MAC_ADDR
brctl addbr br0
brctl addif br0 eth0
brctl addif br0 eth1

ifconfig br0 192.168.1.6

ifconfig  eth0 up
ifconfig  eth1 up

iwpriv wlan0 set_mib mp_specific=1
iwpriv wlan1 set_mib mp_specific=1
ifconfig  wlan0 up
ifconfig  wlan1 up

/bin/set_rx_gain_from_flash.sh


