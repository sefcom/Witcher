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
                        openssh-server mysql-server \
                        # editors
                        vim emacs \
                        # analysis
                        afl qemu gdb \
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
RUN . $NVM_DIR/nvm.sh && nvm install node
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
#COPY config/dash /bin/dash
#COPY --from=hacrs/build-widash-x86 /Widash/archbuilds/dash /bin/dash
COPY --from=witcher/basebuild /httpreqr/httpreqr.64 /httpreqr

COPY afl /afl
ENV AFL_PATH=/afl

COPY --chown=wc:wc helpers/ /helpers/
COPY --chown=wc:wc phuzzer /helpers/phuzzer
RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  cd /helpers/phuzzer && pip install -e ."

COPY --chown=wc:wc witcher /witcher/
RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate &&  cd /witcher && pip install -e ."

RUN su - wc -c "source /home/wc/.virtualenvs/witcher/bin/activate && pip install ipython "
COPY --chown=wc:wc wclibs /wclibs
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


RUN curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash - && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && \
    apt-get install -y git-core openjdk-11-jdk zlib1g-dev build-essential libssl-dev libreadline-dev libyaml-dev libsqlite3-dev \
                       sqlite3 libxml2-dev libxslt1-dev libcurl4-openssl-dev software-properties-common libffi-dev \
                       yarn libx11-dev libxext-dev libxrender-dev libxtst-dev libxt-dev libcups2-dev libfontconfig1-dev \
                       libasound2-dev
COPY --chown=wc:wc /jdksrc/build/linux-x86_64-normal-server-release/ /jdk-release/
RUN ln -s /jdk-release/jdk /jdk
#RUN cd /jdksrc && bash ./configure -disable-warnings-as-errors && CONF="linux-x86_64-normal-server-release" make

COPY apache-tomcat-9.0.38.tar.gz /tmp/apache-tomcat.tar.gz

RUN mkdir -p /opt/tomcat && tar xf /tmp/apache-tomcat.tar.gz -C /opt/tomcat && \
    mv /opt/tomcat/apache-tomcat-9.0.38 /opt/tomcat/base && \
    chown -RH wc:wc /opt/tomcat/base && chmod +x /opt/tomcat/base/bin/*.sh

RUN rm -rf /opt/tomcat/base/webapps/* && ln -s /p/Witcher/java /j
COPY --chown=wc:wc config/ /config
COPY --chown=wc:wc tests/ /tests

RUN cp /config/mysql-connector-java-8.0.21.jar /opt/tomcat/base/lib/ && \
    cp /config/init-*.sh / && chmod +x /init-*.sh && /init-tomcats.sh && \
    cp /config/supervisord.conf /etc/supervisord.conf && \
    printf "\nskip_ssl\n# disable_ssl\n" >> /etc/mysql/mysql.cnf

EXPOSE 14000
#CMD /opt/tomcat/port_14000/bin/catalina.sh start && bash
CMD /bin/true && /usr/bin/supervisord





