#!/bin/sh
#
# Lighttpd
#

[ -f ${LIGHTTPD} ] || exit 0

start() {
        ncecho 'Starting web server.        '
	start-stop-daemon -S -q -b -x ${LIGHTTPD} -- -f ${LIGHTTPD_CONF}
}

stop() {
        ncecho 'Stoping web server.         '
	start-stop-daemon -K -q -b -x ${LIGHTTPD} -- -f ${LIGHTTPD_CONF}
}

restart() {
        ncecho 'Restarting web server.      '
	stop
	start
}

case "$1" in
  start)
        start
        cecho green '[DONE]'
        ;;
  stop)
        stop
        cecho green '[DONE]'
        ;;
  restart|reload)
        restart
        cecho green '[DONE]'
        ;;
  *)
        echo $"Usage: $0 {start|stop|restart}"
        exit 1
esac

exit $?
