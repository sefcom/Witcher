#!/bin/sh


#output HTTP header
now_day=`date +%Y%m%d`
echo "Pragma: no-cache\n"
echo "Cache-control: no-cache\n"
echo "Content-type: application/octet-stream"
echo "Content-Transfer-Encoding: binary"			#  "\n" make Un*x happy
echo "Content-Disposition: attachment; filename=\"$1-$2-${now_day}-syslog.tar.gz\""
echo ""

tar -cf /tmp/syslog.tar.gz /var/log/messages*

cat /tmp/syslog.tar.gz 2>/dev/null
