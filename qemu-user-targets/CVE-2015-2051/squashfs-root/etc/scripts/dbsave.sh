#!/bin/sh
# dump the DOM tree to a file.

#patch for 404
orig_devconfsize=`xmldbc -g /runtime/device/devconfsize`

#Prevent multiple DB save working at the same time.
if [ -f "/var/run/db_saving" ]; then
	for i in 1 2 3 4 5; do
		if [ ! -f "/var/run/db_saving" ]; then
			break
		fi
		sleep 1
	done
fi
echo "1" > /var/run/db_saving

xmldbc -d /var/config.xml
gzip /var/config.xml
devconf put -f /var/config.xml.gz
rm -f /var/config.xml.gz

if [ "$orig_devconfsize" = "0" ]; then
	xmldbc -t "BreakFactoryDefault:3:phpsh /etc/scripts/factorydefault.php ACTION=break"
	service MDNSRESPONDER restart
	killall telnetd
fi

rm -f /var/run/db_saving