#!/bin/bash

# Railway deployment script for WhatsML
# This script runs after the application is deployed

echo "🚀 Running post-deployment setup..."

# Set error handling
set -e

# Create missing directories
echo "📁 Creating missing directories..."
mkdir -p modules/*/resources/views
touch modules/*/resources/views/.gitkeep

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Run database migrations (skip if cache table doesn't exist)
echo "📊 Running database migrations..."
php artisan migrate --force || echo "⚠️ Some migrations failed, continuing..."

# Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link already exists or failed"

# Clear and cache configurations
echo "⚡ Optimizing application..."
php artisan config:cache || echo "⚠️ Config cache failed"
php artisan route:cache || echo "⚠️ Route cache failed"
php artisan view:cache || echo "⚠️ View cache failed"

echo "✅ Post-deployment setup completed!"
