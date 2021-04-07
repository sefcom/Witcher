#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	(sleep 2; service SHAREPORT start)&
else
	service SHAREPORT stop
fi
