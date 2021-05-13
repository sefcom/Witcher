#!/bin/sh

#Loading bridge modules and create a bridge

error()
{
     if [  $1 != 0 ]; then
        	cecho red '[FAILED]'
        	exit;
     fi
}

ncecho 'Loading Bridge module.      '

if [ -e /lib/modules/net/bridge/*.ko ]; then
	if [ -e /lib/modules/net/llc/*.ko ]; then
        	${INSMOD} /lib/modules/net/llc/*.ko
		error $?
        	${INSMOD} /lib/modules/net/bridge/*.ko
		error $?
	else
        	${INSMOD} /lib/modules/net/bridge/*.ko
		error $?
	fi

	if [ ${CONFIG_CENTRALIZED_VLAN} = "yes" ]; then
		${INSMOD} lib/modules/net/8021q/*.ko
                error $?
	fi

		cecho green '[DONE]'
		#Keep bridge-netfilter disabled by default.
		#It will be enabled by wifidog for HTTP redirect feature, when enabled
		if [ ${BRIDGE_NF_DISABLED_BY_DEFAULT} = "yes" ]; then
			echo 0 > /proc/sys/net/bridge/bridge-nf-enabled
		fi

	AP_MODE=`grep wlan0:apMode /var/config | cut -d ':' -f 5 | cut -d ' ' -f 2`

	if [ ${CLIENT_MODE} = "yes" ]; then
        	if [ ${AP_MODE} = "5" ]; then
 			${RMMOD} bridge.ko
                	${INSMOD} /lib/modules/net/client_bridge/client_bridge.ko

		fi
	fi

else
        	cecho red '[FAILED]'
fi
