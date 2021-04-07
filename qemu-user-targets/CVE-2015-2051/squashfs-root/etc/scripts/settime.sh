#!/bin/sh
echo [$0] $1 $2 $3 ... > /dev/console
if [ $# = 0 ]; then
	echo "Usage: settime.sh HH:MM:SS"
	exit 9
fi
D=`date +%Y.%m.%d -u`
#use date -u to set UTC time because of current time is send by HNAP is GMT
date -u "$D-$1" > /dev/console 2>&1
service schedule on
exit 0
