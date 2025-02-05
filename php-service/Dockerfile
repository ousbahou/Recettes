FROM php:8.0-apache
RUN apt-get update && apt-get install -y unzip libssl-dev && \
    pecl install mongodb && docker-php-ext-enable mongodb
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www/html/
WORKDIR /var/www/html
RUN composer install
RUN a2enmod rewrite
EXPOSE 80
