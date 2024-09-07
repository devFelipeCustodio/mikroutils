FROM php:8.2-fpm

COPY --from=php:8.2-fpm-alpine "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install sockets

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 

RUN apt update && apt install git -y

COPY composer* /var/www/html

RUN /usr/bin/composer install
