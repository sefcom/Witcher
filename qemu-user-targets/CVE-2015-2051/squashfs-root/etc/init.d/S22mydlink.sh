#!/bin/sh
MYDLINK=`cat /etc/config/mydlinkmtd`
domount=`xmldbc -g /mydlink/mtdagent` 
if [ "$domount" != "" ]; then 
	mount -t squashfs $MYDLINK /mydlink
fi
 

