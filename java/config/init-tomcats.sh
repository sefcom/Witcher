#!/usr/bin/env bash

PORT=14000
ADMIN_PORT=15000
for x in {1..100}; do
    cp -a /opt/tomcat/base /opt/tomcat/port_${PORT}
    sed 's/Connector port="8080"/Connector port="'${PORT}'"/g' -i /opt/tomcat/port_${PORT}/conf/server.xml
    sed 's/port="8005"/port="'${ADMIN_PORT}'"/g' -i /opt/tomcat/port_${PORT}/conf/server.xml
    PORT=$(( $PORT + 1 ))
    ADMIN_PORT=$(( $ADMIN_PORT + 1 ))
done

