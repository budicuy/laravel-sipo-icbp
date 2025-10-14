#!/bin/bash

set -e

echo "🐳 Starting Laravel Docker Deployment with Podman"

# Check if Podman is installed
if ! command -v podman &> /dev/null; then
    echo "❌ Podman is not installed. Please install Podman first."
    exit 1
fi

# Check if .env.production exists
if [ ! -f .env.production ]; then
    echo "❌ .env.production file not found. Please create it first."
    exit 1
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=base64:" .env.production; then
    echo "🔑 Generating APP_KEY..."
    APP_KEY=$(podman run --rm -i php:8.2-cli php -r "echo base64_encode(random_bytes(32));")
    sed -i "s/APP_KEY=.*/APP_KEY=base64:$APP_KEY/" .env.production
    echo "✅ APP_KEY generated and saved to .env.production"
fi

# Build and start containers
echo "🔨 Building and starting containers..."
podman-compose -f compose.prod.yaml up --build -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 30

# Check if services are running
echo "🔍 Checking service status..."
podman-compose -f compose.prod.yaml ps

# Show logs
echo "📋 Showing recent logs..."
podman-compose -f compose.prod.yaml logs --tail=50

echo ""
echo "✅ Deployment completed successfully!"
echo ""
echo "🌐 Application URL: http://localhost:8080"
echo "🗄️  phpMyAdmin URL: http://localhost:8081"
echo ""
echo "🔧 Useful commands:"
echo "  View logs: podman-compose -f compose.prod.yaml logs -f"
echo "  Stop services: podman-compose -f compose.prod.yaml down"
echo "  Restart services: podman-compose -f compose.prod.yaml restart"
echo "  Run artisan command: podman-compose -f compose.prod.yaml exec php-cli php artisan <command>"
echo ""
echo "📁 Storage volume: laravel-storage-production"
echo "🗄️  Database volume: mariadb-data-production"
