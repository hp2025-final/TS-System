# TS-POS-V1 Project Plan
## Point of Sale System for Dress Retail Store

### Project Overview
A single-outlet POS system for a dress retail business with Laravel backend, Vue.js frontend, and MySQL database. Each dress piece has a unique barcode for inventory tracking.

---

## System Requirements

### Technology Stack
- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3.x with Composition API
- **Database**: MySQL 8.0+
- **Hosting**: Cloudways VPS
- **Authentication**: Laravel Sanctum
- **Styling**: Tailwind CSS (responsive design)
- **Build Tool**: Vite
- **Camera Integration**: Browser MediaDevices API for barcode scanning
- **Barcode Library**: ZXing-js or QuaggaJS for camera scanning

### Key Features
1. **User Management**
   - Simple staff authentication (register/login)
   - Single role system (staff only)
   - Session management

2. **Product Management**
   - Collection-based organization
   - Individual dress tracking with unique barcodes
   - Size variants per dress
   - Flexible 3-level discount system:
     * **Collection Level**: Discount entire collection (all dresses, all sizes)
     * **Dress Level**: Discount specific dress (all sizes of that dress)
     * **Size Level**: Discount specific size of specific dress
   - Manual barcode entry with duplicate validation

3. **Point of Sale**
   - Mobile camera barcode scanning
   - Cart functionality with discount calculations
   - Payment methods: Cash and Bank Transfer
   - Invoice generation with tax calculations
   - Profit tracking per sale

4. **Inventory Management**
   - Real-time stock tracking by size
   - Low stock alerts
   - Product search and filtering
   - Stock level monitoring

5. **Returns & Exchanges**
   - Return processing with reasons
   - Exchange functionality
   - Refund calculations
   - Return history tracking

6. **Reporting**
   - Daily sales reports
   - Profit analysis
   - Inventory reports
   - Returns and exchanges analytics

7. **Responsive Design**
   - Mobile-first approach
   - Tablet and desktop compatibility
   - Touch-friendly interface

---

## Database Schema

### Users Table
```sql
- id (Primary Key)
- name
- email (unique)
- password
- role (default: 'staff')
- created_at
- updated_at
```

### Collections Table
```sql
- id (Primary Key)
- name (e.g., "Ghazal")
- description
- discount_percentage (%) - collection level discount (optional)
- discount_active (boolean) - enable/disable collection discount
- status (active/inactive)
- created_at
- updated_at
```

### Dresses Table
```sql
- id (Primary Key)
- collection_id (Foreign Key)
- name (e.g., "Naz")
- sku (e.g., "gz12345")
- description
- cost_price (PKR)
- sale_price (PKR)
- discount_percentage (%) - dress level discount (optional)
- discount_active (boolean) - enable/disable dress discount
- tax_percentage (%)
- status (active/inactive)
- created_at
- updated_at
```

### Dress_Items Table (Individual pieces)
```sql
- id (Primary Key)
- dress_id (Foreign Key)
- barcode (unique, e.g., "2503071") - manual entry, duplicate validation
- size (XS, S, M, L, XL, XXL)
- size_discount_percentage (%) - size level discount (optional)
- size_discount_active (boolean) - enable/disable size discount
- status (available/sold/returned/damaged)
- sold_at
- returned_at
- created_at
- updated_at
```

### Sales Table
```sql
- id (Primary Key)
- user_id (Foreign Key - staff who made sale)
- invoice_number (unique)
- subtotal
- collection_discount_amount
- dress_discount_amount
- size_discount_amount
- total_discount_amount
- tax_amount
- total_amount
- payment_method (cash/bank_transfer)
- sale_date
- created_at
- updated_at
```

### Sale_Items Table
```sql
- id (Primary Key)
- sale_id (Foreign Key)
- dress_item_id (Foreign Key)
- dress_name
- collection_name
- sku
- size
- cost_price
- sale_price
- collection_discount_percentage
- dress_discount_percentage
- size_discount_percentage
- total_discount_amount
- tax_percentage
- tax_amount
- item_total
- profit_amount (sale_price - cost_price - discounts)
- created_at
- updated_at
```

