#!/bin/sh
echo [$0] [$1] [$2] [$3] [$4] ... > /dev/console
xmldbc -P /etc/events/WANV6_DSLITE_DETECT.php -V INF=$1 -V V4ACTUID=$2  -V V6ACTUID=$3 -V AUTOSET=$4 > /var/run/$1_dslite_det.sh
sh /var/run/$1_dslite_det.sh
