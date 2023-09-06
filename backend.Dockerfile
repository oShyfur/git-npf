FROM docker-reg.portal.gov.bd/npf-backend:1.34
RUN sed -i 's/memory_limit = 128M/memory_limit = 2G/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/expose_php = On/expose_php = off/g' /usr/local/etc/php/php.ini-production
RUN sed -i 's/memory_limit = 128M/memory_limit = 2G/g' /usr/local/etc/php/php.ini-development
RUN sed -i 's/expose_php = On/expose_php = off/g' /usr/local/etc/php/php.ini-development
WORKDIR /var/www/app
RUN rm -rf *
COPY . .
RUN chown -R www-data:www-data /var/www/app
RUN chmod -R 755 /var/www/app

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 80 443
CMD /usr/sbin/apache2ctl -D FOREGROUND
