# Use official PHP with Apache
FROM php:8.2-apache

# Install MySQL extension for PHP
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all project files to the container
COPY . /var/www/html/

# Set proper permissions (VULN: World-writable for vulnerability)
RUN chmod -R 777 /var/www/html/uploads/ 2>/dev/null || true
RUN chmod -R 777 /var/www/html/logs/ 2>/dev/null || true

# Expose port 80
EXPOSE 80