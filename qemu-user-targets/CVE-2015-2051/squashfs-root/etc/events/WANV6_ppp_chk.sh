#!/bin/sh
echo [$0] [$1] ... > /dev/console
xmldbc -P /etc/events/WANV6_ppp_chk.php -V INF=$1 > /var/run/$1_ppp_chk.sh
sh /var/run/$1_ppp_chk.sh
