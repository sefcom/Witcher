#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
event WAN-1.UP		add "service INFSVCS.WAN-1 restart"
event WAN-1.DOWN	add "service INFSVCS.WAN-1 stop"
event WAN-2.UP		add "service INFSVCS.WAN-2 restart"
event WAN-2.DOWN	add "service INFSVCS.WAN-2 stop"
event WAN-3.UP		add "service INFSVCS.WAN-3 restart"
event WAN-3.DOWN	add "service INFSVCS.WAN-3 stop"
event WAN-4.UP		add "service INFSVCS.WAN-4 restart"
event WAN-4.DOWN	add "service INFSVCS.WAN-4 stop"
#fast boot for mfc mode, Sammy
mfcmode=`devdata get -e mfcmode`
if [ "$mfcmode" = "1" ]; then
	event LAN-1.UP 	add "service ENLAN start"
else
event LAN-1.UP		add "service INFSVCS.LAN-1 restart"
fi
event LAN-1.DOWN	add "service INFSVCS.LAN-1 stop"
event LAN-2.UP		add "service INFSVCS.LAN-2 restart"
event LAN-2.DOWN	add "service INFSVCS.LAN-2 stop"
event LAN-3.UP		add "service INFSVCS.LAN-3 restart"
event LAN-3.DOWN	add "service INFSVCS.LAN-3 stop"
event LAN-4.UP		add "service INFSVCS.LAN-4 restart"
event LAN-4.DOWN	add "service INFSVCS.LAN-4 stop"
event LAN-5.UP		add "service INFSVCS.LAN-5 restart"
event LAN-5.DOWN	add "service INFSVCS.LAN-5 stop"
event LAN-6.UP		add "service INFSVCS.LAN-6 restart"
event LAN-6.DOWN	add "service INFSVCS.LAN-6 stop"
event BRIDGE-1.UP	add "service INFSVCS.BRIDGE-1 restart"
event BRIDGE-1.DOWN	add "service INFSVCS.BRIDGE-1 stop"

event REBOOT		add "/etc/events/reboot.sh"
event FRESET		add "/etc/events/freset.sh"
event DBSAVE		add "/etc/scripts/dbsave.sh"
event RESETCFG.WIFI	add "/etc/events/resetcfg-wifi.sh"
event UPDATERESOLV	add "/etc/events/UPDATERESOLV.sh"
event SEALPAC.SAVE	add "/etc/events/SEALPAC-SAVE.sh"
event SEALPAC.LOAD	add "/etc/events/SEALPAC-LOAD.sh"
event SEALPAC.CLEAR	add "/etc/events/SEALPAC-CLEAR.sh"
event DNSCACHE.FLUSH add "/etc/events/DNSCACHE-FLUSH.sh"
event SENDMAIL		add "phpsh /etc/events/SENDMAIL.php"
event LOGFULL 		add "phpsh /etc/events/SENDMAIL.php ACTION=LOGFULL"
event SCANARP		add "/etc/events/scanarp.sh"
event CHECKFW		add "/etc/events/checkfw.sh"
event DHCPS4.RESTART add "/etc/events/DHCPS-RESTART.sh"
event INF.RESTART	add "phpsh /etc/events/INF-RESTART.php"
event WAN.RESTART	add "phpsh /etc/events/INF-RESTART.php PREFIX=WAN"
event LAN.RESTART	add "phpsh /etc/events/INF-RESTART.php PREFIX=LAN"
event BRIDGE.RESTART add "phpsh /etc/events/INF-RESTART.php PREFIX=BRIDGE"
event DISKUP add "/etc/events/disk.sh"
event DISKDOWN add "/etc/events/disk.sh"

event SEALPAC.LOAD
service DNS alias DNS4.INF
service DNS alias DNS6.INF
fi
