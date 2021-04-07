#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
event SYSLOG_MSG add "sh /var/run/syslog_msg.sh"
fi

event SITESURVEY add "sh /etc/events/SITESURVEY.sh"
event DISKUP insert USB_LED:"phpsh /etc/events/update_usb_led.php"
event DISKDOWN insert USB_LED:"phpsh /etc/events/update_usb_led.php"
