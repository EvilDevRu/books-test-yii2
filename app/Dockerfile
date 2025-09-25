FROM php:8.3-fpm

RUN groupadd --gid 1000 user \
  && useradd --uid 1000 --gid user --shell /bin/bash --create-home user

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    libzip-dev \
    zip \
    libmagickwand-dev \
    libssl-dev \
    libkrb5-dev \
    libicu-dev \
    libpq-dev \
    libkrb5-dev \
    pkg-config \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install \
    imagick \
    redis

RUN docker-php-ext-configure intl

RUN docker-php-ext-install -j$(nproc) \
    zip \
    pdo pdo_mysql pdo_pgsql \
    intl

RUN docker-php-ext-enable \
    imagick

COPY ./usr/local/etc/php/conf.d/app.ini /usr/local/etc/php/conf.d/app.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN mkdir /var/www/.composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/
USER user

