#!/bin/bash

set -e

# Function to check if database is ready
check_database() {
    echo "Checking database connection..."
    while ! php artisan db:show 2>/dev/null; do
        echo "Waiting for database to be ready..."
        sleep 2
    done
    echo "Database is ready!"
}

# Function to run migrations and seeders
run_migrations_and_seeders() {
    echo "Running database migrations..."
    php artisan migrate --force

    echo "Running database seeders..."
    php artisan db:seed --force

    echo "Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Setting correct permissions..."
    chown -R www-data:www-data /var/www/storage
    chown -R www-data:www-data /var/www/bootstrap/cache
}

# Check if this is the first run
if [ ! -f /var/www/storage/.docker-initialized ]; then
    echo "First run detected, initializing application..."

    # Wait for database to be ready
    check_database

    # Run migrations and seeders
    run_migrations_and_seeders

    # Create initialization marker
    touch /var/www/storage/.docker-initialized
    echo "Application initialized successfully!"
else
    echo "Application already initialized, starting PHP-FPM..."
fi

# Execute the original command
exec "$@"
