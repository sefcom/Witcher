ARG BASE_BUILD=ubuntu:bionic

FROM $BASE_BUILD
LABEL maintainer="erik.trickel@asu.edu"

# Use the fastest APT repo
#COPY ./files/sources.list.with_mirrors /etc/apt/sources.list
RUN dpkg --add-architecture i386
RUN apt-get update

ENV DEBIAN_FRONTEND noninteractive


# Install apt-fast to speed things up
RUN apt-get install -y aria2 curl wget virtualenvwrapper

RUN apt-get install -y git

#APT-FAST installation
RUN /bin/bash -c "$(curl -sL https://git.io/vokNn) "
RUN apt-fast update

RUN apt-fast -y upgrade

# Install all APT packages

RUN apt-fast install -y git build-essential  binutils-multiarch nasm \
                        #Libraries
                        libxml2-dev libxslt1-dev libffi-dev cmake libreadline-dev \
                        libtool debootstrap debian-archive-keyring libglib2.0-dev libpixman-1-dev \
                        libssl-dev qtdeclarative5-dev libcapnp-dev libtool-bin \
                        libcurl4-nss-dev libpng-dev libgmp-dev \
                        # x86 Libraries
                        libc6:i386 libgcc1:i386 libstdc++6:i386 libtinfo5:i386 zlib1g:i386 \
                        #python 3
                        python3-pip python3-pexpect ipython3 \
                        #Utils
                        sudo openssh-server automake rsync net-tools netcat openssh-client \
                        ccache make g++-multilib pkg-config coreutils rsyslog \
                        manpages-dev ninja-build capnproto  software-properties-common zip unzip pwgen \
                        # other stuff
                        openssh-server \
                        # editors
                        vim emacs \
                        # analysis
                        afl qemu gdb \
                        # web
                        supervisor



# Create wc user
RUN useradd -s /bin/bash -m wc
# Add wc to sudo group
RUN usermod -aG sudo wc
RUN echo "wc ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

RUN su - wc -c "source /usr/share/virtualenvwrapper/virtualenvwrapper.sh && mkvirtualenv -p `which python3` witcher"

######### Install phuzzer stuff
RUN apt-fast install -y libxss1 bison

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install protobuf termcolor "

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install git+https://github.com/etrickel/phuzzer"

######### last installs, b/c don't want to wait for phuzzer stuff again.
RUN apt-fast install -y jq
RUN wget https://github.com/sharkdp/bat/releases/download/v0.15.0/bat_0.15.0_amd64.deb -O /root/bat15.deb && sudo dpkg -i /root/bat15.deb