### Returns Table
```sql
- id (Primary Key)
- sale_item_id (Foreign Key)
- dress_item_id (Foreign Key)
- user_id (Foreign Key - staff who processed return)
- return_reason
- return_type (return/exchange)
- refund_amount
- exchange_item_id (Foreign Key - if exchange)
- return_date
- notes
- created_at
- updated_at
```

---

## API Endpoints

### Authentication
- `POST /api/register` - Staff registration
- `POST /api/login` - Staff login
- `POST /api/logout` - Staff logout
- `GET /api/user` - Get authenticated user

### Collections
- `GET /api/collections` - List all collections
- `POST /api/collections` - Create collection
- `GET /api/collections/{id}` - Get collection details
- `PUT /api/collections/{id}` - Update collection
- `DELETE /api/collections/{id}` - Delete collection

### Dresses
- `GET /api/dresses` - List dresses with filters
- `POST /api/dresses` - Create dress
- `GET /api/dresses/{id}` - Get dress details
- `PUT /api/dresses/{id}` - Update dress
- `DELETE /api/dresses/{id}` - Delete dress

### Dress Items (Individual pieces)
- `GET /api/dress-items` - List dress items
- `POST /api/dress-items` - Create dress item
- `GET /api/dress-items/barcode/{barcode}` - Get item by barcode
- `PUT /api/dress-items/{id}` - Update dress item
- `DELETE /api/dress-items/{id}` - Delete dress item

### Sales
- `POST /api/sales` - Process sale (cash/bank_transfer)
- `GET /api/sales` - List sales with pagination
- `GET /api/sales/{id}` - Get sale details
- `GET /api/sales/{id}/invoice` - Generate invoice PDF

### Returns & Exchanges
- `POST /api/returns` - Process return or exchange
- `GET /api/returns` - List returns with pagination
- `GET /api/returns/{id}` - Get return details
- `PUT /api/returns/{id}` - Update return status

### Reports
- `GET /api/reports/daily-sales` - Daily sales report
- `GET /api/reports/profit` - Profit analysis report
- `GET /api/reports/inventory` - Inventory report
- `GET /api/reports/low-stock` - Low stock alert
- `GET /api/reports/returns` - Returns and exchanges report

### Barcode Validation
- `POST /api/validate-barcode` - Check barcode uniqueness
- `GET /api/dress-items/barcode/{barcode}` - Get item by barcode (camera scan)

### Discount Management
- `PUT /api/collections/{id}/discount` - Set/remove collection discount
- `PUT /api/dresses/{id}/discount` - Set/remove dress discount
- `PUT /api/dress-items/{id}/discount` - Set/remove size-specific discount
- `GET /api/discounts/active` - Get all active discounts

---

## Frontend Structure

### Vue.js Components

#### Authentication
- `LoginForm.vue` - Staff login
- `RegisterForm.vue` - Staff registration

#### Dashboard
- `Dashboard.vue` - Main dashboard with sales overview
- `StatsCard.vue` - Statistics display component

#### Product Management
- `CollectionList.vue` - Display collections
- `CollectionForm.vue` - Create/edit collections
- `DressList.vue` - Display dresses
- `DressForm.vue` - Create/edit dresses
- `DressItemForm.vue` - Add individual dress pieces

#### Point of Sale
- `POSInterface.vue` - Main POS screen (responsive)
- `CameraBarcodeScanner.vue` - Mobile camera barcode scanning
- `Cart.vue` - Shopping cart component
- `PaymentOptions.vue` - Cash/Bank transfer payment selection
- `InvoiceGenerator.vue` - Invoice creation and printing

#### Returns & Exchanges
- `ReturnExchangeForm.vue` - Process returns and exchanges
- `ReturnsList.vue` - Display returns history

#### Inventory
- `InventoryList.vue` - Inventory overview with stock levels
- `StockAlert.vue` - Low stock notifications
- `ProductSearch.vue` - Product search functionality
- `DiscountSettings.vue` - 3-level discount management

#### Reports
- `ProfitReport.vue` - Profit analysis dashboard
- `SalesReport.vue` - Sales analytics
- `ReturnsReport.vue` - Returns and exchanges analytics

---

## Development Phases

