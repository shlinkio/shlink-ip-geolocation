FROM composer:2

RUN apk add --no-cache libpng-dev libpng libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev
RUN docker-php-ext-install gd
RUN apk add --update linux-headers && \
    apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS} && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apk del .phpize-deps
