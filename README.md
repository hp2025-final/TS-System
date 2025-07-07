# TS-POS-V1 - Point of Sale System

A comprehensive Point of Sale (POS) system built specifically for dress retail stores, featuring advanced discount management and barcode tracking.

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Node.js 18+
- MySQL/MariaDB
- Composer

### Installation

1. **Clone & Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

3. **Build Frontend**
   ```bash
   # For production
   npm run build
   
   # For development with hot reload
   npm run dev
   ```

4. **Access the Application**
   - Navigate to your local server
   - Login with: `admin@tspos.com` / `password`

### ⚡ Windows Quick Setup
Double-click `build-frontend.bat` for automatic setup, or `start-dev.bat` for development mode.

## 🎯 Features

### ✅ Implemented
- **3-Level Discount System** (Collection → Dress → Item)
- **Barcode Management** with automatic generation
- **User Authentication** with role-based access
- **Inventory Management** with size/color variants
- **RESTful API** with 15+ endpoints
- **Vue.js SPA** with modern UI
- **Sample Data** (100+ dress items across 4 collections)

### 🚧 In Progress
- Advanced POS interface
- Payment processing
- Receipt generation
- Reporting system

## 🏗️ Technical Stack

- **Backend:** Laravel 11, MySQL, Laravel Sanctum
- **Frontend:** Vue.js 3, Tailwind CSS, Pinia
- **Build Tools:** Vite, NPM

## 📊 Database Schema

### Core Tables
- `collections` - Dress collections with discount rules
- `dresses` - Individual dress designs with pricing
- `dress_items` - Inventory items with barcodes
- `sales` & `sale_items` - Transaction tracking
- `users` - Authentication & roles

### Discount Logic
The system applies discounts hierarchically:
1. **Item Level** (highest priority)
2. **Dress Level** (if no item discount)
3. **Collection Level** (fallback)

## 🔌 API Endpoints

### Authentication
```
POST /api/auth/login    - User login
POST /api/auth/register - User registration
GET  /api/auth/user     - Get current user
POST /api/auth/logout   - Logout
```

### Collections
```
GET    /api/collections     - List all collections
POST   /api/collections     - Create collection
GET    /api/collections/{id} - Get collection details
PUT    /api/collections/{id} - Update collection
DELETE /api/collections/{id} - Delete collection
```

### Dresses
```
GET /api/dresses                        - List dresses (with filters)
GET /api/dresses/{id}/calculate-discount - Calculate final price
```

### Dress Items (Inventory)
```
GET  /api/dress-items                      - List items (with filters)
GET  /api/dress-items/barcode/{barcode}    - Find by barcode
POST /api/dress-items/{id}/generate-barcode - Generate new barcode
```

## 🧪 Testing the API

### Login to get authentication token:
```bash
curl -X POST http://your-domain/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@tspos.com","password":"password"}'
```

### Test barcode lookup:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://your-domain/api/dress-items/barcode/2503071
```

## 📋 Sample Data

The system comes with pre-loaded sample data:

### Collections (4 total)
- Summer Collection 2024 (10% discount)
- Winter Collection 2024 (15% discount)
- Formal Wear (5% discount)
- Casual Wear (8% discount)

### Dress Items (100+ total)
- Various sizes: XS, S, M, L, XL, XXL
- Multiple colors per design
- Price range: $50 - $300
- Unique barcodes for each item

### Demo Users
- **Admin:** admin@tspos.com / password
- **Staff:** staff@tspos.com / password

## 🔧 Development

### File Structure
```
app/
├── Http/Controllers/Api/  # API controllers
├── Models/               # Eloquent models
database/
├── migrations/          # Database schema
├── seeders/            # Sample data
resources/
├── js/                 # Vue.js components
├── css/               # Tailwind styles
├── views/             # Blade templates
routes/
├── api.php            # API routes
├── web.php            # Web routes
```

### Adding New Features

1. **New API Endpoint:**
   - Add route in `routes/api.php`
   - Create controller method
   - Test with API client

2. **New Vue Component:**
   - Create in `resources/js/components/`
   - Add to router if needed
   - Import in parent component

### Build Commands
```bash
npm run dev          # Development with hot reload
npm run build        # Production build
npm run watch        # Watch mode
```

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `npm run build`
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up SSL certificate
- [ ] Configure database backup

### Environment Variables
```env
APP_NAME="TS-POS-V1"
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ts_pos_v1
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📚 Documentation

- `IMPLEMENTATION_DOCUMENTATION.md` - Detailed technical documentation
- `PROJECT_PLAN.md` - Original project specifications
- API documentation available at `/api/documentation` (when built)

## 🐛 Troubleshooting

### Vite Manifest Error
If you see "Vite manifest not found":
1. Run `npm install`
2. Run `npm run build`
3. Refresh the page

### Database Issues
```bash
php artisan migrate:fresh --seed  # Reset database with sample data
```

### Permission Issues (Linux/Mac)
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

For technical support or feature requests, please create an issue in the repository or contact the development team.

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
