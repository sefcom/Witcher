FROM witcher/basebuildrun
MAINTAINER tricke

RUN curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash - && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && \
    apt-get install -y git-core zlib1g-dev build-essential libssl-dev libreadline-dev libyaml-dev libsqlite3-dev \
                       sqlite3 libxml2-dev libxslt1-dev libcurl4-openssl-dev software-properties-common libffi-dev \
                       yarn build-essential git autoconf ruby

COPY --chown=wc:wc rubysrc271 /rubysrc/

#RUN cd /rubysrc && autoconf && ./configure && make -j$(nproc)
#RUN cd /rubysrc && make install && gem install bundler
#RUN gem install rails -v 6.0.2.2
#COPY config/supervisord.conf /etc/supervisord.conf
#
#CMD /usr/bin/supervisord

#RUN chmod +x /run-php-test.sh

#RUN cp /bin/dash /bin/dash_backup; cp /crashing_dash /bin/dash
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









