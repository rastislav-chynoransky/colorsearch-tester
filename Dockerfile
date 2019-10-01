FROM php:7.3


RUN apt-get update -y && apt-get install -y libzip-dev
#    libpng-dev \
#    libjpeg-dev \
#    libzip-dev \
#    git

RUN docker-php-ext-install zip

WORKDIR /var/www

COPY --from=composer:1.8 /usr/bin/composer /usr/bin/composer
COPY composer.json .
RUN composer install --no-plugins --no-scripts --no-interaction

ENTRYPOINT php -S 0.0.0.0:80 -t /var/www/public
