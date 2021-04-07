#!/bin/sh
echo [$0] $1 $2 $3 ... > /dev/console
echo [$0] $1 $2 $3 ... > /var/run/temp
if [ $# = 0 ]; then
	echo "Usage: getmodem MODEMNAME"
	exit 9
fi
exit 0
