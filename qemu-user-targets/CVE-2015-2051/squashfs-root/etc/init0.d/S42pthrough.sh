#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	service DEVICE.PASSTHROUGH start
else
	service DEVICE.PASSTHROUGH stop
fi
