#!/bin/sh
echo [$0]: $1 ... > /dev/console
case "$1" in
start|stop|restart)
	service LAYOUT $1
	;;
*)
	echo [$0]: Invalid argument - $1 > /dev/console
	;;
esac

