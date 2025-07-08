#!/bin/bash

# Import Seed Data Script for TS POS System
# This script imports sample data into the database

echo "🌱 Importing seed data for TS POS System..."

# Check if MySQL is available
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL command not found. Please install MySQL client."
    exit 1
fi

# Get database credentials from .env file
if [ -f .env ]; then
    DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
    DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
    DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
    DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)
else
    echo "❌ .env file not found. Please ensure your environment is configured."
    exit 1
fi

echo "📋 Database: $DB_DATABASE"
echo "🏠 Host: $DB_HOST"
echo "👤 User: $DB_USERNAME"

# Import the complete seed data
echo "📦 Importing collections, dresses, and dress items..."

mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < database/sql/complete_seed_data.sql

if [ $? -eq 0 ]; then
    echo "✅ Seed data imported successfully!"
    echo ""
    echo "📊 Data Summary:"
    echo "   • 8 Collections"
    echo "   • 14 Dresses"
    echo "   • 48 Dress Items"
    echo ""
    echo "🎯 You can now test your POS system with sample data!"
else
    echo "❌ Failed to import seed data. Please check your database configuration."
    exit 1
fi
