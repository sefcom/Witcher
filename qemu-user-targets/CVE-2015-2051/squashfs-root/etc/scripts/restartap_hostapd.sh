#!/bin/sh
echo "aaaaaaaa" > /dev/console
xmldbc -P /etc/services/WIFI/hostapdcfg.php > /var/topology.conf
hostapd /var/topology.conf &
xmldbc -X /runtime/hostapd_restartap
phpsh /etc/services/WIFI/wifilogo_arg_hostapd_ready.php
