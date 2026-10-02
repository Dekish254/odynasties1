FROM php:8.2-apache

# Install SQLite tools for PHP
RUN apt-get update && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite

# Copy your website files into the container
COPY . /var/www/html/

# FIX: Grant the web server full ownership & write permissions to the folder
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

EXPOSE 80
