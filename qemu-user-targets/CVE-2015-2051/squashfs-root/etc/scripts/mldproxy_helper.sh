#!/bin/sh
echo [$0] $1 $2 $3 $4 ... > /dev/console
PHPFILE="/etc/scripts/libs/mldproxy_helper.php"
xmldbc -P $PHPFILE -V ACTION=$1 -V GROUP=$2 -V IF=$3 -V SRC=$4 -V GROUPMAC=$5 -V SRCMAC=$6 > /var/run/mldproxy_helper.sh
sh /var/run/mldproxy_helper.sh > /dev/console
