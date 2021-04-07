#!/bin/sh
#echo [$0] [$1] [$2] [$3] [$4] [$5] [$6] [$7] [$8] [$9] > /dev/console
xmldbc -P /etc/scripts/wifi/sitesurvey.php -V ACTION=$1 -V INDEX=$2 -V CHANNEL=$3 -V BSSID=$4 -V SIGNAL=$5 -V WLMODE=$6 -V AUTHTYPE=$7 -V ENCRTYPE=$8 -V SSID="$9" > /dev/null
exit 0
