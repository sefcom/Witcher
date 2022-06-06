FROM witcher/basebuild as basebuild
#FROM ubuntu:20.04

MAINTAINER tricke

ENV DEBIAN_FRONTEND noninteractive
#RUN mkdir /php5 && chown wc:wc /php5 -R
RUN apt-fast update && apt-get update
RUN apt-fast install -y libcurl4-gnutls-dev apache2 apache2-dev
COPY wclibs /wclibs

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


