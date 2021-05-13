#!/bin/sh
#

CUR_VER=$1
DEF_VER=$2
PRODUCT=`printmd | grep -i ProductId | cut -d ' ' -f 3`
if [ ${PRODUCT} == "WN604" ] || [ ${PRODUCT} == "wn604" ] || [ ${PRODUCT} == "WN604-1KMUKS" ] || [ ${PRODUCT} == "wn604-1kmuks" ]; then
	ENC_DEC_VER="4.0"
else
	ENC_DEC_VER="3.0"
fi

if [ ${CUR_VER} \< ${DEF_VER} ]; then 
	if [ ${CUR_VER} \< ${ENC_DEC_VER} ]; then
		# Encrypting the plain texts - security keys & passwords
		exit 2
	else
		# Upgrade migration
		exit 1
	fi
elif [ ${CUR_VER} \> ${DEF_VER} ]; then
		# Downgrade migration
		exit 3
else
	# Should not enter here - restore to factory
	exit 0
fi

