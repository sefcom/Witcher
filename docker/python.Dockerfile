FROM witcher/basebuildrun
MAINTAINER tricke

COPY --chown=wc:wc pythonsrc37 /pythonsrc/

RUN cd /pythonsrc && bash ./configure && make -j50 && make install

COPY config/supervisord.conf /etc/supervisord.conf

RUN su - wc -c "source /usr/share/virtualenvwrapper/virtualenvwrapper.sh && mkvirtualenv -p `which python3` pywitch && sed 's/workon witcher/workon pywitch/g' -i /home/wc/.bashrc "

RUN su - wc -c "source /home/wc/.virtualenvs/pywitch/bin/activate && pip install termcolor flask "

RUN cp /bin/dash /bin/dash_backup

COPY --from=witcher/build-widash-x86 /Widash/archbuilds/dash /bin/dash

EXPOSE 80

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









