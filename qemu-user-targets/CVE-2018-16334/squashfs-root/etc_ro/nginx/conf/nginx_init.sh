#/bin/sh
mkdir /var/nginx
mkdir /var/lib
mkdir /var/lib/nginx
nginx -p /var/nginx
spawn-fcgi -a 127.0.0.1 -p 8188 /usr/bin/app_data_center