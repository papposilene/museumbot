FROM composer:latest

RUN addgroup -g 1000 museumbots && adduser -G museumbots -g museumbots -s /bin/sh -D museumbots

WORKDIR /var/www/html
