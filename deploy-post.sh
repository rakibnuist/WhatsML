#!/bin/bash

# Railway deployment script for WhatsML
# This script runs after the application is deployed

echo "🚀 Running post-deployment setup..."

# Create missing directories
echo "📁 Creating missing directories..."
mkdir -p modules/*/resources/views
touch modules/*/resources/views/.gitkeep

# Run database migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Post-deployment setup completed!"
