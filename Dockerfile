FROM php:8.0-fpm

#RUN pecl install xdebug
#COPY config/90-xdebug.ini "${PHP_INI_DIR}/conf.d"

RUN apt-get update && apt-get install -qqy \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libaio1 \
        libicu-dev \
        curl \
        libmcrypt-dev \
        libonig-dev \
        libzip-dev \
        libxml2-dev \
        libldb-dev \
        wget \
        && apt-get clean autoclean \
        && apt-get autoremove --yes \
        &&  rm -rf /var/lib/{apt,dpkg,cache,log}/
#composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring zip xml gd mysqli pdo_mysql