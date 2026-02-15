# 1. بنحدد النسخة اللي هنبني عليها (PHP 8.2)
FROM php:8.2-fpm

# 2. تحديث وتثبيت الأدوات اللازمة لنظام التشغيل
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev

# 3. تثبيت إضافات PHP اللي بيحتاجها Laravel (زي الـ MySQL والـ GD)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. تثبيت Composer جوه الـ Container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. تحديد فولدر الشغل جوه السيرفر
WORKDIR /var/www

# 6. نسخ ملفات المشروع من جهازك للسيرفر
COPY . .

# 7. تثبيت مكتبات Laravel (Packages)
RUN composer install --no-dev --optimize-autoloader

# 8. ضبط الصلاحيات لفولدرات التخزين
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 9. الأمر اللي بيشغل المشروع على بورت Railway
CMD php artisan serve --host=0.0.0.0 --port=$PORT