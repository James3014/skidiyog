FROM php:8.1-apache

# Install sqlite3 CLI tool
RUN apt-get update && apt-get install -y \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache
RUN a2enmod rewrite
RUN a2enmod auth_basic
RUN a2enmod authn_file
RUN a2enmod authz_user
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "date.timezone = Asia/Taipei" >> "$PHP_INI_DIR/php.ini"
RUN echo "memory_limit = 256M" >> "$PHP_INI_DIR/php.ini"
RUN echo "upload_max_filesize = 50M" >> "$PHP_INI_DIR/php.ini"
RUN echo "post_max_size = 50M" >> "$PHP_INI_DIR/php.ini"

# Set working directory
WORKDIR /var/www/html

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy application with correct ownership
COPY --chown=www-data:www-data . /var/www/html/

# Create writable directories
RUN mkdir -p /var/www/html/data /var/www/html/photos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 775 /var/www/html/data /var/www/html/photos

# Expose port
EXPOSE 80

# Use custom entrypoint to handle dynamic PORT
ENTRYPOINT ["docker-entrypoint.sh"]
