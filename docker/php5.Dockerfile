# webcam:php5
FROM witcher
MAINTAINER tricke

#RUN mkdir /php5 && chown wc:wc /php5 -R
RUN apt-fast update && apt-get update
RUN apt-fast install -y libcurl4-gnutls-dev

COPY installs /tmp
RUN dpkg -i /tmp/libbison-dev_2.7.1.dfsg-1_amd64.deb && dpkg -i /tmp/bison_2.7.1.dfsg-1_amd64.deb
#COPY php-cgi-* /php/
ARG ARG_PHP_VER=5.5
ENV PHP_VER=${ARG_PHP_VER}
ENV PHP_INI_DIR="/etc/php/"
ENV LD_LIBRARY_PATH="/wclibs"
ENV PROF_FLAGS="-lcgiwrapper -I/wclibs"
ENV CPATH="/wclibs"

RUN mkdir -p $PHP_INI_DIR/conf.d /phpsrc
COPY phpsrc$PHP_VER /phpsrc

RUN cd /phpsrc &&         \
        ./configure       \
#		--with-config-file-path="$PHP_INI_DIR" \
#		--with-config-file-scan-dir="$PHP_INI_DIR/conf.d" \
        --with-apxs2=/usr/bin/apxs \
#		\
		--enable-cgi      \
		--enable-ftp      \
		--enable-mbstring \
		--with-gd         \
		\
		--with-mysql      \
		--with-pdo-mysql  \
		--with-zlib       \
	&& printf "\033[36m[Witcher] PHP $PHP_VER Configure completed \033[0m\n"

RUN sed -i 's/CFLAGS_CLEAN = /CFLAGS_CLEAN = -L\/wclibs -lcgiwrapper -I\/wclibs /g' /phpsrc/Makefile \
    && cd /phpsrc \
	&& make -j \
	&& printf "\033[36m[Witcher] PHP $PHP_VER Make completed \033[0m\n"

RUN cd /phpsrc && make install \
	&& printf "\033[36m[Witcher] PHP $PHP_VER Install completed \033[0m\n"


######### apache and php setup
ENV APACHE_RUN_DIR=/etc/apache2/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
# RUN ln -s /etc/php/7.1/mods-available/mcrypt.ini /etc/php/7.3/mods-available/ && phpenmod mcrypt

RUN sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf && \
  sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

# change apache to forking instead of thread
RUN rm /etc/apache2/mods-enabled/mpm_event.* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

#RUN wget http://pear.php.net/go-pear.phar --quiet -O /tmp/go-pear.phar
#RUN echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/go-pear && chmod +x /usr/bin/go-pear
#RUN cd /tmp && /usr/bin/go-pear && rm /usr/bin/go-pear
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/php5.conf /etc/apache2/mods-available/
#COPY config/php.ini /etc/php/5.5/apache2/php.ini
#COPY config/php.ini /etc/php/5.5/cli/php.ini

RUN ln -s /etc/apache2/mods-available/php5.conf /etc/apache2/mods-enabled/php5.conf
#    &&  ln -s /etc/apache2/mods-available/php5.load /etc/apache2/mods-enabled/php5.load

RUN a2enmod rewrite
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M
RUN rm -fr /var/www/html && ln -s /app /var/www/html


#RUN chmod +x /run-php-test.sh

#COPY supervisord.conf /etc/supervisord.conf

##### MyCloud
#RUN apt-get install -y qemu-user-static
#
#COPY mycloud/code/. /app/
#RUN chown wc:wc /app -R && chmod 766 /app/*
#
#RUN mkdir /test
#COPY mycloud/sifter_data.json /test/
#COPY mycloud/test_data.json /test
#
#RUN chown wc:wc /test -R
## if fresh binwalk copy of squashfs then need to run these for nginx to work
## RUN cd /emulate/squashfs && rm etc && mv etc_ro etc && mkdir -p var/lib/nginx/ \
##    && mkdir -p var/nginx/ && touch /dev/null mkdir -p var/run/
#
#### END Tenda
#
#CMD /netconf.sh && /usr/bin/supervisord









