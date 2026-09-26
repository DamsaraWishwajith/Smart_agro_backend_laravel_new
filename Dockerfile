FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev zip unzip nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build || true

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf
EXPOSE 9000
CMD ["php-fpm"]
