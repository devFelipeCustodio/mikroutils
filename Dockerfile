FROM php:8.2-fpm

COPY --from=php:8.2-fpm-alpine "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    && docker-php-ext-install sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/nginx.conf /etc/nginx/sites-enabled/default

COPY . /var/www/html

WORKDIR /var/www/html

RUN mkdir data && chmod 777 data

RUN composer install

EXPOSE 80

CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]