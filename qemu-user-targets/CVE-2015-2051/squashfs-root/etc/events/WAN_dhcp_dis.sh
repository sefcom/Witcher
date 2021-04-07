#!/bin/sh
echo [$0] [$1] ... > /dev/console
xmldbc -P /etc/events/WAN_dhcp_dis.php -V INF=$1 > /var/run/$1_dhcp_dis.sh
sh /var/run/$1_dhcp_dis.sh
