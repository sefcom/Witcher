##################################################################################################################################
FROM witcher/baserun as php7run
##################################################################################################################################

COPY --from=witcher/php7build /usr/local/bin/php /usr/local/bin/php-config /usr/local/bin/phpize /usr/local/bin/php-cgi /usr/local/bin/phar.phar /usr/local/bin/phpdbg /usr/local/bin/
COPY --from=witcher/php7build /usr/local/lib/php/build/ /usr/local/lib/php/build/

COPY --from=witcher/php7build /usr/local/include/php/ /usr/local/include/php/
COPY --from=witcher/php7build /usr/local/bin/ /usr/local/bin/
COPY --from=witcher/php7build /phpsrc/ext/xdebug /xdebug
COPY --from=witcher/php7build /usr/lib/apache2/modules/libphp7.so /usr/lib/apache2/modules/libphp7.so

######### apache, php, and crawler setup
RUN apt-fast install -y libpng16-16 net-tools ca-certificates fonts-liberation libappindicator3-1 libasound2 \
                        libatk-bridge2.0-0 libatk1.0-0  libc6 libcairo2 libcups2 libdbus-1-3  libexpat1 libfontconfig1 \
                        libgbm1 libgcc1 libglib2.0-0 libgtk-3-0  libnspr4 libnss3 libpango-1.0-0 libpangocairo-1.0-0 \
                        libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 \
                        libxi6 libxrandr2 libxrender1 libxss1 libxtst6 lsb-release wget xdg-utils \
                        php-xdebug
RUN php -i

ENV APACHE_RUN_DIR=/etc/apache2/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
# RUN ln -s /etc/php/7.1/mods-available/mcrypt.ini /etc/php/7.3/mods-available/ && phpenmod mcrypt

RUN sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf && \
  sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

# change apache to forking instead of thread
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
    && rm -f /etc/apache2/mods-enabled/mpm_prefork.* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

#RUN wget http://pear.php.net/go-pear.phar --quiet -O /tmp/go-pear.phar
#RUN echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/go-pear && chmod +x /usr/bin/go-pear
#RUN cd /tmp && /usr/bin/go-pear && rm /usr/bin/go-pear
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/php.ini /usr/local/lib/php.ini
COPY config/php.ini /etc/php/7.2/apache2/php.ini
COPY config/php7.conf config/php7.load /etc/apache2/mods-available/

RUN ln -s /etc/apache2/mods-available/php7.load /etc/apache2/mods-enabled/ && ln -s /etc/apache2/mods-available/php7.conf /etc/apache2/mods-enabled/

#COPY config/php.ini /etc/php/5.5/cli/php.ini

#RUN ln -s /etc/apache2/mods-available/php7.conf /etc/apache2/mods-enabled/php5.conf
#    &&  ln -s /etc/apache2/mods-available/php5.load /etc/apache2/mods-enabled/php5.load

RUN a2enmod rewrite
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M
RUN rm -fr /var/www/html && ln -s /app /var/www/html

#### XDEBUG

RUN cd /xdebug && phpize && ./configure --enable-xdebug && make -j $(nproc) && make install

COPY --chown=wc:wc  config/phpinfo_test.php config/db_test.php config/cmd_test.php config/run_segfault_test.sh /app/

# disable directory browsing in apache2
RUN sed -i 's/Indexes//g' /etc/apache2/apache2.conf && \
    echo "DirectoryIndex index.php index.phtml index.html index.htm" >> /etc/apache2/apache2.conf

# add index
COPY config/000-default.conf /etc/apache2/sites-available/

RUN printf '\nzend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20180731/xdebug.so\nxdebug.mode=coverage\nauto_prepend_file=/enable_cc.php\n\n' >> $(php -i |egrep "Loaded Configuration File.*php.ini"|cut -d ">" -f2|cut -d " " -f2)
RUN for fn in $(find /etc/php/ . -name 'php.ini'); do printf '\nzend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20180731/xdebug.so\nxdebug.mode=coverage\nauto_prepend_file=/enable_cc.php\n\n' >> $fn; done

#RUN echo alias p='python -m witcher --affinity $(( $(ifconfig |egrep -oh "inet 172[\.0-9]+"|cut -d "." -f4) * 2 ))' >> /home/wc/.bashrc
COPY config/py_aff.alias /root/py_aff.alias
RUN cat /root/py_aff.alias >> /home/wc/.bashrc

#RUN cp /bin/dash /bin/saved_dash && cp /crashing_dash /bin/dash
# there's a problem with building xdebug and the modifid dash, so copy after xdebug
COPY --from=witcher/basebuild /Widash/archbuilds/dash /bin/dash

COPY --chown=wc:wc  config/codecov_conversion.py config/enable_cc.php /

CMD /usr/bin/supervisord -c /etc/supervisord.conf








