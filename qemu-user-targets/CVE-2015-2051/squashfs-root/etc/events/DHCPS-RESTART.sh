#!/bin/sh
xmldbc -P /etc/events/DHCPS-RESTART.php > /var/run/DHCPS-RESTART.sh
sh /var/run/DHCPS-RESTART.sh
