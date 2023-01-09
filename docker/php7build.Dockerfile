FROM witcher/basebuild as basebuild
#FROM ubuntu:20.04

MAINTAINER tricke

ENV DEBIAN_FRONTEND noninteractive

ARG ARG_PHP_VER=7
ENV PHP_VER=${ARG_PHP_VER}
ENV PHP_INI_DIR="/etc/php/"
ENV LD_LIBRARY_PATH="/wclibs"
ENV PROF_FLAGS="-lcgiwrapper -I/wclibs"
ENV CPATH="/wclibs"

RUN mkdir -p $PHP_INI_DIR/conf.d /phpsrc
COPY repo /phpsrc

COPY witcher-php-install/php-7.3.3-witcher.patch /phpsrc/witcher.patch
COPY witcher-php-install/zend_witcher_trace.c witcher-php-install/zend_witcher_trace.h /phpsrc/Zend/

RUN apt-get update && apt-get install -y apache2 apache2-dev

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
#
RUN cd /phpsrc && make install \
	&& printf "\033[36m[Witcher] PHP $PHP_VER Install completed \033[0m\n" \
    \
