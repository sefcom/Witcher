#!/bin/sh
sign=`xmldbc -g /runtime/device/image_sign`
devn=`cat /etc/config/devconf`
xmldbc -P /etc/scripts/dlcfg_hlper.php -V ACTION=STARTTODOWNLOADFILE
xmldbc -d /var/config.xml
xmldbc -P /etc/scripts/dlcfg_hlper.php -V ACTION=ENDTODOWNLOADFILE
gzip /var/config.xml
seama -i /var/config.xml.gz -m signature=$sign -m noheader=1 -m type=devconf -m dev=$devn 
mv /var/config.xml.gz.seama /htdocs/web/docs/config.bin
rm -f /var/config.xml.gz /var/config.xml.gz.seama
echo "[$0]: config.bin generated!" > /dev/console
