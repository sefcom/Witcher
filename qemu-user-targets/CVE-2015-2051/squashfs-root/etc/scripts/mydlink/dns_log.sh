#!/bin/sh

str=$1","$2","$3","$4","$5

xmldbc -P /htdocs/web/dnslog.php -V RAW_VALUE=$str
