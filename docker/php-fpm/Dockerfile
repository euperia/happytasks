FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    curl \
    libssl-dev \
    zlib1g-dev \
    libicu-dev \
    libmcrypt-dev \
    unzip \
    libonig-dev \
    libtidy-dev \
    libzip-dev

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_mysql mbstring intl opcache tidy zip

# Install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN usermod -u 1000 www-data

WORKDIR /var/www/html

CMD ["php-fpm"]

EXPOSE 9000

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
