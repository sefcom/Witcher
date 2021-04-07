#!/bin/sh
echo [$0] [$1] [$2] [$3] [$4]... > /dev/console
xmldbc -P /etc/events/WANV6-DETECT.php -V INFV4=$1 -V INFLL=$2 -V INFV6=$3 -V INFV6AUTO=$4 > /var/run/$1_DETECTV6.sh
sh /var/run/$1_DETECTV6.sh
