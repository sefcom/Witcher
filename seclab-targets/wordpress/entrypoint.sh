#!/bin/sh

sudo service mysql start
mysql -e "CREATE DATABASE wordpress;"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'root'";
sudo service mysql stop

cd /app;
# Install wordpress from CLI
./wp-cli.phar core install --url="http://127.0.0.1/" --title=Witcher --admin_user=admin --admin_password=admin --admin_email=admin@admin.com --allow-root

# Remove these two lines if you are not interested in test data.
./wp-cli.phar plugin install wordpress-importer.0.7.zip --activate --allow-root
./wp-cli.phar import themeunittestdata.wordpress.xml --authors=create --allow-root

supervisord;
