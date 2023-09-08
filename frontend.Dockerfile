#FROM docker-reg.portal.gov.bd/npf-frontend:v8
FROM ubuntu:latest

WORKDIR /var/www/app
RUN rm -rf *
COPY . .
RUN chown -R www-data:www-data /var/www/app
RUN chmod -R 755 /var/www/app

RUN apt update
RUN apt install -y software-properties-common
RUN add-apt-repository -y ppa:ondrej/php
RUN apt-get install -y php7.3
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 80 443
CMD service php7.3-fpm start && /usr/sbin/apache2ctl -D FOREGROUND
