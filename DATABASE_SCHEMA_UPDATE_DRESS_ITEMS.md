# Database Schema Update - Dress Items Table

## New Columns Added ✅

Added two new columns to the `dress_items` table:

### 1. HS Code
- **Column Name**: `hs_code`
- **Type**: `varchar(255)`
- **Nullable**: Yes
- **Position**: After `barcode` column
- **Purpose**: Store Harmonized System classification code for the dress item

### 2. FBR Invoice No
- **Column Name**: `fbr_invoice_no`
- **Type**: `varchar(255)`
- **Nullable**: Yes
- **Position**: After `hs_code` column
- **Purpose**: Store Federal Board of Revenue invoice number for the dress item

## Files Modified

### 1. Migration File
**File**: `database/migrations/2025_07_09_165923_add_hs_code_and_fbr_invoice_no_to_dress_items_table.php`

```php
Schema::table('dress_items', function (Blueprint $table) {
    $table->string('hs_code')->nullable()->after('barcode');
    $table->string('fbr_invoice_no')->nullable()->after('hs_code');
});
```

### 2. DressItem Model
**File**: `app/Models/DressItem.php`

Updated the `$fillable` array to include the new fields:
```php
protected $fillable = [
    'dress_id',
    'barcode',
    'hs_code',           // ✅ Added
    'fbr_invoice_no',    // ✅ Added
    'size',
    'size_discount_percentage',
    'size_discount_active',
    'status',
    'sold_at',
    'returned_at'
];
```

## Current Table Structure

The `dress_items` table now has the following columns:
1. `id` (Primary Key)
2. `dress_id` (Foreign Key)
3. `barcode` (Unique)
4. `hs_code` (New - Nullable)
5. `fbr_invoice_no` (New - Nullable)
6. `size` (Enum: XS, S, M, L, XL, XXL)
7. `size_discount_percentage` (Decimal)
8. `size_discount_active` (Boolean)
9. `status` (Enum: available, sold, returned, damaged)
10. `sold_at` (Timestamp)
11. `returned_at` (Timestamp)
12. `created_at` (Timestamp)
13. `updated_at` (Timestamp)

## Migration Status
- ✅ Migration file created
- ✅ Migration executed successfully
- ✅ Columns added to database
- ✅ Model updated to include new fields
- ✅ Database structure verified

## Next Steps

### Frontend Integration (Optional)
If you need to display or edit these fields in the frontend:

1. **POS System**: Update barcode scanning to show HS Code and FBR Invoice No
2. **Dress Management**: Add input fields for HS Code and FBR Invoice No
3. **Reports**: Include these fields in inventory and sales reports
4. **API Endpoints**: Update dress item creation/editing endpoints to handle new fields

### Data Entry
The new columns are nullable, so existing dress items will have NULL values for these fields. You can:
1. Manually update existing records through the admin panel
2. Create a data seeder to populate sample data
3. Update through direct database queries

## Usage Examples

### Creating a New Dress Item
```php
DressItem::create([
    'dress_id' => 1,
    'barcode' => 'ITEM123456',
    'hs_code' => '6204.62.00',
    'fbr_invoice_no' => 'FBR-2025-001',
    'size' => 'M',
    // ... other fields
]);
```

### Updating Existing Item
```php
$dressItem = DressItem::find(1);
$dressItem->update([
    'hs_code' => '6204.62.00',
    'fbr_invoice_no' => 'FBR-2025-001'
]);
```

The database schema update is now complete and ready for use!
