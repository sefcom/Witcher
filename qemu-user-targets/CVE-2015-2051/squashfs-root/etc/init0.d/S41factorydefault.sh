#!/bin/sh
echo [$0]: $1 ... > /dev/console
orig_devconfsize=`xmldbc -g /runtime/device/devconfsize`

if [ "$1" = "start" && "$orig_devconfsize" = "0"]; then
phpsh "/etc/scripts/factorydefault.php"
fi
