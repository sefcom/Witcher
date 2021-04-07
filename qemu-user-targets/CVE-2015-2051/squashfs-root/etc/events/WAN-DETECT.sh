#!/bin/sh
echo [$0] [$1] ... > /dev/console
xmldbc -P /etc/events/WAN-DETECT.php -V INF=$1 > /var/run/$1_DETECT.sh
sh /var/run/$1_DETECT.sh
