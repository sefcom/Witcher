#!/bin/sh
echo [$0] $1 $2 $3 ....

case "$1" in
NEW_CLIENT)
	if [ $2 -eq 1 ]; then
		logger -p notice -t WIFI "Got new client [$3] associated from BAND24G-1.2 (2.4 Ghz)"
	else
	logger -p notice -t WIFI "Got new client [$3] associated from BAND24G-1.1 (2.4 Ghz)"
	fi
	event "BAND24G.ASSOCIATED"
	;;
*)
	echo "not support [$1] ..."
	;;
esac
exit 0
