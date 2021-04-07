#!/bin/sh
#echo [$0] ... > /dev/console
xmldbc -P /etc/scripts/adapter_cmd.php  > /var/run/adapter_cmd.sh
sh /var/run/adapter_cmd.sh
