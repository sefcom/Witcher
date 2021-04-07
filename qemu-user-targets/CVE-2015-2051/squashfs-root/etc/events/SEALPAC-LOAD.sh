#!/bin/sh
dev=`xmldbc -g /runtime/devdata/lanpack`
cd /var
# dettach the lang pack.
xmldbc -P /etc/events/SEALPAC.php -V FILE=
rm -f sealpac/*
# extract the sealpac tarball from flash.
seama -x sealpac.tgz -i $dev -m type=sealpac
if [ -f sealpac.tgz ]; then
	tar -jxf sealpac.tgz
	if [ ! -e sealpac ]; then
		tar -zxf sealpac.tgz
	else
		if [ ! -f sealpac/sealpac.slp ]; then
			tar -zxf sealpac.tgz
		fi
	fi
	if [ -e sealpac -a -f sealpac/sealpac.slp ]; then
		xmldbc -P /etc/events/SEALPAC.php -V FILE=/var/sealpac/sealpac.slp
		rm -f sealpac.tgz
		exit 0
	fi
fi
if [ -f /etc/sealpac/en.slp ]; then
    xmldbc -P /etc/events/SEALPAC.php -V FILE=/etc/sealpac/en.slp
fi
