#!/bin/sh
#
# All the mount related things during bootup mustbe done here.
#

ncecho 'Mounting etc to ramfs.      '
${CP} -a /etc /tmp
${MOUNT} -t ${ETC_FS} -n none /etc # Check for the error.
${CP} -a /tmp/etc/. /etc
${RM} -rf /tmp/etc
if [ ${WORKAROUNDS} = "yes" ]; then
	sed -i 's/1000/0/g' /etc/passwd
fi

echo 'ncecho "System initilization is ..  "' >> /etc/init.d/S099final-touch.sh
echo 'cecho green "[DONE...]"' >> /etc/init.d/S099final-touch.sh

cecho green '[DONE]'

ncecho 'Mounting var to jffs2.      '

if ${MOUNT} | ${GREP} -q "\/dev\/root on \/ type ${RFS_IMAGE} (ro)" ; then
        ${MOUNT} -t ${VAR_FS} ${VAR_MTD_BLK} ${LOG_N_MANU_DIR}
	sleep 1 # Give enough time to GC to finish.
	cecho green '[DONE]'
else
	cecho red '[FAILED]'
fi
umask 0007
