FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
libpng-dev libjpeg-dev libfreetype6-dev \
libsqlite3-dev zip unzip git curl \
&& docker-php-ext-configure gd --with-freetype --with-jpeg \
&& docker-php-ext-install gd pdo pdo_sqlite \
&& pecl install zip \
&& docker-php-ext-enable zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
