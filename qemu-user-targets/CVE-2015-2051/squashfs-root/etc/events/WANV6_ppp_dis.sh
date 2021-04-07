#!/bin/sh
echo [$0] [$1] [$2] [$3] ... > /dev/console
xmldbc -P /etc/events/WANV6_ppp_dis.php -V INF=$1 -V ACT=$2 -V INFV4=$3 > /var/run/$1_ppp_dis_$2.sh
sh /var/run/$1_ppp_dis_$2.sh
