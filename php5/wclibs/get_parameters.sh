#!/usr/bin/env bash

http_logfile=${1}

echo "PAGES"
egrep -a "(^GET|^POST)" ${http_logfile} |grep php|cut -d "8" -f5|cut -d'?' -f1|cut -d " " -f1|sort|uniq
echo "---------------------------"
echo "POST variables"
egrep -a "^([A-Za-z0-9]{1,100})=[A-Za-z0-9\-%_*&]{0,300}&{0,1}" ${http_logfile} |sed 's/&/\n/g'|cut -d "=" -f1|sort|uniq
echo "---------------------------"
echo "GET variables"
egrep -a "(^GET|^POST)" ${http_logfile} |grep php|cut -d "8" -f5|grep '\?'|cut -d'?' -f2|cut -d " " -f1|cut -d "=" -f1|sort|uniq







