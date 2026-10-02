FROM php:8.2-apache

# Install SQLite tools for PHP instead of MySQL
RUN apt-get update && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo_sqlite

COPY . /var/www/html/

EXPOSE 80

