#!/bin/sh	
#Restart configd to revert the changes made to the database
/sbin/start-stop-daemon  -K -x /usr/sbin/configd
/sbin/start-stop-daemon  -S -x /usr/sbin/configd & 
#Restart hostpad 
/usr/local/bin/hostapd_tr < /var/config &
