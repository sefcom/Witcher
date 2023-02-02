## this base is used under the image name witcher, which is used by
FROM ubuntu:bionic
LABEL maintainer="erik.trickel@asu.edu"


# Use the fastest APT repo
#COPY ./files/sources.list.with_mirrors /etc/apt/sources.list
RUN apt-get update

ENV DEBIAN_FRONTEND noninteractive


# Install apt-fast to speed things up
RUN apt-get install -y aria2 curl wget virtualenvwrapper

RUN apt-get install -y git

#APT-FAST installation
RUN /bin/bash -c "$(curl -sL https://git.io/vokNn) "

RUN apt-fast update && apt-fast -y upgrade && apt-fast update

# Install all APT packages

RUN apt-fast install -y git build-essential  \
                        #Libraries
                        libxml2-dev libxslt1-dev libffi-dev cmake libreadline-dev \
                        libtool debootstrap debian-archive-keyring libglib2.0-dev libpixman-1-dev \
                        libssl-dev qtdeclarative5-dev libcapnp-dev libtool-bin \
                        libcurl4-nss-dev libpng-dev libgmp-dev \
                        # x86 Libraries
                        #libc6:i386 libgcc1:i386 libstdc++6:i386 libtinfo5:i386 zlib1g:i386 \
                        #python 3
                        python3-pip python3-pexpect ipython3 \
                        #Utils
                        sudo openssh-server automake rsync net-tools netcat  \
                        ccache make g++-multilib pkg-config coreutils rsyslog \
                        manpages-dev ninja-build capnproto  software-properties-common zip unzip pwgen \
                        # other stuff
                        openssh-server mysql-server \
                        # editors
                        vim emacs \
                        # analysis
                        afl qemu gdb patchelf \
                        # web
                        apache2 apache2-dev supervisor


RUN rm -rf /var/lib/mysql
RUN  /usr/sbin/mysqld --initialize-insecure

# PHP 7.3 installation
#RUN add-apt-repository -y ppa:ondrej/php && \
#    apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
#RUN apt-fast update && apt-get update
#RUN apt-fast install -y php7.3-xdebug  libapache2-mod-php7.3 php7.3-mysql php7.3-apcu php7.1-mcrypt \
#                        php7.3-gd php7.3-xml php7.3-mbstring php7.3-gettext php7.3-zip php7.3-curl \
#                        php7.3-gmp php7.3-cli

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
COPY config/.bash_prompt /home/wc/.bash_prompt
RUN mkdir /home/wc/.ssh && cat pubkeys/* >> /home/wc/.ssh/authorized_keys && chmod 400 /home/wc/.ssh/* && rm -rf pubkeys

RUN echo 'source /usr/share/virtualenvwrapper/virtualenvwrapper.sh' >> /home/wc/.bashrc
RUN echo 'workon witcher' >> /home/wc/.bashrc

######### root's bash and emacs profile
RUN sudo cp -r /home/wc/docker_env/. /root/

######### NodeJS and NPM Setup
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
RUN echo 'export NVM_DIR=$HOME/.nvm; . $NVM_DIR/nvm.sh; . $NVM_DIR/bash_completion' >> /home/wc/.bashrc
ENV NVM_DIR /home/wc/.nvm
RUN . $NVM_DIR/nvm.sh && nvm install 16
#RUN sudo mkdir /node_modules && sudo chown wc:wc /node_modules && sudo apt-get install -y npm
RUN sudo apt-get install -y npm
RUN . $NVM_DIR/nvm.sh && npm install puppeteer cheerio

USER root
RUN mkdir /app && chown www-data:wc /app
COPY --chown=wc:wc /helpers/gremlins.min.js /app


COPY config/supervisord.conf /etc/supervisord.conf
RUN if [ ! -d /run/sshd ]; then mkdir /run/sshd; chmod 0755 /run/sshd; fi
RUN mkdir /var/run/mysqld ; chown mysql:mysql /var/run/mysqld
# mysql configuration for disk access, used when running 25+ containers on single system
RUN printf "[mysqld]\ninnodb_use_native_aio = 0\n" >> /etc/mysql/my.cnf

RUN ln -s /p /projects

COPY config/network_config.sh /netconf.sh
RUN chmod +x /netconf.sh

ENV TZ=America/Phoenix
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "export TZ=$TZ" >> /home/wc/.bashrc

RUN usermod -a -G www-data wc

#"Installing" the Witcher's Dash that abends on a parsing error when STRICT=1 is set.
#

#COPY --from=hacrs/build-httpreqr /Witcher/base/httpreqr/httpreqr /httpreqr
#COPY --chown=wc:wc /httpreqr/httpreqr.64 /httpreqr
COPY --from=witcher/basebuild /httpreqr/httpreqr.64 /httpreqr

COPY afl /afl
ENV AFL_PATH=/afl

COPY --chown=wc:wc helpers/ /helpers/
COPY --chown=wc:wc phuzzer /helpers/phuzzer

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  pip install archr ipdb "

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  cd /helpers/phuzzer && pip install -e ."

COPY --chown=wc:wc witcher /witcher/
RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  cd /witcher && pip install -e ."

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install ipython "

COPY --chown=wc:wc wclibs /wclibs

RUN cd /wclibs && gcc -c -Wall -fpic db_fault_escalator.c && gcc -shared -o lib_db_fault_escalator.so db_fault_escalator.o -ldl

RUN rm -f /wclibs/libcgiwrapper.so && ln -s /wclibs/lib_db_fault_escalator.so /wclibs/libcgiwrapper.so && ln -s /wclibs/lib_db_fault_escalator.so /lib/libcgiwrapper.so

#COPY --chown=wc:wc bins /bins

COPY --from=witcher/build-widash-x86 /Widash/archbuilds/dash /crashing_dash

ENV CONTAINER_NAME="witcher"
ENV WC_TEST_VER="EXWICHR"
ENV WC_FIRST=""
ENV WC_CORES="10"
ENV WC_TIMEOUT="1200"
ENV WC_SET_AFFINITY="0"
# single script takes "--target scriptname"
ENV WC_SINGLE_SCRIPT=""

RUN mkdir -p /test && chown wc:wc /test

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install ply "

CMD /netconf.sh && /usr/bin/supervisord



