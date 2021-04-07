#!/bin/sh
UPNPMSG=`xmldbc -g /runtime/upnpmsg`
[ "$UPNPMSG" = "" ] && UPNPMSG="/dev/null"
echo "[$0] ..." > $UPNPMSG

SVC="L3Forwarding1"
PHP="NOTIFY.Layer3Forwarding.1.php"
SCRIPT="/var/run/PROPCHANGE.$SVC.$$.sh"

xmldbc -P /etc/scripts/upnp/run.NOTIFY-PROPCHANGE.php -V SERVICE=$SVC -V TARGET_PHP=$PHP > $SCRIPT
echo "rm $SCRIPT" >> $SCRIPT
sh $SCRIPT &
