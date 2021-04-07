#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	ddnsd &
else
	killall ddnsd
fi
