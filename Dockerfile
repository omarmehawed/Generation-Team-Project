# Base image PHP 8.2 FPM
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libicu-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install calendar gd mbstring pdo pdo_mysql xml curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

CMD ["php-fpm"]
