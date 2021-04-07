#!/bin/sh
echo [$0] [$1] ... > /dev/console
xmldbc -P /etc/events/wan_detect_chk.php -V INF=$1 > /var/run/$1_detect_chk.sh
sh /var/run/$1_detect_chk.sh