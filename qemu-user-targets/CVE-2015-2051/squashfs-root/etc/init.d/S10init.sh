#!/bin/sh
mount -t proc none /proc
mount -t ramfs ramfs /var
mount -t sysfs sysfs /sys
mount -t usbfs usbfs /proc/bus/usb
echo 7 > /proc/sys/kernel/printk
echo 1 > /proc/sys/vm/panic_on_oom
