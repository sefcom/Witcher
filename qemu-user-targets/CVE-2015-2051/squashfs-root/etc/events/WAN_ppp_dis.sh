#!/bin/sh
echo [$0] [$1] [$2] ... > /dev/console
xmldbc -P /etc/events/WAN_ppp_dis.php -V INF=$1 -V ACT=$2 > /var/run/$1_ppp_dis_$2.sh
sh /var/run/$1_ppp_dis_$2.sh
