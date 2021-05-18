#!/bin/bash


export MANUAL_SETUP="no"
export MYSQL_HOST="127.0.0.1"
export MYSQL_ROOT_USER="root"
export MYSQL_ROOT_PASS="root"
export MYSQL_USER="root"
export MYSQL_PASS="root"

# ln -s /wclibs/libcgiwrapper.so /usr/lib/x86_64-linux-gnu/libcgiwrapper.so

service mysql start;
mysql -e "CREATE DATABASE openemr;"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';"

bash autoconfig.sh

chown -R www-data:www-data /var/www/openemr/

mysql -u root -p$MYSQL_ROOT_PASS < /var/www/openemr/add_portal.sql

supervisord
