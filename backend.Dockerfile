#FROM docker-reg.portal.gov.bd/npf-backend:1.34
FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN sed -i 's/memory_limit = 128M/memory_limit = 2G/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/expose_php = On/expose_php = off/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/memory_limit = 128M/memory_limit = 2G/g' /usr/local/etc/php/php.ini-development
RUN sed -i 's/expose_php = On/expose_php = off/g' /usr/local/etc/php/php.ini-development
WORKDIR /var/www/app
RUN rm -rf *
COPY . .
RUN chown -R www-data:www-data /var/www/app
RUN chmod -R 755 /var/www/app

RUN apt update && \
    apt install -y software-properties-common && \
    add-apt-repository -y ppa:ondrej/php && \
    apt-get update && \
    apt-get install -y apache2 php7.3 php7.3-fpm && \
    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 80 443
CMD /usr/sbin/apache2ctl -D FOREGROUND
