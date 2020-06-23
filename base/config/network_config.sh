#!/usr/bin/env bash

# this flag tells ipv4 to re-use connections in time_wait
# an app using a database may run into a problem where too many connections are in time_wait (openemr)

echo 1 > /proc/sys/net/ipv4/tcp_tw_reuse
echo 15000 64000 > /proc/sys/net/ipv4/ip_local_port_range