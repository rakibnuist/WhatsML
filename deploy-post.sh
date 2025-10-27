#!/bin/bash

# Railway deployment script for WhatsML
# This script runs after the application is deployed

echo "🚀 Running post-deployment setup..."

# Create missing directories
echo "📁 Creating missing directories..."
mkdir -p modules/*/resources/views
touch modules/*/resources/views/.gitkeep

# Run database migrations (skip if cache table doesn't exist)
echo "📊 Running database migrations..."
php artisan migrate --force || echo "⚠️ Some migrations failed, continuing..."

# Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Post-deployment setup completed!"
