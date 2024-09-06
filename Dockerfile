FROM shinsenter/php:8.3-fpm-nginx

RUN phpaddmod sockets 

ENV DOCUMENT_ROOT=/public