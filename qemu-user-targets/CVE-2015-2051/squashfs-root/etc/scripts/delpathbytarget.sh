#!/bin/sh
echo [$0] [$1] [$2] [$3] [$4] [$5]... > /dev/console
xmldbc -P /etc/scripts/delpathbytarget.php -V BASE=$1 -V NODE=$2 -V TARGET=$3 -V VALUE=$4 -V POSTFIX=$5 > /dev/null
