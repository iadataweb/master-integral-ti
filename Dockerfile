FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    poppler-utils \
    zip unzip \
 && docker-php-ext-install mysqli pdo pdo_mysql gd

RUN a2enmod rewrite

COPY ./app /var/www/html
