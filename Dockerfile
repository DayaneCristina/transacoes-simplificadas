FROM php:8.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libonig-dev  \
    librdkafka-dev  \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*\
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip pdo pdo_pgsql pgsql

RUN pecl install rdkafka \
    && docker-php-ext-enable rdkafka

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Configurações gerais do PHP para desenvolvimento
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/error-reporting.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/error-reporting.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/error-reporting.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/app

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

CMD ["php-fpm"]
