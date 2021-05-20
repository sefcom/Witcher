#!/bin/bash

for f in fuzzer-master/crashes/id*; 
do 
	echo "$f";
	parser.py "$f"; 
	echo; 
	echo; 
done; 

echo "ALL: "
cd fuzzer-master/crashes/
/bin/ls id* | cat
cd -
