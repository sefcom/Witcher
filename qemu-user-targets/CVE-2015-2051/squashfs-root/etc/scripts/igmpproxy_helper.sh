#!/bin/sh
echo [$0] $1 $2 $3 $4 ... > /dev/console
PHPFILE="/etc/scripts/libs/igmpproxy_helper.php"
xmldbc -P $PHPFILE -V ACTION=$1 -V GROUP=$2 -V IF=$3 -V SRC=$4 -V GROUPMAC=$5 -V SRCMAC=$6 > /var/run/igmpproxy_helper.sh
sh /var/run/igmpproxy_helper.sh > /dev/console
