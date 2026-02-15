# استخدم نسخة PHP مناسبة لـ Laravel
FROM php:8.2-fpm

# تثبيت الإضافات المطلوبة للـ System
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# تثبيت الـ PHP extensions
RUN docker-php-ext-install pdo_mysql gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع
WORKDIR /var/www
COPY . .

# تثبيت الـ Dependencies
RUN composer install --no-dev --optimize-autoloader

# تشغيل السيرفر
CMD php artisan serve --host=0.0.0.1 --port=$PORT