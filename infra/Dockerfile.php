FROM composer:latest as composer

FROM php:8.0-fpm-alpine as php_install
RUN apk update && apk add mysql-client icu make icu-dev g++
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install intl mysqli pdo_mysql && \
    docker-php-ext-enable intl mysqli pdo_mysql && \
    php -m && \
    pecl list-packages

FROM php_install as php_setup
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN sed -e 's/max_execution_time = 30/max_execution_time = 90/' -i "$PHP_INI_DIR/php.ini" && \
    sed -e 's/memory_limit = 128M/memory_limit = 136M/' -i "$PHP_INI_DIR/php.ini" && \
    sed -e 's/post_max_size = 8M/post_max_size = 16M/' -i "$PHP_INI_DIR/php.ini" && \
    sed -e 's/upload_max_filesize = 2M/upload_max_filesize = 22M/' -i "$PHP_INI_DIR/php.ini"  && \
    sed -i 's/9000/9001/' /usr/local/etc/php-fpm.d/*

RUN cat /usr/local/etc/php/php.ini | grep ^[^\;] && \
    cat /usr/local/etc/php/php.ini | grep error

WORKDIR /app

COPY . .

RUN composer install && \
    chown -R www-data:www-data /app

COPY infra/docker-php-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
EXPOSE 9001
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
