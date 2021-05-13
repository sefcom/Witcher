#!/bin/sh

if [ "$#" != "1" ]; then echo "Usage: $0 {start | stop | restart}"; exit 1 ; fi

RETVAL=0
GETMIB="flash get"
CONFILE=/etc/net-snmp/snmpd.conf
PIDFILE=/var/run/snmpd.pid

if [ $1 = "stop" -o $1 = "restart" ]; then
	# Stop snmpd.
	if [ -f $PIDFILE ]; then
		PID=`cat $PIDFILE`
		if [ $PID != 0 ]; then
			kill -9 $PID
			RETVAL=$?
			if [ $RETVAL = 0 ]; then 
				echo "Shutting down snmpd ... Success"
			else    
				echo "Shutting down snmpd ... Fail"
			fi    
		fi
		rm -f $PIDFILE
	else
		echo "Shutting down snmpd ... No snmpd running"	
	fi
fi

if [ $1 = "start" -a -f $PIDFILE ]; then
	echo "Starting snmpd ... Already running"
	exit 1
fi	

if [ $1 = "start" -o $1 = "restart" ]; then
	# Start snmpd.
	eval `$GETMIB SNMP_ENABLED`
	eval `$GETMIB SNMP_NAME`
	eval `$GETMIB SNMP_LOCATION`
	eval `$GETMIB SNMP_CONTACT`
	eval `$GETMIB SNMP_RWCOMMUNITY`
	eval `$GETMIB SNMP_ROCOMMUNITY`
	eval `$GETMIB SNMP_TRAP_RECEIVER1`
	eval `$GETMIB SNMP_TRAP_RECEIVER2`
	eval `$GETMIB SNMP_TRAP_RECEIVER3`

	if [ $SNMP_ENABLED = 1 ]; then
		echo "sysDescr DEFAULT" > $CONFILE
		echo "sysObjectid  .1.3.6.1.4.1.28866.4.1" >> $CONFILE
		echo "sysContact  $SNMP_CONTACT" >> $CONFILE
		echo "sysName  $SNMP_NAME" >> $CONFILE
		echo "sysLocation  $SNMP_LOCATION" >> $CONFILE
		echo "sysServices  3" >> $CONFILE
		echo "rocommunity  $SNMP_ROCOMMUNITY" >> $CONFILE
		echo "rwcommunity  $SNMP_RWCOMMUNITY" >> $CONFILE
		if [ "$SNMP_TRAP_RECEIVER1" != "0.0.0.0" ]; then
			echo "trapsink  $SNMP_TRAP_RECEIVER1" >> $CONFILE
			echo "trap2sink  $SNMP_TRAP_RECEIVER1" >> $CONFILE
			echo "informsink  $SNMP_TRAP_RECEIVER1" >> $CONFILE
		fi    
		if [ "$SNMP_TRAP_RECEIVER2" != "0.0.0.0" ]; then
			echo "trapsink  $SNMP_TRAP_RECEIVER2" >> $CONFILE
			echo "trap2sink  $SNMP_TRAP_RECEIVER2" >> $CONFILE
			echo "informsink  $SNMP_TRAP_RECEIVER2" >> $CONFILE
		fi    
		if [ "$SNMP_TRAP_RECEIVER3" != "0.0.0.0" ]; then
			echo "trapsink  $SNMP_TRAP_RECEIVER3" >> $CONFILE
			echo "trap2sink  $SNMP_TRAP_RECEIVER3" >> $CONFILE
			echo "informsink  $SNMP_TRAP_RECEIVER3" >> $CONFILE
		fi 

		echo "authtrapenable  1" >> $CONFILE
		if [ "$DEBUG" = "1" ]; then     
			snmpd -d -Lo -C -c $CONFILE -p $PIDFILE
		else
			snmpd -Lf /dev/null -C -c $CONFILE -p $PIDFILE
		fi	
		RETVAL=$?
		if [ $RETVAL = 0 ]; then 
			echo "Starting snmpd ... Success"
		else    
			echo "Starting snmpd ... Fail"
		fi    
	fi
fi

exit $RETVAL
