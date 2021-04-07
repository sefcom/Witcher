#!/bin/sh
# load default value
xmldbc -L /etc/defnodes/defaultvalue.xml

# setup defaultvalue for mfc mode, Sammy
mfcmode=`devdata get -e mfcmode`
if [ "$mfcmode" = "1" -a -f /etc/defnodes/default_mfc.php ]; then
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	echo "!! Enable mfc mode, setup mfc default value !!"
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	xmldbc -P /etc/defnodes/default_mfc.php
fi

# set ssid, password, mac from devdata to node
[ -f /etc/defnodes/defaultvalue.php ] && xmldbc -P /etc/defnodes/defaultvalue.php

# for wifi default value (used in configured --> unconfigured)
xmldbc -P /etc/defnodes/default_wifi.php > /var/default_wifi.xml
xmldbc -R /var/default_wifi.xml

[ -f /usr/sbin/isfreset ] && RESET=`/usr/sbin/isfreset`

if [ "$RESET" = "PRESSED" ]; then
	# do factory reset
	devconf del
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	echo "!! Reset button is pressed, reset to factory default. !!"
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
elif [ "$mfcmode" != "1" ]; then
	# read config value
	devconf get -f /var/config.xml.gz
	if [ "$?" = "0" ]; then
		gunzip /var/config.xml.gz
		xmldbc -r /var/config.xml
		rm /var/config.xml
	else
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
		echo "!!     Uable to read device config.     !!"
		echo "!! Setting is reset to factory default. !!"
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	fi
fi
# update defnode
for i in /etc/defnodes/S??* ;do
	[ ! -f "$i" ] && continue
	echo "  DEFNODE[$i]" > /dev/console
	case "$i" in
	*.sh)
		sh $i
		;;
	*.php)
		xmldbc -P $i
		;;
	*.xml)
		xmldbc -R $i
		;;
	esac
done
