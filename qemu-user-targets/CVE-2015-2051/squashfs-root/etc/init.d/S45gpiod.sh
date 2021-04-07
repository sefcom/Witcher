#!/bin/sh
wanidx=`xmldbc -g /device/router/wanindex`
if [ "$wanidx" != "" ]; then 
	gpiod -w $wanidx &
else
	gpiod &
fi

