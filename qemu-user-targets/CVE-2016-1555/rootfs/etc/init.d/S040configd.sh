#!/bin/sh
#
# Controls Configd
#
[ -f ${CONFIGD} ] || exit 0

start() {
        ncecho 'Starting configd.           '
	start-stop-daemon -S -b -q --exec ${CONFIGD}
}

stop() {
        ncecho 'Stoping configd.            '
	start-stop-daemon -K -b -q --exec ${CONFIGD}
}

restart() {
        ncecho 'Restarting configd.         '
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
