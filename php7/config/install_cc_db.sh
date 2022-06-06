#!/bin/bash

# Initialize MySQL database.
# ADD this file into the container via Dockerfile.
# Assuming you specify a VOLUME ["/var/lib/mysql"] or `-v /var/lib/mysql` on the `docker run` command…
# Once built, do e.g. `docker run your_image /path/to/docker-mysql-initialize.sh`
# Again, make sure MySQL is persisting data outside the container for this to have any effect.

set -e
set -x

#/usr/sbin/mysqld --initialize

# Start the MySQL daemon in the background.
/usr/bin/pidproxy /var/run/mysqld/mysqld.pid /usr/sbin/mysqld &
mysql_pid=$!

until mysqladmin ping >/dev/null 2>&1; do
  echo -n "."; sleep 0.2
  ps -ef |grep mysql
done

# Permit root login without password from outside container.
#mysql -e "GRANT ALL ON *.* TO root@'%' IDENTIFIED BY '' WITH GRANT OPTION"

# create the default database from the ADDed file.
mysql < /root/cc_create.sql

mysql witchercc -e "select count(*) from page"

# Tell the MySQL daemon to shutdown.
#/etc/init.d/mysql stop
echo "MYSQL pid = $mysql_pid"
ps -ef |grep mysql
kill $mysql_pid
wait $mysql_pid
ps -ef |grep mysql
# Wait for the MySQL daemon to exit.

echo "Completed"