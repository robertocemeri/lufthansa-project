FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpng-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# RUN cp .env.example .env

RUN composer install --no-dev --optimize-autoloader

# CMD ["sh", "-c", "php artisan key:generate && sleep 15 && php artisan migrate && php artisan serve --host=0.0.0.0 --port=8000 "]
# CMD ["sh", "-c", "php artisan key:generate && sleep 15 && php artisan migrate && php artisan serve --host=0.0.0.0 --port=8000 & php artisan schedule:work"]

