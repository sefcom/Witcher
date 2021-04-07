#!/bin/sh
echo [$0] [$1] ... > /dev/console
service STORAGE restart
exit 0
