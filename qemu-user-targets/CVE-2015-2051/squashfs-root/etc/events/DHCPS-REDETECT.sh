#!/bin/sh
xmldbc -P /etc/events/DHCPS-REDETECT.php -V INF=$1 > /var/run/DHCPS-REDETECT.sh
sh /var/run/DHCPS-REDETECT.sh
