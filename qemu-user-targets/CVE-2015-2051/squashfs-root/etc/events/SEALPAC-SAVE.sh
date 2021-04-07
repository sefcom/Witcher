#!/bin/sh
dev=`xmldbc -g /runtime/devdata/lanpack`
langcode=`cat /var/sealpac/langcode`
cd /var
tar -jcf sealpac.tgz sealpac
seama -i sealpac.tgz -m type=sealpac -m langcode=$lancode
cat sealpac.tgz.seama > $dev
rm -f sealpac.tgz sealpac.tgz.seama
reload=/htdocs/phplib/isplst.php
[ -f $reload ] && xmldbc -P $reload
