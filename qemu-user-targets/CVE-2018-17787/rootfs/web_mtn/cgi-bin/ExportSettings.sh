#!/bin/sh


#output HTTP header
now_day=`date +%Y%m%d`
echo "Pragma: no-cache\n"
echo "Cache-control: no-cache\n"
echo "Content-type: application/octet-stream"
echo "Content-Transfer-Encoding: binary"			#  "\n" make Un*x happy
echo "Content-Disposition: attachment; filename=\"$1-$2-${now_day}-backup.dat\""
echo ""

#echo "Default_2860"
#ralink_init show 2860 2>/dev/null
#echo "Default_rtdev"
#ralink_init show rtdev 2>/dev/null

/bin/saveConfigToFile
cat /var/config.dat 2>/dev/null
