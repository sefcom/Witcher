#!/bin/sh

if [ $# -ne 1 ]; then
	echo "$0 <config-file>"
	exit 1

fi

if [ ! -e $1 ] || [ -z $1 ]; then
	echo "file $1 does not exists"
	exit 1
fi

default_config=/etc/default-config
config_file=$1

while read line
do
	config_name=`echo $line | awk '{print $1}'`

	grep  "$config_name" $config_file > /dev/null
	if [ $? != 0 ]; then
		echo "$config_name -- is not in $config_file"
		exit 1
	fi

done < $default_config

exit 0
