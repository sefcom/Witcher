#!/bin/sh

sudo service mysql start
mysql -e "CREATE DATABASE abante;"
sudo service mysql stop

supervisord;
