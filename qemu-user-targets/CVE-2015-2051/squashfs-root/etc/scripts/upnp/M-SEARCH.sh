#!/bin/sh
UPNPMSG=`xmldbc -g /runtime/upnpmsg`
[ "$UPNPMSG" = "" ] && UPNPMSG="/dev/null"
echo "[$0] GOT M-SEARCH: [$1 $2 $3 $4] ..." > $UPNPMSG

if [ -f /var/run/M-SEARCH.$2.$1.sh ]; then
	echo "[$0] The /var/run/M-SEARCH.$2.$1.sh is running, so do nothing & leave." > $UPNPMSG
else
	xmldbc -P /etc/scripts/upnp/M-SEARCH.php -V "SEARCH_TARGET=$1" -V "TARGET_HOST=$2" -V "INF_UID=$3" -V "PARAM=$4" > /var/run/M-SEARCH.$2.$1.sh
	sh /var/run/M-SEARCH.$2.$1.sh
	rm /var/run/M-SEARCH.$2.$1.sh
fi
