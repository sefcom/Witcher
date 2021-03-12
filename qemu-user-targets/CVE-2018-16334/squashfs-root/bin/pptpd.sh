#!/bin/sh
mkdir /etc/ppp

unit=$1
mppe=$2
dns1=$3
dns2=$4
mppeop=$5
ippool=$6
up="pptp_server?op=1,index=$unit"
down="pptp_server?op=2,index=$unit"
confile=/etc/ppp/options$unit.pptpd
IPUP=/etc/ppp/ip-up$unit
IPDOWN=/etc/ppp/ip-down$unit

echo "#!/bin/sh" > $IPUP
echo "#!/bin/sh" > $IPDOWN
echo "cfm Post netctrl $up &" >> $IPUP
echo "cfm Post netctrl $down &" >> $IPDOWN
echo "cat /proc/uptime > /etc/at" >> $IPUP
chmod +x $IPUP
echo "echo '0 0' > /etc/at" >> $IPDOWN
##ipcp downºóÉ¾³ýµÇÂ¼ÐÅÏ¢
#echo "rm /tmp/pptp/logininfo$unit">>$IPDOWN
chmod +x $IPDOWN

echo auth > $confile
echo lock >> $confile
echo lcp-echo-interval 20 >> $confile
##modfiy by zhang
echo lcp-echo-failure 3 >> $confile
#echo lcp-echo-failure 0 >> $confile
echo idle 60 >> $confile
###end modfiy###
echo ipcp-accept-local >> $confile
echo ipcp-accept-remote >> $confile
echo ip-up-script $IPUP >> $confile
echo ip-down-script $IPDOWN >> $confile
echo proxyarp >> $confile
echo name pptpserver >> $confile
echo refuse-pap >> $confile
#echo refuse-chap >> $confile
echo require-mschap >> $confile
echo require-mschap-v2 >> $confile
if [  $mppe -ne 0 ]
then 
#echo mppe required,no40,no56,stateless >> $confile
	echo refuse-chap >> $confile
	if [  $mppeop -ne 40 ]
	then
		echo +mppe-128 >> $confile
	else
		echo +mppe-40 >> $confile
	fi
else
	echo require-chap >> $confile
	echo nomppe >> $confile
fi
echo ip-address-pool $ippool >> $confile
echo ms-dns $dns1 >> $confile
echo ms-dns $dns2 >> $confile
#echo noccp >> $confile
echo nobsdcomp >> $confile
#echo novj >> $confile
#echo novjccomp >> $confile
echo nologfd >> $confile
echo dump >> $confile
echo unit $unit >> $confile