### Phase 1: Backend Setup (Week 1-2)
1. Laravel project initialization
2. Database migrations and models with 3-level discount system
3. Authentication system with Sanctum (staff role only)
4. Basic API endpoints for CRUD operations
5. Barcode validation system (no duplicates)
6. Database seeders with sample data

### Phase 2: Frontend Setup (Week 2-3)
1. Vue.js project setup with Vite (responsive design)
2. Authentication pages (login/register)
3. Dashboard layout and navigation
4. Basic product management interfaces
5. 3-level discount settings interface

### Phase 3: Core POS Functionality (Week 3-4)
1. POS interface development (responsive)
2. Mobile camera barcode scanning integration
3. Cart functionality with discount calculations
4. Payment options (cash/bank transfer)
5. Invoice generation
6. Sale processing with profit tracking

### Phase 4: Inventory & Returns Management (Week 4-5)
1. Inventory tracking system with stock levels
2. Stock alerts for low inventory
3. Product search and filtering
4. Returns and exchange functionality
5. Return/exchange processing interface

### Phase 5: Reports & Deployment (Week 5-6)
1. Profit analysis reports
2. Sales and returns reporting
3. Performance optimization
4. Responsive design testing (mobile/tablet/desktop)
5. Cloudways VPS setup and deployment

---

## Sample Data Structure

### Example Product Entry with Flexible Discounts
```json
{
  "collection": {
    "name": "Ghazal",
    "discount_percentage": 5,
    "discount_active": true
  },
  "dress": {
    "name": "Naz",
    "sku": "gz12345",
    "cost_price": 5000,
    "sale_price": 7000,
    "discount_percentage": 0,
    "discount_active": false,
    "tax_percentage": 18
  },
  "items": [
    {
      "barcode": "2503071",
      "size": "XS",
      "size_discount_percentage": 2,
      "size_discount_active": true,
      "status": "available"
    },
    {
      "barcode": "2503072", 
      "size": "S",
      "size_discount_percentage": 0,
      "size_discount_active": false,
      "status": "available"
    }
  ]
}
```

### Example: Different Discount Scenarios

**Scenario 1: Only Collection Discount Active**
```json
{
  "collection_discount": 5,
  "dress_discount": 0,
  "size_discount": 0,
  "total_discount_applied": 5
}
```

**Scenario 2: Collection + Size Discount Active**
```json
{
  "collection_discount": 5,
  "dress_discount": 0,
  "size_discount": 2,
  "total_discount_applied": 7
}
```

**Scenario 3: All Three Levels Active**
```json
{
  "collection_discount": 5,
  "dress_discount": 3,
  "size_discount": 2,
  "total_discount_applied": 10
}
```

### Example Sale Transaction with Active Discounts
```json
{
  "invoice_number": "INV-20250703-001",
  "payment_method": "cash",
  "items": [
    {
      "barcode": "2503071",
      "dress_name": "Naz",
      "collection": "Ghazal",
      "sku": "gz12345",
      "size": "XS",
      "sale_price": 7000,
      "applied_discounts": {
        "collection": {
          "percentage": 5,
          "amount": 350,
          "active": true
        },
        "dress": {
          "percentage": 0,
          "amount": 0,
          "active": false
        },
        "size": {
          "percentage": 2,
          "amount": 140,
          "active": true
        }
      },
      "total_discount": 490,
      "net_price": 6510,
      "tax_percentage": 18,
      "tax_amount": 1172,
      "total": 7682,
      "cost_price": 5000,
      "profit": 1510
    }
  ],
  "subtotal": 6510,
  "total_discount": 490,
  "total_tax": 1172,
  "grand_total": 7682,
  "total_profit": 1510
}
```

### Example Return/Exchange
```json
{
  "return_type": "exchange",
  "original_item": {
    "barcode": "2503071",
    "sale_item_id": 123
  },
  "exchange_item": {
    "barcode": "2503080",
    "size": "S"
  },
  "reason": "Size issue",
  "processed_by": "staff_user_id"
}
```

---

## Deployment Configuration

### Cloudways VPS Requirements
- **Server**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Memory**: 2GB RAM minimum
- **Storage**: 20GB SSD
- **Bandwidth**: Unlimited

### Environment Variables
```env
APP_NAME="TS POS System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ts_pos_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SANCTUM_STATEFUL_DOMAINS=your-domain.com
SESSION_DOMAIN=.your-domain.com
```

