#!/bin/sh
dev=`xmldbc -g /runtime/devdata/lanpack`
cd /var
# dettach the lang pack.
xmldbc -P /etc/events/SEALPAC.php -V FILE=
rm -f sealpac/*
echo FFFFFFFF > $dev
reload=/htdocs/phplib/isplst.php
[ -f $reload ] && xmldbc -P $reload
