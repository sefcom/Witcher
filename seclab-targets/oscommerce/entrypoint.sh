#!/bin/sh

sudo service mysql start
mysql -e "CREATE DATABASE oscommerce;"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'root'";
sudo service mysql stop

supervisord;
