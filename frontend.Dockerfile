FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

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
CMD service php7.3-fpm start && /usr/sbin/apache2ctl -D FOREGROUND


