FROM composer:1.10

RUN apk add --no-cache libpng-dev libpng libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev
RUN docker-php-ext-install gd
