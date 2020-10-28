#!/usr/bin/env bash



find /opt/tomcat -name 'port_*' -type d -exec cp /p/Witcher/java/tests/JavaWitcherHello.war {}/webapps/JavaWitcherHello.war \;

if sudo mysql feedback --execute 'show databases;'; then
    echo "Feedback database already exists"
else
    sudo mysql < /config/feedback.sql
fi

