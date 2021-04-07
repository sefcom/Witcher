#!/bin/sh

CPULOADPRE="-1"
NETBUDGET="/proc/sys/net/core/netdev_budget"
NETMAXBACKLOG="/proc/sys/net/core/netdev_max_backlog"

echo 300 > $NETBUDGET
echo 1000 > $NETMAXBACKLOG

while :
do
  CPULOADNOW=`cat /proc/loadavg | cut -d '.' -f 1`
#  echo "CPU loading : "$CPULOADNOW

  if [ "$CPULOADNOW" -ge "10" ]; then
    CPULOADNOW=10
  elif [ "$CPULOADNOW" -ge "6" ]; then
    CPULOADNOW=6
  else
    CPULOADNOW=0
  fi

  if [ $CPULOADNOW -ne $CPULOADPRE ]; then
    if [ $CPULOADNOW -eq 10 ]; then
      echo 8 > $NETBUDGET
      echo 100 > $NETMAXBACKLOG
    elif [ $CPULOADNOW -eq 6 ]; then
      echo 32 > $NETBUDGET
      echo 200 > $NETMAXBACKLOG
    else
      echo 300 > $NETBUDGET
      echo 1000 > $NETMAXBACKLOG
    fi
    CPULOADPRE=$CPULOADNOW
  fi

  sleep 1
done
