# webcam:php7
FROM witcher
MAINTAINER tricke

# PHP 7.3 installation
#RUN add-apt-repository -y ppa:ondrej/php && \
#    apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
#RUN apt-fast update && apt update
#RUN apt-fast install -y php7.3-xdebug  libapache2-mod-php7.3 php7.3-mysql php7.3-apcu php7.1-mcrypt \
#                        php7.3-gd php7.3-xml php7.3-mbstring php7.3-gettext php7.3-zip php7.3-curl \
#                        php7.3-gmp php7.3-cli
RUN cp /wclibs/libcgiwrapper.so /lib

ARG ARG_PHP_VER=7
ENV PHP_VER=${ARG_PHP_VER}
ENV PHP_INI_DIR="/etc/php/"
ENV LD_LIBRARY_PATH="/wclibs"
ENV PROF_FLAGS="-lcgiwrapper -I/wclibs"
ENV CPATH="/wclibs"

RUN mkdir -p $PHP_INI_DIR/conf.d /phpsrc
COPY repo /phpsrc

COPY witcher-php-install/php-7.3.3-witcher.patch /phpsrc/witcher.patch
COPY witcher-php-install/zend_witcher_trace.c /phpsrc/Zend/
COPY witcher-php-install/zend_witcher_trace.h /phpsrc/Zend/

RUN cd /phpsrc && git apply ./witcher.patch && ./buildconf --force

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
		--with-ssl      \
		--with-mysqli      \
		--with-pdo-mysql  \
		--with-zlib       \
	&& printf "\033[36m[Witcher] PHP $PHP_VER Configure completed \033[0m\n"

#RUN sed -i 's/CFLAGS_CLEAN = /CFLAGS_CLEAN = -L\/wclibs -lcgiwrapper -I\/wclibs /g' /phpsrc/Makefile \
RUN cd /phpsrc \
	&& make clean &&  EXTRA_CFLAGS="-DWITCHER_DEBUG=1" make -j $(nproc) \
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
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
    && rm -f /etc/apache2/mods-enabled/mpm_prefork.* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

#RUN wget http://pear.php.net/go-pear.phar --quiet -O /tmp/go-pear.phar
#RUN echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/go-pear && chmod +x /usr/bin/go-pear
#RUN cd /tmp && /usr/bin/go-pear && rm /usr/bin/go-pear
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/php7.conf /etc/apache2/mods-enabled/
COPY config/php.ini /usr/local/lib/php.ini
#COPY config/php.ini /etc/php/5.5/cli/php.ini

#RUN ln -s /etc/apache2/mods-available/php7.conf /etc/apache2/mods-enabled/php5.conf
#    &&  ln -s /etc/apache2/mods-available/php5.load /etc/apache2/mods-enabled/php5.load

RUN a2enmod rewrite
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M
RUN rm -fr /var/www/html && ln -s /app /var/www/html

#### XDEBUG
RUN cd /phpsrc/ext/xdebug && phpize && ./configure --enable-xdebug && make -j $(nproc) && make install

COPY --chown=wc:wc  config/phpinfo_test.php /app
COPY --chown=wc:wc  config/db_test.php /app
COPY --chown=wc:wc  config/cmd_test.php /app
COPY --chown=wc:wc  config/run_segfault_test.sh /app

# disable directory browsing in apache2
RUN sed -i 's/Indexes//g' /etc/apache2/apache2.conf && \
    echo "DirectoryIndex index.php index.phtml index.html index.htm" >> /etc/apache2/apache2.conf

# add index


RUN printf '\nzend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20180731/xdebug.so\nxdebug.mode=coverage\nauto_prepend_file=/enable_cc.php\n\n' >> $(php -i |egrep "Loaded Configuration File.*php.ini"|cut -d ">" -f2|cut -d " " -f2)

#RUN echo alias p='python -m witcher --affinity $(( $(ifconfig |egrep -oh "inet 172[\.0-9]+"|cut -d "." -f4) * 2 ))' >> /home/wc/.bashrc
COPY config/py_aff.alias /root/py_aff.alias
RUN cat /root/py_aff.alias >> /home/wc/.bashrc


#RUN cp /bin/dash /bin/saved_dash && cp /crashing_dash /bin/dash
COPY --from=hacrs/build-widash-x86 /Widash/archbuilds/dash /bin/dash
COPY --chown=wc:wc  config/enable_cc.php /



#### NAVEX

#RUN apt-get update && apt-get -y install openjdk-8-jdk python3-dev graphviz libgraphviz-dev pkg-config lsof daemon
#RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install pygraphviz "
#
#RUN su - wc -c "source /usr/share/virtualenvwrapper/virtualenvwrapper.sh && mkvirtualenv -p `which python2` py2witcher"
#RUN su - wc -c "source /home/wc/.virtualenvs/py2witcher/bin/activate && pip install py2neo==2.0.7 "

#RUN add-apt-repository ppa:openjdk-r/ppa && \
#    apt-get update && \
#    apt-get install -y openjdk-7-jdk && \
#    apt-get install -y ant && \
#    apt-get clean;

#COPY --chown=wc:wc navex /navex/
#
#WORKDIR /navex
#
#RUN cd php-ast &&  phpize && ./configure && make -j && make install
#RUN dpkg -i /navex/neo4j_2.1.5_all.deb
#RUN cd joern && ../gradle-4.2/bin/gradle build -x test
#PHP7=$(which php) LD_LIBRARY_PATH=/wclibs ./php2ast /p/Witcher/php7/tests/openemr/code/index.php


#RUN su - wc -c "source /home/wc/.virtualenvs/py2witcher/bin/activate && cd /navex/python-joern/ && pip install -e ."

CMD /usr/bin/supervisord



#COPY supervisord.conf

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









