FROM puppeteer1337/php7

ADD abantecart-src-master/public_html/ /app/

RUN chmod -R 0755  /app/image/
RUN chmod -R 0755  /app/system/cache/
RUN chmod -R 0755  /app/system/logs/
RUN chmod -R 0755  /app/download/
RUN chmod -R 0755  /app/extensions/
RUN chmod -R 0755  /app/resources/
RUN chmod 0755 /app/system/config.php

RUN chown www-data -R /app/

RUN sed -i 's/opcache.enable=1/opcache.enable=0/' /usr/local/lib/php.ini
RUN echo 'opcache.enable=0' >> /app/php.ini


COPY entrypoint.sh /entrypoint.sh

RUN chmod 0755  /entrypoint.sh

ENTRYPOINT /entrypoint.sh