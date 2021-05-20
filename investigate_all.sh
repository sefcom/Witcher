#!/bin/bash

find . -name id* | grep crashes | sed 's/\/fuzzer-master.*id/\tid/g' | sed 's/.*tr0_//g' | sed 's/\+/\//g'
echo
echo

for f in ./*/fuzzer-master; 
do 
	DDD=$(dirname "$f")
	echo "##############################  $DDD   ####################";
	cd $DDD
	investigate.sh
	cd -
	echo; 
	echo; 
	echo; 
	echo; 
	echo; 
done;

