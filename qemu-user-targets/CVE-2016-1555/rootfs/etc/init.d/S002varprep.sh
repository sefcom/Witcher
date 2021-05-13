#!/bin/sh

[ -d ${DROPBEAR_DIR} ] && (ncecho 'Checking SSH keys.          ' && cecho green '[DONE]')

if [ ! -e ${LOG_FILE} ]; then
	ncecho 'Creating new log file.      '
	${MKDIR} -p `dirname ${LOG_FILE}`
	touch ${LOG_FILE}
	cecho yellow '[CREATED]'
fi

[ -e /var/log/wtmp ] && rm -rf /var/log/wtmp 

if [ ! -h /var/run ]; then
	ncecho 'Creating new run file.      '
	[ ! -L /var/run ] && rm -fr /var/run
	[ ! -L /var/net-snmp ] && rm -fr /var/net-snmp
	[ -d /var/dropbear ] && rm -fr /var/dropbear
	mkdir -p /tmp/run
	mkdir -p /tmp/net-snmp 
	ln -s /tmp/run /var/run
	ln -s /tmp/net-snmp /var/net-snmp
	/bin/sync
	cecho yellow '[CREATED]'
else
	ncecho 'Checking for run file.      '
	mkdir -p /tmp/run
	mkdir -p /tmp/net-snmp
	cecho green '[DONE]'
fi
