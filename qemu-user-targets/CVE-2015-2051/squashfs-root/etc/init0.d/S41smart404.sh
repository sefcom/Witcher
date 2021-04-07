#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
#for smart404 support (tom, 20101130)
phpsh "/etc/scripts/control_smart404.php" ACTION=INIT_SMART404
phpsh "/etc/scripts/control_smart404.php" ACTION=INIT_EVENTS
fi
