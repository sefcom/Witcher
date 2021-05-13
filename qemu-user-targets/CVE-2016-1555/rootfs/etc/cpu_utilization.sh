#!/bin/sh
	top -n 1 | grep idle | awk '{print $8}' | cut -f1 -d% > /tmp/cpu_utilization.txt
	sed -i '2d' /tmp/cpu_utilization.txt
