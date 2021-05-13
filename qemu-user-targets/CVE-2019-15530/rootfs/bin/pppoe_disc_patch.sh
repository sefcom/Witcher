#!/bin/sh

echo "pass" > /etc/ppp/pppPatch
killall pppd
sleep 1
ifconfig eth1 down




