
FROM ubuntu:bionic
LABEL maintainer="erik.trickel@gmail.com"

# Use the fastest APT repo
#COPY ./files/sources.list.with_mirrors /etc/apt/sources.list
RUN dpkg --add-architecture i386
RUN apt update

ENV DEBIAN_FRONTEND noninteractive

# Install apt-fast to speed things up
RUN apt install -y aria2 curl wget virtualenvwrapper

RUN apt install -y git

#APT-FAST installation
RUN /bin/bash -c "$(curl -sL https://git.io/vokNn)"
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
                        python3-pip python3-pexpect \
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
RUN add-apt-repository -y ppa:ondrej/php && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
RUN apt-fast update && apt update
RUN apt-fast install -y php7.3-xdebug  libapache2-mod-php7.3 php7.3-mysql php7.3-apcu php7.1-mcrypt \
                        php7.3-gd php7.3-xml php7.3-mbstring php7.3-gettext php7.3-zip php7.3-curl \
                        php7.3-gmp php7.3-cli

# Create wc user
RUN useradd -s /bin/bash -m wc
# Add wc to sudo group
RUN usermod -aG sudo wc
RUN echo "wc ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

RUN su - wc -c "source /usr/share/virtualenvwrapper/virtualenvwrapper.sh && mkvirtualenv -p `which python3` webcam"
RUN su - wc -c "echo 'source /usr/share/virtualenvwrapper/virtualenvwrapper.sh' >> /home/wc/.bashrc"
RUN su - wc -c "echo 'workon webcam' >> /home/wc/.bashrc"

######### Install phuzzer stuff
RUN apt-fast install -y libxss1 bison

RUN su - wc -c "source /home/wc/.virtualenvs/webcam/bin/activate && pip install protobuf termcolor "

RUN su - wc -c "source /home/wc/.virtualenvs/webcam/bin/activate && pip install git+https://github.com/etrickel/phuzzer"

######### last installs, b/c don't want to wait for phuzzer stuff again.
RUN apt-fast install -y jq
RUN wget https://github.com/sharkdp/bat/releases/download/v0.15.0/bat_0.15.0_amd64.deb -O /root/bat15.deb && sudo dpkg -i /root/bat15.deb

######### apache and php setup
ENV APACHE_RUN_DIR=/etc/apache2/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
# RUN ln -s /etc/php/7.1/mods-available/mcrypt.ini /etc/php/7.3/mods-available/ && phpenmod mcrypt

RUN sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf && \
  sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

RUN wget http://pear.php.net/go-pear.phar --quiet -O /tmp/go-pear.phar
RUN echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/go-pear && chmod +x /usr/bin/go-pear
RUN cd /tmp && /usr/bin/go-pear && rm /usr/bin/go-pear

COPY config/php.ini /etc/php/7.3/apache2/php.ini
COPY config/php.ini /etc/php/7.3/cli/php.ini

RUN a2enmod rewrite
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M
RUN mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html


######### wc's environment setup
USER wc
WORKDIR /home/wc
RUN mkdir -p /home/wc/tmp/emacs-saves
RUN git clone -q https://github.com/etrickel/docker_env.git
RUN chown wc:wc -R . && cp -r /home/wc/docker_env/. .
COPY config/.bash_prompt /home/wc/.bash_prompt
RUN mkdir /home/wc/.ssh && cat pubkeys/* >> /home/wc/.ssh/authorized_keys && chmod 400 /home/wc/.ssh/* && rm -rf pubkeys

RUN echo 'workon webcam' >> /home/wc/.bashrc
######### root's bash and emacs profile
RUN sudo cp -r /home/wc/docker_env/. /root/

######### NodeJS and NPM Setup
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
RUN echo 'export NVM_DIR=$HOME/.nvm; . $NVM_DIR/nvm.sh; . $NVM_DIR/bash_completion' >> /home/wc/.bashrc
ENV NVM_DIR /home/wc/.nvm
RUN . $NVM_DIR/nvm.sh && nvm install node
RUN sudo mkdir /node_modules && sudo chown wc:wc /node_modules && sudo apt install -y npm
RUN . $NVM_DIR/nvm.sh && cd / && npm install puppeteer cheerio

USER root
COPY --chown=wc:wc /helpers/gremlins.min.js /app
RUN sudo chown www-data:wc /app

COPY config/supervisord.conf /etc/supervisord.conf
RUN if [ ! -d /run/sshd ]; then mkdir /run/sshd; chmod 0755 /run/sshd; fi
RUN mkdir /var/run/mysqld ; chown mysql:mysql /var/run/mysqld

RUN ln -s /p /projects

COPY config/network_config.sh /netconf.sh
RUN chmod +x /netconf.sh

ENV TZ=America/Phoenix
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "export TZ=$TZ" >> /home/wc/.bashrc
RUN echo "alias cdt='cd /p/webcam/php/tests'" >> /home/wc/.bashrc

#"Installing" the Witcher's Dash that abends on a parsing error when STRICT=1 is set.
COPY config/dash /bin/dash

COPY afl /afl
ENV AFL_PATH=/afl

COPY --chown=wc:wc helpers/ /helpers/
COPY --chown=wc:wc phuzzer /helpers/phuzzer
RUN su - wc -c "source /home/wc/.virtualenvs/webcam/bin/activate &&  cd /helpers/phuzzer && pip install -e ."

COPY --chown=wc:wc wclibs /wclibs


CMD /netconf.sh && /usr/bin/supervisord



