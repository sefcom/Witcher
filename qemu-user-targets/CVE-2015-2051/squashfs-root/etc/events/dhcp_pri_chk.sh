#!/bin/sh
echo [$0] [$1] [$2] [$3] ... > /dev/console
xmldbc -P /etc/events/dhcp_pri_chk.php -V PHYINF=$1 -V INF=$2 -V CONN=$3 > /var/run/$2_dhcp_pri_chk.sh
sh /var/run/$2_dhcp_pri_chk.sh
