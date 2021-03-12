#!/bin/sh

USER=$1
PSWD=$2
MTU=$3
ETH=$4
case $ETH in 
	eth0.2)
	UNIT=1;;
	eth0.3)
	UNIT=2;;
	eth0.4)
	UNIT=3;;
	eth0.5)
	UNIT=4;;
	*)
	UNIT=0;;
esac
if [ $# -ne 4 ]
then
echo "Usage: $0 username  password mtu devname"
exit 1 
fi

IPUP=/etc/ppp/ip-up$UNIT
IPDOWN=/etc/ppp/ip-down$UNIT

PPPD=/bin/pppd_wan$UNIT
CONFIG_FILE=/etc/ppp/option.pppoe.wan$UNIT
IFNAME=wan$UNIT

mkdir -p /etc/ppp
echo noauth >$CONFIG_FILE
echo user \'$USER\' >>$CONFIG_FILE
echo password \'$PSWD\' >>$CONFIG_FILE
echo nomppe >>$CONFIG_FILE
echo hide-password >>$CONFIG_FILE
echo noipdefault >>$CONFIG_FILE
echo nodetach >>$CONFIG_FILE
echo usepeerdns >>$CONFIG_FILE
echo mru $MTU >>$CONFIG_FILE
echo mtu $MTU >>$CONFIG_FILE
echo persist >>$CONFIG_FILE
if [ $UNIT -ne 0 ]
then 
echo unit $UNIT >>$CONFIG_FILE
fi
echo ip-up-script $IPUP >>$CONFIG_FILE
echo ip-down-script $IPDOWN >>$CONFIG_FILE
echo lcp-echo-failure 8 >>$CONFIG_FILE
echo lcp-echo-interval 20 >>$CONFIG_FILE
echo plugin /lib/rp-pppoe.so $ETH >>$CONFIG_FILE

#if [ ! -f $PPPD ] 
#then 
#	ln -s /bin/pppd $PPPD
#fi

echo "#!/bin/sh" >$IPUP
echo "cfm post multiWAN AutoUp$UNIT" >>$IPUP
echo "cat /proc/uptime > /etc/conntime$UNIT" >>$IPUP
chmod +x $IPUP

echo "#!/bin/sh" >$IPDOWN
echo "cfm Post multiWAN AutoDown$UNIT" >>$IPDOWN
echo "echo '0 0' > /etc/conntime$UNIT" >>$IPDOWN
chmod +x $IPDOWN
