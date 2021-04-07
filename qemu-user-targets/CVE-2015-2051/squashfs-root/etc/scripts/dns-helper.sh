#!/bin/sh
#echo [$0] $1 $2 $3 ... > /dev/console
xmldbc -P /etc/scripts/libs/dns-helper.php -V ACTION=$1 -V TARGET=$2 -V DNS=$3 > /var/run/dns-helper.sh
sh /var/run/dns-helper.sh
