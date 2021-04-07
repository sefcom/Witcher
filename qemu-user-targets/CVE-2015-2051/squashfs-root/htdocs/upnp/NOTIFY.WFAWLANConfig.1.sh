#!/bin/sh
UPNPMSG=`xmldbc -g /runtime/upnpmsg`
[ "$UPNPMSG" = "" ] && UPNPMSG="/dev/null"
echo "[$0] [$1] [$2] [$3] [$4] ..." > $UPNPMSG

SVC="WFAWLANConfig1"
PHP="NOTIFY.WFAWLANConfig.1.php"
SHFILE="/var/run/NOTIFY.WFAWLANConfig.$$.sh"

PARAMS="-V TARGET_SERVICE=$SERVICE -V EVENT_TYPE=$1 -V EVENT_MAC=$2 -V EVENT_PAYLOAD=$3 -V REMOTE_ADDR=$4"

xmldbc -P /etc/scripts/upnp/run.NOTIFY-WFADEV.php -V SERVICE=$SVC -V TARGET_PHP=$PHP > $SHFILE
echo "rm $SHFILE" >> $SHFILE
sh $SHFILE &
