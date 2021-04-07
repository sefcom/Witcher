#!/bin/sh
#echo [$0] $1 $2....
RSLT="/var/ping_result"
case "$1" in
set)
	rm -f $RSLT
	ping -4 "$2" > $RSLT
	;;
get)
	if [ -f $RSLT ]; then
		cat $RSLT
	fi
	;;
set6)
	rm -f $RSLT
	ping -6 "$2" > $RSLT
	;;
esac
exit 0
