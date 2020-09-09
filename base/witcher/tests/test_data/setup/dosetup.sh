#! /bin/bash

cd "$(dirname "$0")"
rm -rf  /results/unittests-EXWICHR/*
cp ../*.json /test
mkdir -p /app/test
cp ../code/*.php /app/test

echo "DROP DATABASE IF EXISTS testdb" | sudo mysql
echo "CREATE DATABASE testdb" | sudo mysql
echo "GRANT ALL PRIVILEGES on testdb.* to 'username'@'localhost' identified by 'password';FLUSH PRIVILEGES;"| sudo mysql


echo $'CREATE TABLE `user` (`id` int(11) NOT NULL AUTO_INCREMENT,`username` varchar(255) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=latin1;' | sudo mysql testdb
echo $'INSERT INTO `user` (`username`) VALUES ("trickE");' |sudo mysql testdb

val=$(echo 'SELECT * FROM user' | sudo mysql testdb |wc -l)

if [[ ${val} -eq 2 ]]; then
    echo "Setup completed successfully".
else
    echo "DB Setup failed"
    exit 241

fi





