#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ -f "/etc/scripts/cpuload.sh" ]; then
    /etc/scripts/cpuload.sh &
fi

exit 0
