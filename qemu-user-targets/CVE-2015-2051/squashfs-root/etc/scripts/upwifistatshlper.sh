#!/bin/sh
echo [$0] $1 $2 $3 ....

case "$1" in
NEW_CLIENT)
	if [ $2 == 1 ]; then
		INF="WLAN-2"
	else
		INF="WLAN-1"
	fi
	logger -p notice -t WIFI "Got new client [$3] associated from $INF."
	;;
*)
	echo "not support [$1] ..."
	;;
esac
exit 0
