#!/bin/sh

[ "$#" -lt 2 ] && echo "Usage: $0 NFS_SERVER_IP USERNAME" && exit 1

HOST=$1
USER=$2

MOUNT="mount"
SUBDIRS="etc htdocs lib usr sbin www"

for i in $SUBDIRS; do
  $MOUNT -onolock $HOST:/nfsroot/$USER/elbox/$i /$i
done

