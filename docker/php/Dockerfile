FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    curl zip unzip git \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install \
       pdo_mysql mbstring xml zip


# Redis
RUN pecl install redis-6.1.0 && docker-php-ext-enable redis

# Composer
COPY --from=composer:2.9.1 /usr/bin/composer /usr/bin/composer

# Copy project FIRST
COPY . /var/www

# IMPORTANT: create storage folders
RUN mkdir -p storage/framework/cache \
    storage/framework/views \
    storage/framework/sessions \
    bootstrap/cache

# permissions
RUN chmod -R 775 storage bootstrap/cache

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY docker/scripts /usr/local/bin/scripts
RUN chmod +x /usr/local/bin/scripts/*.sh

#COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

CMD ["/usr/local/bin/scripts/app.sh"]
