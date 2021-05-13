#!/bin/sh
#
# Mighty translators Area.
#

AP_MODE=`grep wlan0:apMode /var/config | cut -d ':' -f 5 | cut -d ' ' -f 2`

if [ ${CLIENT_MODE} = "yes" ]; then
	if [ ${AP_MODE} = "5" ]; then
		TRANS=${AP_MODE_TRANSLATORS}
	else
		TRANS=${TRANSLATORS}
	fi
else
	TRANS=${TRANSLATORS}
fi

for i in ${TRANS};
do
	[ -f ${TRANSLATORS_BIN_LOCATION}/$i ] || continue
	ncecho 'Starting Translator...      '
	${TRANSLATORS_BIN_LOCATION}/$i < /var/config > ${NULL_DEVICE}
	cecho green "[${i}]"
done
#Flush the files open after all translators are done.
/bin/sync
