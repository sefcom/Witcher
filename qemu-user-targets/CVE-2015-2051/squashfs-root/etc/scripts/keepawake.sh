#!/bin/sh

if [ "$#" != "2" ] ; then
	echo "Need device name and mount path"
	exit 0
fi

#save our pid, we need this to kill the loop
echo $$ > /tmp/$1.pid

while :
do
	date > $2/.keepawake 2> /dev/null
	sleep 60
done
