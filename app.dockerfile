FROM php:5.6-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev zlib1g-dev \
    mysql-client libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install zip mcrypt pdo_mysql