# Base image PHP 8.2 FPM
FROM php:8.2-fpm

# تثبيت الأدوات المطلوبة والـ PHP extensions
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

# تثبيت Node dependencies لو المشروع محتاج npm build
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# إعطاء صلاحيات على storage و bootstrap/cache
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# الأمر الافتراضي لتشغيل PHP
CMD ["php-fpm"]
