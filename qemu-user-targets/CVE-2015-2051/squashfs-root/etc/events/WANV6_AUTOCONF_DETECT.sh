#!/bin/sh
echo [$0] [$1] [$2] ... > /dev/console
xmldbc -P /etc/events/WANV6_AUTOCONF_DETECT.php -V INF=$1 -V ACT=$2 > /var/run/$1_autoconf_det_$2.sh
sh /var/run/$1_autoconf_det_$2.sh
