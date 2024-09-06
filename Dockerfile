FROM shinsenter/php:8.3-fpm-nginx

RUN phpaddmod sockets 

ADD --chown=$APP_USER:$APP_GROUP . /var/www/html/

ENV DOCUMENT_ROOT=/public