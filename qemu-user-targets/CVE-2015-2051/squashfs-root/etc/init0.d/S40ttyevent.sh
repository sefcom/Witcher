#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	event "TTY.ATTACH"		add "/etc/events/ttyplugin.sh;usockc /var/wanmonitor_ctrl USB3G_PLUGIN"
	event "TTY.DETTACH"		add "/etc/events/ttyplugoff.sh;usockc /var/wanmonitor_ctrl USB3G_UNPLUGIN"
	event "DIALINIT"        add "/etc/events/DIALINIT.3G.sh"	
fi
