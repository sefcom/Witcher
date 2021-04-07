#!/bin/sh
[ -f /var/run/UPDATERESOLV_stop.sh ] && sh /var/run/UPDATERESOLV_stop.sh > /dev/console
xmldbc -P /etc/events/UPDATERESOLV.php -V START=/var/run/UPDATERESOLV_start.sh -V STOP=/var/run/UPDATERESOLV_stop.sh
sh /var/run/UPDATERESOLV_start.sh
