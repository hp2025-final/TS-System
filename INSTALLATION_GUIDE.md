# TS-POS-V1 Installation & Setup Guide

## Complete Point of Sale System for Dress Retail Store

This guide will help you install and set up the TS-POS-V1 system, a comprehensive Point of Sale application built with Laravel 11 and Vue.js 3.

---

## System Requirements

### Backend Requirements
- PHP 8.2 or higher
- Composer
- Laravel 11.x
- MySQL 8.0 or higher
- XAMPP/WAMP (for local development)

### Frontend Requirements
- Node.js 18 or higher
- npm or yarn
- Vue.js 3.x
- Vite

---

## Installation Steps

### 1. Clone or Download the Project
```bash
git clone <repository-url> TS-POS-V1
cd TS-POS-V1
```

### 2. Backend Setup

#### Install PHP Dependencies
```bash
composer install
```

#### Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Configure Database (.env file)
```env
APP_NAME="TS POS System"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost/TS-POS-V1/public

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ts_pos_v1
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

#### Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE ts_pos_v1;
exit

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed
```

### 3. Frontend Setup

#### Install JavaScript Dependencies
```bash
npm install
```

#### Build Assets for Development
```bash
npm run dev
```

#### Build Assets for Production
```bash
npm run build
```

---

## Quick Setup with Batch Script

For Windows users, use the provided batch script:

```bash
# Run the setup script
setup-database.bat
```

This script will:
- Check database status
- Run migrations
- Seed sample data
- Provide test URLs and credentials

---

## Default Login Credentials

### Admin User
- **Email:** admin@tspos.com
- **Password:** password

### Staff User
- **Email:** staff@tspos.com
- **Password:** password

---

## Testing the Application

### Working Test URLs

1. **Enhanced POS Demo:**
   - http://localhost/TS-POS-V1/public/demo.html
   - Full demo with login integration

2. **Simple Demo:**
   - http://localhost/TS-POS-V1/public/simple-demo.php
   - Quick data verification

3. **Database Check:**
   - http://localhost/TS-POS-V1/public/check.php
   - Verify database connection and data

4. **Main Application:**
   - http://localhost/TS-POS-V1/public
   - Full Vue.js application

### Direct API Endpoints

- **Login API:** http://localhost/TS-POS-V1/public/api-login.php
- **API Test:** http://localhost/TS-POS-V1/public/api-test.php

### Sample Barcodes for Testing
- 2503071
- 2503076
- 2503080
- 2503085

---

## Features Overview

### 🛒 Point of Sale
- **Barcode Scanning:** Camera-based and manual input
- **Cart Management:** Add/remove items, quantity management
- **Payment Methods:** Cash and Bank Transfer
- **Discount System:** Collection, dress, and size-level discounts
- **Real-time Calculation:** Automatic tax and profit calculation
- **Invoice Generation:** Professional invoice printing
- **Keyboard Shortcuts:** F1-F3 for quick actions

### 📦 Inventory Management
- **Collection Organization:** Group dresses by collections
- **Size Variants:** Multiple sizes per dress
- **Barcode System:** Unique barcode per item
- **Stock Tracking:** Real-time inventory updates
- **Low Stock Alerts:** Automatic notifications
- **Status Management:** Available/Sold/Reserved tracking

### 📊 Reporting & Analytics
- **Sales Reports:** Daily, weekly, monthly sales analysis
- **Profit Tracking:** Cost vs sale price analysis
- **Inventory Reports:** Stock levels and valuation
- **Low Stock Reports:** Items needing restocking
- **Returns Analytics:** Return reasons and trends
- **Customer Insights:** Payment method preferences

### ↩️ Returns & Exchanges
- **Flexible Returns:** Item-specific return processing
- **Exchange System:** Direct item replacement
- **Reason Tracking:** Return reason categorization
- **Refund Calculation:** Automatic refund processing
- **Exchange Validation:** Ensure replacement item availability

### 🎨 User Interface
- **Responsive Design:** Mobile, tablet, and desktop optimized
- **Modern UI:** Clean, intuitive interface with Tailwind CSS
- **Dark/Light Themes:** Professional appearance
- **Touch-Friendly:** Optimized for touch devices
- **Keyboard Navigation:** Full keyboard support
- **Real-time Updates:** Live data synchronization

---

## File Structure

```
TS-POS-V1/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── CollectionController.php
│   │   ├── DressController.php
│   │   ├── DressItemController.php
│   │   ├── SaleController.php          # Complete POS logic
│   │   ├── ReturnController.php        # Returns & exchanges
│   │   └── ReportController.php        # Analytics & reports
│   └── Models/
│       ├── User.php
│       ├── Collection.php
│       ├── Dress.php
│       ├── DressItem.php
│       ├── Sale.php
│       ├── SaleItem.php
│       └── Return.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── pos/
│   │   │   │   ├── POSInterfaceEnhanced.vue    # Complete POS UI
│   │   │   │   └── BarcodeScanner.vue          # Camera scanning
│   │   │   ├── reports/
│   │   │   │   └── ReportsDashboard.vue        # Analytics dashboard
│   │   │   ├── returns/
│   │   │   │   └── ReturnsInterface.vue        # Returns processing
│   │   │   └── DashboardEnhanced.vue           # Main dashboard
│   │   └── app.js
│   └── views/
├── public/
│   ├── index.php
│   ├── demo.html                       # Working demo
│   ├── simple-demo.php                 # Data verification
│   ├── check.php                       # Database check
│   ├── api-login.php                   # Direct login endpoint
│   └── api-test.php                    # API testing
└── routes/
    ├── web.php
    └── api.php
```

