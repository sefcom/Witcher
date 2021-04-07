#!/bin/sh
echo [$0] $1 $2 $3 ... > /dev/console
if [ $# = 0 ]; then
	echo "Usage: settime.sh MM/DD/YYYY"
	exit 9
fi
Y=`echo $1 | cut -d/ -f3`
M=`echo $1 | cut -d/ -f1`
D=`echo $1 | cut -d/ -f2`
T=`date +%H:%M:%S -u`
#use date -u to set UTC time because of current time is send by HNAP is GMT
date -u "$Y.$M.$D-$T" > /dev/console 2>&1
exit 0
