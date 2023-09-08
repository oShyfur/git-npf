FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

# Installing packages
RUN apt update && \
    apt install -y software-properties-common && \
    add-apt-repository -y ppa:ondrej/php && \
    apt-get update && \
    apt-get install -y apache2 php7.3 php7.3-fpm

# Modify PHP configurations
RUN sed -i 's/memory_limit = 128M/memory_limit = 2G/g' /etc/php/7.3/apache2/php.ini
RUN sed -i 's/expose_php = On/expose_php = off/g' /etc/php/7.3/apache2/php.ini

WORKDIR /var/www/app
RUN rm -rf *
COPY . .
RUN chown -R www-data:www-data /var/www/app
RUN chmod -R 755 /var/www/app
		
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 80 443
CMD /usr/sbin/apache2ctl -D FOREGROUND

