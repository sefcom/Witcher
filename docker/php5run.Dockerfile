##################################################################################################################################
FROM witcher/baserun as php5run
##################################################################################################################################

# removed /usr/local/bin/phpdbg
COPY --from=witcher/php5build /usr/local/bin/php /usr/local/bin/php-config /usr/local/bin/phpize /usr/local/bin/php-cgi /usr/local/bin/phar.phar /usr/local/bin/
COPY --from=witcher/php5build /usr/local/lib/php/build/ /usr/local/lib/php/build/

COPY --from=witcher/php5build /usr/local/include/php/ /usr/local/include/php/
COPY --from=witcher/php5build /usr/local/bin/ /usr/local/bin/
COPY --from=witcher/php5build /phpsrc/ext/xdebug /xdebug
COPY --from=witcher/php5build /usr/lib/apache2/modules/libphp5.so /usr/lib/apache2/modules/libphp5.so

COPY wclibs /wclibs

######### apache and php setup
ENV APACHE_RUN_DIR=/etc/apache2/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
# RUN ln -s /etc/php/7.1/mods-available/mcrypt.ini /etc/php/7.3/mods-available/ && phpenmod mcrypt

RUN sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf && \
  sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

RUN cd /xdebug && phpize && ./configure --enable-xdebug && make -j $(nproc) && make install

# change apache to forking instead of thread
RUN rm /etc/apache2/mods-enabled/mpm_event.* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

COPY config/supervisord.conf /etc/supervisord.conf
COPY config/php5.conf config/php5.load /etc/apache2/mods-available/
COPY config/phpinfo_test.php /app

COPY config/php.ini /etc/php/5.5/apache2/php.ini
COPY config/enable_cc.php /

RUN ln -s /etc/apache2/mods-available/php5.conf /etc/apache2/mods-enabled/php5.conf && \
    ln -s /etc/apache2/mods-available/php5.load /etc/apache2/mods-enabled/php5.load

#RUN ls -lah /etc/apache2/mods-enabled/php* && rm /etc/apache2/mods-available/php7.4.load

COPY config/py_aff.alias /root/py_aff.alias
RUN cat /root/py_aff.alias >> /home/wc/.bashrc

RUN a2enmod rewrite
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M
RUN rm -fr /var/www/html && ln -s /app /var/www/html

COPY --from=witcher/basebuild /Widash/archbuilds/dash /bin/dash










