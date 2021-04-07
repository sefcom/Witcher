#!/bin/sh
echo "Setting gw_name `xmldbc -g /device/gw_name` to /sys/module/jcp_cmd/parameters/product ..."

if [ "`xmldbc -g /device/gw_name`" != "" ]; then
	echo -n `xmldbc -g /device/gw_name` > /sys/module/jcp_cmd/parameters/product
	echo -n `xmldbc -g /device/gw_name` > /sys/module/jcp_cmd/parameters/hostname
fi
