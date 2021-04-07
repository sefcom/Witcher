#!/bin/sh
# reset wifi config to default

xmldbc -P /etc/events/RESETCFG.WIFI.php
/etc/scripts/dbsave.sh


echo "Resetting wifi config success...." > /dev/console
service PHYINF.WIFI restart

