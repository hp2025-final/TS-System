# TS-POS-V1 Quick Start Guide

## 🚀 Starting the System

### Option 1: One-Click Startup (Recommended)
Double-click `startup.bat` in the project root directory. This will:
- Install all dependencies
- Build frontend assets
- Set up the database
- Start both servers automatically

### Option 2: Manual Startup

#### Prerequisites
- PHP 8.1+ with required extensions
- Node.js 18+ with npm
- MySQL/MariaDB running
- Composer installed globally

#### Step-by-Step Commands
```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node.js dependencies  
npm install

# 3. Set up environment
cp .env.example .env
# Edit .env file with your database credentials

# 4. Generate application key
php artisan key:generate

# 5. Run database migrations
php artisan migrate

# 6. Seed database with sample data
php artisan db:seed

# 7. Build frontend assets
npm run build

# 8. Start Laravel server (in one terminal)
php artisan serve --port=8000

# 9. Start Vite dev server (in another terminal)
npm run dev
```

## 🌐 Access Points

| Service | URL | Purpose |
|---------|-----|---------|
| **Main Application** | http://localhost:8000 | Full POS system |
| **POS Interface** | http://localhost:8000/pos | Point of Sale |
| **API Documentation** | http://localhost:8000/api | REST API endpoints |
| **Vite Dev Server** | http://localhost:5173 | Frontend development |

## 👤 Demo Login

| Field | Value |
|-------|-------|
| **Email** | admin@tspos.com |
| **Password** | password |
| **Role** | Administrator |

## 🛠️ Available Scripts

| Script | Purpose |
|--------|---------|
| `startup.bat` | Complete system startup |
| `build-frontend.bat` | Build frontend assets only |
| `start-dev.bat` | Start development servers |
| `check-status.bat` | Check system status |

## 📱 Features Available

- ✅ **User Authentication** - Login/logout system
- ✅ **Product Management** - Collections, dresses, inventory
- ✅ **POS Interface** - Barcode scanning, cart management
- ✅ **Discount System** - 3-level discount calculation
- ✅ **Responsive Design** - Works on desktop and mobile
- ✅ **Real-time Updates** - Live inventory tracking

## 🗃️ Sample Data

The system comes pre-loaded with:
- **4 Collections** with various discount levels
- **20+ Dress designs** across different categories
- **100+ Individual items** with unique barcodes
- **Admin user** for immediate testing

## 🔧 Troubleshooting

### Common Issues

1. **Port already in use**
   ```bash
   # Use different ports
   php artisan serve --port=8001
   npm run dev -- --port=5174
   ```

2. **Database connection error**
   - Check MySQL is running
   - Verify .env database credentials
   - Run: `php artisan migrate:fresh --seed`

3. **Frontend assets not loading**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

4. **Permission errors**
   ```bash
   # Fix storage permissions
   chmod -R 775 storage bootstrap/cache
   ```

## 📋 Quick Test Checklist

After startup, verify these work:

- [ ] Login with demo credentials
- [ ] Navigate to POS interface
- [ ] Search for barcode: `2503071`
- [ ] Add item to cart
- [ ] Process checkout
- [ ] View collections and dresses

## 🆘 Support

If you encounter issues:

1. Check `storage/logs/laravel.log` for backend errors
2. Check browser console for frontend errors  
3. Verify all services are running with `check-status.bat`
4. Restart the system with `startup.bat`

---

**Ready to start? Just double-click `startup.bat` and you're good to go! 🎉**