######### wc's environment setup
USER wc
WORKDIR /home/wc
RUN mkdir -p /home/wc/tmp/emacs-saves
RUN git clone -q https://github.com/etrickel/docker_env.git
RUN chown wc:wc -R . && cp -r /home/wc/docker_env/. .
COPY base/config/.bash_prompt /home/wc/.bash_prompt
RUN mkdir /home/wc/.ssh && cat pubkeys/* >> /home/wc/.ssh/authorized_keys && chmod 400 /home/wc/.ssh/* && rm -rf pubkeys

RUN echo 'source /usr/share/virtualenvwrapper/virtualenvwrapper.sh' >> /home/wc/.bashrc
RUN echo 'workon witcher' >> /home/wc/.bashrc

######### root's bash and emacs profile
RUN sudo cp -r /home/wc/docker_env/. /root/

######### NodeJS and NPM Setup
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
RUN echo 'export NVM_DIR=$HOME/.nvm; . $NVM_DIR/nvm.sh; . $NVM_DIR/bash_completion' >> /home/wc/.bashrc
ENV NVM_DIR /home/wc/.nvm
RUN . $NVM_DIR/nvm.sh && nvm install node
#RUN sudo mkdir /node_modules && sudo chown wc:wc /node_modules && sudo apt-get install -y npm
RUN sudo apt-get install -y npm
RUN . $NVM_DIR/nvm.sh && npm install puppeteer cheerio

USER root
RUN mkdir /app && chown www-data:wc /app
COPY --chown=wc:wc base/helpers/gremlins.min.js /app


COPY base/config/supervisord.conf /etc/supervisord.conf
RUN if [ ! -d /run/sshd ]; then mkdir /run/sshd; chmod 0755 /run/sshd; fi
RUN mkdir /var/run/mysqld ; chown mysql:mysql /var/run/mysqld
# mysql configuration for disk access, used when running 25+ containers on single system
RUN printf "[mysqld]\ninnodb_use_native_aio = 0\n" >> /etc/mysql/my.cnf

RUN ln -s /p /projects

COPY base/config/network_config.sh /netconf.sh
RUN chmod +x /netconf.sh

ENV TZ=America/Phoenix
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "export TZ=$TZ" >> /home/wc/.bashrc

RUN usermod -a -G www-data wc

#"Installing" the Witcher's Dash that abends on a parsing error when STRICT=1 is set.
#COPY config/dash /bin/dash
#COPY --from=hacrs/build-widash-x86 /Widash/archbuilds/dash /bin/dash

COPY --from=witcher/basebuild /httpreqr/httpreqr.64 /httpreqr

COPY base/afl /afl
ENV AFL_PATH=/afl

COPY --chown=wc:wc base/helpers/ /helpers/
COPY --chown=wc:wc base/phuzzer /helpers/phuzzer
RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  cd /helpers/phuzzer && pip install -e ."

COPY --chown=wc:wc base/witcher /witcher/
RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  cd /witcher && pip install -e ."

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install ipython archr "
COPY --chown=wc:wc base/wclibs /wclibs
COPY base/wclibs/lib_db_fault_escalator.so /lib
#COPY --chown=wc:wc bins /bins

#COPY --chown=wc:wc httpreqr /httpreqr

ENV CONTAINER_NAME="witcher"
ENV WC_TEST_VER="EXWICHR"
ENV WC_FIRST=""
ENV WC_CORES="10"
ENV WC_TIMEOUT="600"
ENV WC_SET_AFFINITY="0"
# single script takes "--target scriptname"
ENV WC_SINGLE_SCRIPT=""

RUN mkdir -p /test && chown wc:wc /test

CMD /netconf.sh && /usr/bin/supervisord


RUN cp /wclibs/lib_db_fault_escalator.so /lib

ARG ARG_PHP_VER=7
ENV PHP_VER=${ARG_PHP_VER}
ENV PHP_INI_DIR="/etc/php/"
ENV LD_LIBRARY_PATH="/wclibs"
ENV PROF_FLAGS="-lcgiwrapper -I/wclibs"
ENV CPATH="/wclibs"

RUN mkdir -p $PHP_INI_DIR/conf.d /phpsrc
COPY php7/phpsrc$PHP_VER /phpsrc

RUN cd /phpsrc &&         \
        ./configure       \
#		--with-config-file-path="$PHP_INI_DIR" \
#		--with-config-file-scan-dir="$PHP_INI_DIR/conf.d" \
#        --with-apxs2=/usr/bin/apxs \
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

RUN sed -i 's/CFLAGS_CLEAN = /CFLAGS_CLEAN = -L\/wclibs -lcgiwrapper -I\/wclibs /g' /phpsrc/Makefile \
    && cd /phpsrc \
	&& make clean &&  make -j \
	&& printf "\033[36m[Witcher] PHP $PHP_VER Make completed \033[0m\n"

#RUN cd /phpsrc && make install \
#	&& printf "\033[36m[Witcher] PHP $PHP_VER Install completed \033[0m\n"


######### apache and php setup
#ENV APACHE_RUN_DIR=/etc/apache2/
#RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
## RUN ln -s /etc/php/7.1/mods-available/mcrypt.ini /etc/php/7.3/mods-available/ && phpenmod mcrypt
#
#RUN sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf && \
#  sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

# change apache to forking instead of thread
#RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
#    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load || true\
#    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf || true

#RUN wget http://pear.php.net/go-pear.phar --quiet -O /tmp/go-pear.phar
#RUN echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/go-pear && chmod +x /usr/bin/go-pear
#RUN cd /tmp && /usr/bin/go-pear && rm /usr/bin/go-pear
COPY php7/config/supervisord.conf /etc/supervisord.conf
#COPY php7/config/php7.conf /etc/apache2/mods-enabled/
COPY php7/config/php.ini /usr/local/lib/php.ini
#COPY config/php.ini /etc/php/5.5/cli/php.ini

#RUN ln -s /etc/apache2/mods-available/php7.conf /etc/apache2/mods-enabled/php5.conf
#    &&  ln -s /etc/apache2/mods-available/php5.load /etc/apache2/mods-enabled/php5.load

#RUN a2enmod rewrite
#ENV PHP_UPLOAD_MAX_FILESIZE 10M
#ENV PHP_POST_MAX_SIZE 10M
#RUN rm -fr /var/www/html && ln -s /app /var/www/html

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

