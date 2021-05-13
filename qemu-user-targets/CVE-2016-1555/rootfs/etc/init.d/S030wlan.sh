#!/bin/sh
#
# All wlan related stuff loading drivers and setting mac or other parameters etc.
#

# Greping these things here because other scripts might change this if it is initilize in generic script.

if [ ${WLAN_WITH_BASEMAC_N_COUNTRY} = "yes" ]; then
	MAC=`${BDDATARD} ${MAC_OFFSET_4_WLAN}`
	COUNTRY_CODE=`grep sysCountryRegion /var/config | awk '{print $2}'`
fi

if [ ${WLAN_MAX_CLIENT_SUPPORT} = "yes" ]; then
	for i in `seq 0 $(expr $NO_OF_RADIOS - 1)`;do
		eval MAX_CLIENTS_${i}=`grep wlan${i}:maxWirelessClients /var/config | awk '{print $2}'`		
	done
fi

wlan_drv_err()
{
        if [ $1 != 0 ]; then
                cecho red '[FAILED]'
		exit
        fi
}

AP_MODE=`grep wlan0:apMode /var/config | cut -d ':' -f 5 | cut -d ' ' -f 2`

if [ -d ${WLAN_MODULE_PATH} ]; then

ncecho 'Loading wlan modules.       '
	if [ ${WLAN_WITH_BASEMAC_N_COUNTRY} = "yes" ]; then
		${INSMOD} ${WLAN_MODULE_PATH}/ath_hal.ko macaddr=${MAC}
		wlan_drv_err $?
	else
		${INSMOD} ${WLAN_MODULE_PATH}/ath_hal.ko
		wlan_drv_err $?
	fi
        ${INSMOD} ${WLAN_MODULE_PATH}/asc.ko
	wlan_drv_err $?
	if [ ${SYS_USES_WLAN_GPIO} = "yes" ]; then
		${INSMOD} ${WLAN_MODULE_PATH}/aws_gpio.ko
		wlan_drv_err $?
	fi
	${INSMOD} ${WLAN_MODULE_PATH}/ath_rate_atheros.ko
	wlan_drv_err $?
	if [ ${WLAN_MAX_CLIENT_SUPPORT} = "yes" ]; then
		if [ ${NO_OF_RADIOS} = 2 ]; then
			${INSMOD} ${WLAN_MODULE_PATH}/wlan.ko Max_client_if0=${MAX_CLIENTS_0}  Max_client_if1=${MAX_CLIENTS_1}
	 		wlan_drv_err $?
		else
			${INSMOD} ${WLAN_MODULE_PATH}/wlan.ko Max_client_if0=${MAX_CLIENTS_0}
			wlan_drv_err $?
		fi
	else
		${INSMOD} ${WLAN_MODULE_PATH}/wlan.ko
		wlan_drv_err $?
	fi
	${INSMOD} ${WLAN_MODULE_PATH}/wlan_scan_ap.ko

	if [ ${CLIENT_MODE} = "yes" ]; then
        	if [ ${AP_MODE} = "5" ]; then
                	${INSMOD} /lib/modules/wlan/wlan_scan_sta.ko
        	fi
        fi
	wlan_drv_err $?
	${INSMOD} ${WLAN_MODULE_PATH}/wlan_acl.ko
	wlan_drv_err $?
	${INSMOD} ${WLAN_MODULE_PATH}/wlan_wep.ko
	wlan_drv_err $?
	${INSMOD} ${WLAN_MODULE_PATH}/wlan_tkip.ko
	wlan_drv_err $?
	${INSMOD} ${WLAN_MODULE_PATH}/wlan_ccmp.ko
	wlan_drv_err $?
	${INSMOD} ${WLAN_MODULE_PATH}/wlan_xauth.ko
	wlan_drv_err $?
        if [ ${WLAN_DFS_SUPPORT} = "yes" ]; then
            ${INSMOD} ${WLAN_MODULE_PATH}/ath_dfs.ko 
            wlan_drv_err $?
        fi

      	if [ ${FUSION_CODE_BASE} = "yes" ] ; then
		${INSMOD} ${WLAN_MODULE_PATH}/ath_dev.ko
		wlan_drv_err $?
	else
	
		if [ ${WLAN_WITH_BASEMAC_N_COUNTRY} = "yes" ]; then
			${INSMOD} ${WLAN_MODULE_PATH}/ath.ko countrycode=${COUNTRY_CODE}
			wlan_drv_err $?
		else
    	            	${INSMOD} ${WLAN_MODULE_PATH}/ath.ko
			wlan_drv_err $?
		fi
	fi
	${INSMOD} ${WLAN_MODULE_PATH}/ath_${WLAN_BUS}.ko
	wlan_drv_err $?



cecho green '[DONE]'
fi
