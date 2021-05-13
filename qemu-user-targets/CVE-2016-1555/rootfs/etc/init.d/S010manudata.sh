#!/bin/sh

# bddatard returns non zero if mac address is invalid
if [ ! -z ${BDDATARD} ]; then
	if [ ! -z ${WR_MFG_DATA} ]; then
		ncecho 'Checking Manufac. data      '
		${BDDATARD} 0 > ${NULL_DEVICE}
			if [ $? -ne 0 ]; then
				cecho yellow '[DEFAULT]'
				${WR_MFG_DATA} -d
			else
				cecho green '[DONE]'
			fi
	fi
fi

if [ ! -z ${PRINTMD} ]; then
        ncecho 'Checking board file.        '
		if [ ! -e ${MANU_BOARD_FILE} ]; then
			cecho yellow '[CREATED]'
			${PRINTMD} > ${MANU_BOARD_FILE}
		else
			cecho green '[DONE]'
		fi
fi