---

## Security Considerations

1. **Authentication**: Laravel Sanctum for simple API authentication
2. **Barcode Validation**: Prevent duplicate barcodes across system
3. **Basic Input Validation**: Form validation for all inputs
4. **HTTPS**: SSL certificate for secure data transmission
5. **Database Backup**: Daily automated database backups

## Key Features Summary

### ✅ **Included Features**
- **3-Level Discount System**: Collection, Dress, and Size level discounts
- **Mobile Camera Barcode Scanning**: Use phone camera to scan barcodes
- **Returns & Exchanges**: Full return and exchange processing
- **Profit Reporting**: Track profit margins and analysis
- **Responsive Design**: Works on mobile, tablet, and desktop
- **Inventory Management**: Real-time stock tracking
- **Payment Methods**: Cash and Bank Transfer
- **Barcode Validation**: No duplicate barcodes allowed

### ❌ **Excluded Features (For Now)**
- Barcode generation within app
- Offline functionality
- Partial payments
- Advanced security features
- Product images
- Keyboard shortcuts
- Multiple user roles

---

## Maintenance & Updates

1. **Regular Backups**: Daily database and file backups
2. **Security Updates**: Monthly Laravel and dependency updates
3. **Performance Monitoring**: Server and application monitoring
4. **Error Logging**: Comprehensive error logging and monitoring
5. **Data Analytics**: Sales and inventory analytics for business insights

---

## Next Steps

1. Confirm project requirements and scope
2. Set up development environment
3. Initialize Laravel backend with database schema
4. Create Vue.js frontend structure
5. Implement authentication system
6. Begin core POS functionality development

This project plan provides a comprehensive roadmap for developing your dress retail POS system. Each phase builds upon the previous one, ensuring a systematic and organized development process.

---

## 3-Level Discount System Explained

### How It Works
The system allows you to set discounts at three independent levels. Each level can be activated or deactivated separately:

#### **Level 1: Collection Discount**
- Applied to **entire collection** (all dresses, all sizes)
- Example: 5% off on entire "Ghazal" collection
- Affects every dress item in that collection

#### **Level 2: Dress Discount**  
- Applied to **specific dress** (all sizes of that dress)
- Example: 3% off on "Naz" dress (all sizes: XS, S, M, L, etc.)
- Affects only that specific dress, regardless of collection discount

#### **Level 3: Size Discount**
- Applied to **specific size of specific dress**
- Example: 2% off on "Naz" dress in "XS" size only
- Most specific level of discount

### Discount Calculation Priority
Discounts are **cumulative** when multiple levels are active:
```
Final Price = Original Price - (Collection% + Dress% + Size%)
```

### Real Examples

**Example 1: Collection-wide Sale**
```
- Collection "Ghazal": 10% discount (active)
- All dresses in Ghazal collection get 10% off
- Dress "Naz" XS: PKR 7000 → PKR 6300
- Dress "Laila" M: PKR 8000 → PKR 7200
```

**Example 2: Specific Dress Promotion**  
```
- Collection "Ghazal": No discount
- Dress "Naz": 15% discount (active) 
- Only "Naz" dress (all sizes) gets 15% off
- "Naz" XS: PKR 7000 → PKR 5950
- "Laila" M: PKR 8000 → PKR 8000 (no change)
```

**Example 3: Size-Specific Clearance**
```
- Collection "Ghazal": No discount
- Dress "Naz": No discount
- "Naz" in XS size only: 20% discount (active)
- "Naz" XS: PKR 7000 → PKR 5600
- "Naz" S: PKR 7000 → PKR 7000 (no change)
```

**Example 4: Combined Discounts**
```
- Collection "Ghazal": 5% discount (active)
- Dress "Naz": 3% discount (active)
- "Naz" XS size: 2% discount (active)
- Total discount: 5% + 3% + 2% = 10%
- "Naz" XS: PKR 7000 → PKR 6300
```

### Admin Control
- **Enable/Disable**: Each level can be turned on/off independently
- **Percentage Setting**: Set any percentage for each level
- **Real-time Updates**: Changes apply immediately to POS
- **Reporting**: Track which discounts are driving sales

---
