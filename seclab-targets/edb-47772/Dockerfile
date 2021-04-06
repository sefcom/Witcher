FROM puppeteer1337/php7

ADD ona-18.1.1/ /opt/ona-18.1.1/
RUN chown www-data /opt/ona-18.1.1/ -R

ADD apache2/ /etc/apache2/

COPY exploit.sh /

ENTRYPOINT ["supervisord"]
