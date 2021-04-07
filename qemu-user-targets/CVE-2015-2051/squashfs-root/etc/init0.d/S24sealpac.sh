#!/bin/sh
LANGMTD=`cat /etc/config/langpack`	
if [ "$LANGMTD" != "" ]; then
	mount -t squashfs $LANGMTD /htdocs/web/js/localization
	event "CLEANLANG"  add "umount $LANGMTD;echo ffffffffffffffffffffffffffffffff > $LANGMTD"
fi 

