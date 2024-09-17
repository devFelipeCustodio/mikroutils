FROM composer:lts as prod-deps
WORKDIR /app
RUN --mount=type=bind,source=./composer.json,target=composer.json \
    --mount=type=bind,source=./composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction --ignore-platform-req=ext-sockets

FROM composer:lts as dev-deps
WORKDIR /app
RUN --mount=type=bind,source=./composer.json,target=composer.json \
    --mount=type=bind,source=./composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-interaction --ignore-platform-req=ext-sockets

FROM php:8.2-fpm as base
RUN docker-php-ext-install sockets
COPY ./src /var/www/html/src
RUN chown -R www-data /var/www/html

FROM base as development
RUN pecl install xdebug-3.2.1 \
	&& docker-php-ext-enable xdebug
COPY ./docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./tests /var/www/html/tests
COPY ./.env /var/www/html/
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY --from=dev-deps app/vendor/ /var/www/html/vendor

FROM base as final
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=prod-deps app/vendor/ /var/www/html/vendor
USER www-data
