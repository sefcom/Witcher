#!/bin/sh
pidfile="/var/servd/$1-udhcpc.pid"
if [ -f $pidfile ]; then
	PID=`cat $pidfile`
	if [ "$PID" != 0 ]; then
		kill -SIGUSR2 $PID
	fi
fi
