# FROM ubuntu:18.04
FROM puppeteer1337/php7

ENV DEBIAN_FRONTEND="noninteractive"
ENV TZ="Etc/UTC"
RUN apt-get update && apt-get install -y sudo php-mysqli

# Install OpenEMR
COPY openemr-php7_5.0.1-2_all.deb /
RUN dpkg -i /openemr-php7_5.0.1-2_all.deb || true
RUN apt-get install -y -f

# Forget about the PHP installed with apt-get
RUN a2dismod php7.2

# (Auto) Configure OpenEMR
WORKDIR /var/www/openemr
COPY etc/scripts/* /var/www/openemr/
COPY etc/scripts/utilities/* /root/

RUN chmod 600 /var/www/openemr
RUN chmod 500 autoconfig.sh /root/unlock_admin.sh
COPY --chown=www-data:www-data etc/globals.php /var/www/openemr/interface/globals.php

# RUN bash run_setup.sh

RUN chmod 0755 /var/www/openemr/run_setup.sh
ENTRYPOINT /var/www/openemr/run_setup.sh
