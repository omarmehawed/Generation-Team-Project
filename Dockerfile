# استخدام نسخة PHP 8.3 
FROM php:8.3-cli

# تسطيب المكتبات الأساسية اللي لارافل بيحتاجها
RUN apt-get update -y && apt-get install -y unzip curl default-mysql-client

# تسطيب إضافة الداتابيز الخاصة بـ MySQL عشان نربط بـ Aiven
RUN docker-php-ext-install pdo pdo_mysql

# تحميل Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مسار العمل جوه السيرفر
WORKDIR /app

# نسخ كل ملفات المشروع
COPY . .

# تسطيب حزم لارافل
RUN composer install --no-dev --optimize-autoloader

# إعطاء صلاحيات للكتابة في مجلدات التخزين (مهم جداً للارافل)
RUN chmod -R 775 storage bootstrap/cache

# أمر التشغيل: عمل المايجريشن أوتوماتيك وبعدين تشغيل السيرفر
CMD php artisan migrate --force && php -S 0.0.0.0:$PORT -t public
