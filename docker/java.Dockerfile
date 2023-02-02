FROM witcher/basebuildrun

MAINTAINER tricke

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

COPY config/py_aff.alias /root/py_aff.alias
RUN cat /root/py_aff.alias >> /home/wc/.bashrc && printf "\n\n" >> /home/wc/.bashrc

COPY --from=witcher/build-widash-x86 /Widash/archbuilds/dash /bin/dash

EXPOSE 14000
#CMD /opt/tomcat/port_14000/bin/catalina.sh start && bash
CMD /bin/true && /usr/bin/supervisord

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









