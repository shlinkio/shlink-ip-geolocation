FROM composer:2

RUN apk add --no-cache libpng-dev libpng libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev
RUN docker-php-ext-install gd

COPY --from=ghcr.io/php/pie:bin /pie /usr/bin/pie
RUN apk add --update linux-headers && \
    apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS} && \
    pie install xdebug/xdebug && \
    apk del .phpize-deps
