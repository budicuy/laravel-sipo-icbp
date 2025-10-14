# Stage 1: Build environment and Composer dependencies
FROM php:8.2-fpm AS builder

# Install system dependencies and PHP extensions required for Laravel + MariaDB support
# Some dependencies are required for PHP extensions only in the build stage
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    opcache \
    intl \
    zip \
    bcmath \
    soap \
    gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set the working directory inside the container
WORKDIR /var/www

# Copy the entire Laravel application code into the container
COPY . /var/www

# Install Composer and dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# Development stage
FROM builder AS development

# Install development dependencies
RUN composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

# Production stage
FROM builder AS production

# Install runtime libraries needed for production
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfcgi-bin \
    procps \
    curl \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Download and install php-fpm health check script
RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

# Use the default production configuration for PHP runtime arguments
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Enable FPM status page
RUN sed -i '/\[www\]/a pm.status_path = /status' /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy the application code and dependencies from the build stage
COPY --from=builder /var/www /var/www

# Copy the initialization script
COPY docker/production/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set working directory
WORKDIR /var/www

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www

# Switch to the non-privileged user to run the application
USER www-data

# Expose port
EXPOSE 9000

# Set entrypoint and command
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
