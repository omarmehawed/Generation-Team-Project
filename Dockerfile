# Base image PHP 8.2
FROM php:8.2-fpm

# تثبيت الحزم المطلوبة والـ PHP extensions
RUN apt-get update && apt-get install -y libicu-dev libxml2-dev zip unzip git \
    && docker-php-ext-install calendar gd mbstring pdo pdo_mysql xml curl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد مجلد العمل
WORKDIR /var/www/html

# نسخ المشروع
COPY . .

# تثبيت dependencies
RUN composer install --optimize-autoloader --no-dev

# الأمر الافتراضي لتشغيل PHP
CMD ["php-fpm"]
