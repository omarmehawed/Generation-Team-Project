FROM php:8.2-fpm

# تثبيت الأدوات الأساسية للنظام
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# تثبيت إضافات PHP اللي بيحتاجها Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مكان المشروع
WORKDIR /var/www
COPY . .

# تثبيت مكتبات Laravel وتجهيز الـ Cache
RUN composer install --no-dev --optimize-autoloader

# ضبط صلاحيات الفولدرات
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# تشغيل المشروع
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080