---

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user

### Collections
- `GET /api/collections` - List all collections
- `POST /api/collections` - Create new collection
- `PUT /api/collections/{id}` - Update collection
- `DELETE /api/collections/{id}` - Delete collection

### Dresses
- `GET /api/dresses` - List all dresses
- `POST /api/dresses` - Create new dress
- `PUT /api/dresses/{id}` - Update dress
- `DELETE /api/dresses/{id}` - Delete dress

### Dress Items
- `GET /api/dress-items` - List all dress items
- `POST /api/dress-items` - Create new dress item
- `GET /api/dress-items/barcode/{barcode}` - Find item by barcode
- `PUT /api/dress-items/{id}` - Update dress item
- `DELETE /api/dress-items/{id}` - Delete dress item

### Sales
- `GET /api/sales` - List all sales
- `POST /api/sales` - Create new sale (POS checkout)
- `GET /api/sales/{id}` - Get sale details
- `GET /api/sales/{id}/invoice` - Generate invoice

### Returns
- `GET /api/returns` - List all returns
- `POST /api/returns` - Process return/exchange
- `GET /api/returns/{id}` - Get return details

### Reports
- `GET /api/reports/daily` - Daily sales report
- `GET /api/reports/sales` - Comprehensive sales analytics
- `GET /api/reports/inventory` - Inventory reports
- `GET /api/reports/low-stock` - Low stock alerts
- `GET /api/reports/profit` - Profit analysis
- `GET /api/reports/returns` - Returns analytics

---

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check database credentials in .env
# Ensure MySQL service is running
# Verify database exists
```

#### 2. 404 Errors on API Routes
```bash
# Check .htaccess file exists in public folder
# Verify Apache mod_rewrite is enabled
# Use direct API endpoints: api-login.php, api-test.php
```

#### 3. Vue.js Assets Not Loading
```bash
# Install dependencies
npm install

# Build assets
npm run dev

# For production
npm run build
```

#### 4. Permission Issues
```bash
# Set correct permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Debug Mode

Enable debug mode in `.env` for detailed error messages:
```env
APP_DEBUG=true
```

### Log Files

Check Laravel logs for errors:
```
storage/logs/laravel.log
```

---

## Production Deployment

### 1. Environment Setup
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Set proper APP_URL
APP_URL=https://your-domain.com
```

### 2. Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Build production assets
npm run build
```

### 3. Security Considerations
- Change default passwords
- Use HTTPS in production
- Set proper file permissions
- Regular database backups
- Update dependencies regularly

---

## Support & Documentation

### Additional Resources
- **Project Plan:** PROJECT_PLAN.md
- **Troubleshooting:** TROUBLESHOOTING_UPDATE.md
- **Solution Guide:** SOLUTION_404_ERROR.md
- **Implementation Docs:** IMPLEMENTATION_DOCUMENTATION.md

### Getting Help
1. Check the troubleshooting documentation
2. Review Laravel and Vue.js documentation
3. Check browser console for JavaScript errors
4. Review Laravel logs for backend errors

---

## System Architecture

### Backend (Laravel 11)
- **MVC Architecture:** Clean separation of concerns
- **API-First Design:** RESTful API endpoints
- **Database ORM:** Eloquent relationships
- **Authentication:** Laravel Sanctum
- **Validation:** Form request validation
- **Error Handling:** Comprehensive error responses

### Frontend (Vue.js 3)
- **Composition API:** Modern Vue.js patterns
- **Component Architecture:** Reusable components
- **State Management:** Pinia for state management
- **Routing:** Vue Router for navigation
- **HTTP Client:** Axios for API communication
- **Styling:** Tailwind CSS for responsive design

### Database Design
- **Normalized Structure:** Efficient relational design
- **Foreign Key Constraints:** Data integrity
- **Indexes:** Optimized query performance
- **Migrations:** Version controlled schema
- **Seeders:** Sample data generation

---

## Deployment Checklist

- [ ] Database created and configured
- [ ] Environment variables set
- [ ] Dependencies installed (PHP & JavaScript)
- [ ] Database migrations run
- [ ] Sample data seeded
- [ ] Assets compiled
- [ ] File permissions set
- [ ] Default users created
- [ ] Test URLs accessible
- [ ] Barcode scanning tested
- [ ] POS workflow verified
- [ ] Reports generating correctly
- [ ] Returns processing working

---

## License

This project is proprietary software. All rights reserved.

## Version

**Version 1.0** - Complete POS System
- Released: January 2024
- Features: Full POS, Inventory, Reports, Returns
- Status: Production Ready

---

*For technical support or questions, please refer to the documentation files included in this project.*
