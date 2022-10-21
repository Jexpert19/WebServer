FROM php:8.0-apache as base

# Prepare serverfiles
COPY ./html /var/www/html
RUN chmod -R 755 /var/www/html

RUN docker-php-ext-install mysqli