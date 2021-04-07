#!/bin/sh
# Let interfaces become non-host, kernel will not send router solicit or handle router advertisement
# To do so, just set forwarding to 1 (tom, 20130625)
echo 1 > /proc/sys/net/ipv6/conf/default/forwarding
echo 2 > /proc/sys/net/ipv6/conf/default/accept_dad
echo 1 > /proc/sys/net/ipv6/conf/default/disable_ipv6
