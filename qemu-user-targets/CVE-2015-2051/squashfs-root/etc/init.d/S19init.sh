#!/bin/sh
mkdir -p /var/etc /var/log /var/run /var/sealpac /var/tmp /var/etc/ppp /var/dnrd /var/etc/iproute2 /var/htdocs/upnp /var/htdocs/web
echo -n > /var/etc/resolv.conf
echo -n > /var/TZ
echo "127.0.0.1 hgw" > /var/hosts
