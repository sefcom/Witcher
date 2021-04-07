#!/bin/sh
if [ -f "$1" ]; then
	pid=`pfile -f $1`
	[ "$pid" != "0" ] && kill $pid > /dev/console 2>&1
	rm -f $1
fi
