#!/bin/sh
echo [$0]: $1 ... > /dev/console
layout=`xmldbc -w /device/layout`   
if [ "$layout" == "router" ]; then
	event WAN-1.UP  insert "checkfw:sh /etc/events/checkfw.sh &"
else
   	event BRIDGE-1.UP  insert "checkfw:sh /etc/events/checkfw.sh &"
fi
