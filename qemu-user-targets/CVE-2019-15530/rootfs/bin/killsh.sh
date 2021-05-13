#!/bin/sh
TMPFILE=/tmp/tmpfile
line=0
ps | grep '\.sh' > $TMPFILE
line=`cat $TMPFILE | wc -l`
num=1
while [ $num -le $line ];
do
	pat0=` head -n $num $TMPFILE | tail -n 1`
	pat1=`echo $pat0 | cut -f1 -dS`  
	pat2=`echo $pat1 | cut -f1 -d " "`  
	kill -9 $pat2 2> /dev/null

	num=`expr $num + 1`
done

