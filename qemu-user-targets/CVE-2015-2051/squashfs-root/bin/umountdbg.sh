#!/bin/sh
SUBDIRS="etc htdocs lib usr sbin www"
for i in $SUBDIRS; do
  umount /$i
done

