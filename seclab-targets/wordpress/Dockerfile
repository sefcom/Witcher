FROM puppeteer1337/php7

ADD wordpress /app/
COPY wp-cli.phar /app/
COPY wordpress-importer.0.7.zip /app/

RUN chmod 0755 /app/

COPY entrypoint.sh /entrypoint.sh

RUN chmod 0755  /entrypoint.sh

ENTRYPOINT /entrypoint.sh