#!/bin/sh

# Start mariadb
/etc/init.d/mysql start

# Start php
/etc/init.d/php7.2-fpm start

# Start nginx
/etc/init.d/nginx start

#sleep infinity
/bin/bash
