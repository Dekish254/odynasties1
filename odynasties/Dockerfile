FROM php:8.2-apache

# Install SQLite extensions safely
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite

# Copy files
COPY . /var/www/html/

# Expose port
EXPOSE 80

# Overwrite Apache settings to allow public folder reads dynamically
CMD ["apache2-foreground"]
RUN chown -R www-data:www-data /var/www/html
