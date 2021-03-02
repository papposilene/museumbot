FROM nginx:stable-alpine

ADD .dockerfile/nginx/nginx.conf /etc/nginx/nginx.conf
ADD .dockerfile/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/html

RUN addgroup -g 1000 museumbots && adduser -G museumbots -g museumbots -s /bin/sh -D museumbots

RUN chown museumbots:museumbots /var/www/html
