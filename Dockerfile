FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
      git unzip libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_pgsql zip gd bcmath \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev \
 && chmod +x docker-entrypoint.sh \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 10000
CMD ["./docker-entrypoint.sh"]
