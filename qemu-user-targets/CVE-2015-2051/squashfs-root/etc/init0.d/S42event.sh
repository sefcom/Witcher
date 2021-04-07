#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
event UPNPAV.REFRESH add "sh /etc/events/upnpav_refresh.sh"
fi
