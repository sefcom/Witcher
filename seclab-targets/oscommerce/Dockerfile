FROM puppeteer1337/php7

ADD oscommerce2/ /app/

RUN chmod -R 0777 /app/

COPY entrypoint.sh /entrypoint.sh

RUN chmod 0755  /entrypoint.sh

ENTRYPOINT /entrypoint.sh