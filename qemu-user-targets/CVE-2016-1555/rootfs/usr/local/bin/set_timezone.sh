#!/bin/sh

if [ $# -ne 1 ];
then
	echo "$0 <zone code>"
	exit 1
fi

#update the /etx/TZ file latest timezone.
echo $1 > /etc/TZ
