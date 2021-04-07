#!/bin/sh
#this should be after smart 404(S41)...
routermode="`xmldbc -g /device/layout`"

if [ "$routermode" != "router" ] ; then
echo "not enable mydlink in NOT router mode"
exit 0
fi

echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
event WAN-1.UP	insert "mydlink:xmldbc -t \"mydlink:15:/mydlink/opt.local restart\""
event WAN-1.DOWN	insert "mydlink:/mydlink/opt.local stop"
event WAN-1.UP insert "checkfw_mdns:xmldbc -t \"cfm:5:service MDNSRESPONDER restart\""
event WAN-1.UP insert "checkfw_nrlv:xmldbc -t \"cfn:10:service NAMERESOLV restart\""
event MYDLINK_TESTMAIL add "mdtestmail"
service MYDLINK.LOG $1
MODE=`xmldbc -g /device/router/mode`
if [ "$MODE" == "1W2L" ] ; then
arpmonitor -i br0 -i br1 &
else
arpmonitor -i br0 &
fi
else 
event WAN-1.UP	remove mydlink
event WAN-1.UP	remove checkfw
event WAN-1.UP	remove checkfw_mdns
event WAN-1.UP	remove checkfw_nrlv
event WAN-1.DOWN	remove mydlink
event MYDLINK_TESTMAIL flush
service MYDLINK.LOG $1
fi
