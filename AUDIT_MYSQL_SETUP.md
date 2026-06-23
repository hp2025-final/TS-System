# Audit Feature - MySQL Setup Guide

## 📋 Complete MySQL Setup for Audit Feature

### Step 1: Create the Audits Table

Run this SQL in your MySQL database (phpMyAdmin or MySQL CLI):

```sql
CREATE TABLE IF NOT EXISTS audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dress_item_id INT NOT NULL,
    barcode VARCHAR(255) NOT NULL,
    collection_name VARCHAR(255),
    dress_name VARCHAR(255),
    dress_size VARCHAR(50),
    scanned_by INT NULL,
    dress_details JSON NULL,
    scan_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_audits_dress_item
        FOREIGN KEY (dress_item_id)
        REFERENCES dress_items(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_audits_scanned_by
        FOREIGN KEY (scanned_by)
        REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: Create Indexes

```sql
CREATE INDEX idx_audits_barcode
    ON audits(barcode);

CREATE INDEX idx_audits_scan_date
    ON audits(scan_date);

CREATE INDEX idx_audits_scanned_by
    ON audits(scanned_by);

CREATE INDEX idx_audits_collection_name
    ON audits(collection_name);

CREATE INDEX idx_audits_dress_name
    ON audits(dress_name);

CREATE INDEX idx_audits_dress_size
    ON audits(dress_size);
```

### Step 3: Verify Table Creation

```sql
DESCRIBE audits;
```

**Expected Output:**
```
+------------------+--------------+------+-----+-------------------+-------------------+
| Field            | Type         | Null | Key | Default           | Extra             |
+------------------+--------------+------+-----+-------------------+-------------------+
| id               | int          | NO   | PRI | NULL              | auto_increment    |
| dress_item_id    | int          | NO   | MUL | NULL              |                   |
| barcode          | varchar(255) | NO   | MUL | NULL              |                   |
| collection_name  | varchar(255) | YES  | MUL | NULL              |                   |
| dress_name       | varchar(255) | YES  | MUL | NULL              |                   |
| dress_size       | varchar(50)  | YES  | MUL | NULL              |                   |
| scanned_by       | int          | YES  | MUL | NULL              |                   |
| dress_details    | json         | YES  |     | NULL              |                   |
| scan_date        | datetime     | NO   | MUL | CURRENT_TIMESTAMP |                   |
| created_at       | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at       | timestamp    | YES  |     | CURRENT_TIMESTAMP | DEFAULT_...       |
+------------------+--------------+------+-----+-------------------+-------------------+
```

---

## 🔄 Alternative: If Table Already Exists

If you already created the audits table without `dress_size` column, run this:

```sql
-- Add missing columns (if needed)
ALTER TABLE audits ADD COLUMN collection_name VARCHAR(255) NULL AFTER barcode;
ALTER TABLE audits ADD COLUMN dress_name VARCHAR(255) NULL AFTER collection_name;
ALTER TABLE audits ADD COLUMN dress_size VARCHAR(50) NULL AFTER dress_name;

-- Add indexes
CREATE INDEX idx_audits_collection_name ON audits(collection_name);
CREATE INDEX idx_audits_dress_name ON audits(dress_name);
CREATE INDEX idx_audits_dress_size ON audits(dress_size);
```

---

## 🚀 Frontend Setup

After creating the database table, build the frontend:

```bash
cd c:\xampp\htdocs\TS-POS-V1
npm install
npm run build
```

---

## ✅ Testing

### 1. Check Table Structure
```sql
DESCRIBE audits;
SHOW INDEXES FROM audits;
```

### 2. Test Scan API
```bash
# Login first
curl -X POST http://127.0.0.1:8000/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@tspos.com\",\"password\":\"password\"}"

# Get the token from response, then test scan
curl -X POST http://127.0.0.1:8000/api/audit/scan ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"barcode\":\"2503071\"}"
```

### 3. Check Data Saved
```sql
SELECT 
    id,
    barcode,
    collection_name,
    dress_name,
    dress_size,
    scan_date,
    scanned_by
FROM audits
ORDER BY scan_date DESC
LIMIT 10;
```

**Expected Result:**
```
+----+----------+------------------+---------------------+------------+---------------------+-------------+
| id | barcode  | collection_name  | dress_name          | dress_size | scan_date           | scanned_by  |
+----+----------+------------------+---------------------+------------+---------------------+-------------+
| 1  | 2503071  | Summer 2024      | Floral Maxi Dress   | M          | 2025-01-20 14:30:00 | 1           |
+----+----------+------------------+---------------------+------------+---------------------+-------------+
```

---

## 📊 Database Schema

```
audits
├── id (INT, PK, AUTO_INCREMENT)
├── dress_item_id (INT, FK → dress_items.id)
├── barcode (VARCHAR 255)
├── collection_name (VARCHAR 255) ⬅️ Direct storage for fast access
├── dress_name (VARCHAR 255)      ⬅️ Direct storage for fast access
├── dress_size (VARCHAR 50)       ⬅️ Direct storage for fast access
├── scanned_by (INT, FK → users.id)
├── dress_details (JSON)          ⬅️ Full snapshot backup
├── scan_date (DATETIME)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)

Indexes:
├── PRIMARY KEY (id)
├── INDEX (barcode)
├── INDEX (scan_date)
├── INDEX (scanned_by)
├── INDEX (collection_name)
├── INDEX (dress_name)
└── INDEX (dress_size)
```

---

## 🔧 phpMyAdmin Quick Steps

1. Open **phpMyAdmin** (http://127.0.0.1/phpmyadmin)
2. Select your database from the left sidebar
3. Click **SQL** tab at the top
4. Copy and paste the **CREATE TABLE** statement
5. Click **Go** button
6. Copy and paste the **CREATE INDEX** statements
7. Click **Go** button
8. Go to **Structure** tab to verify table was created
9. You should see the `audits` table with all columns

---

## 🐛 Troubleshooting

### Error: Table 'audits' already exists
```sql
DROP TABLE IF EXISTS audits;
-- Then run the CREATE TABLE statement again
```

### Error: Foreign key constraint fails
Make sure `dress_items` and `users` tables exist first:
```sql
SHOW TABLES LIKE 'dress_items';
SHOW TABLES LIKE 'users';
```

### Error: Cannot add foreign key constraint
Check if the referenced columns exist and have correct types:
```sql
DESCRIBE dress_items;
DESCRIBE users;
```

---

## ✨ All Set!

After completing these steps:

1. ✅ Database table created with all columns
2. ✅ Indexes created for fast queries
3. ✅ Foreign keys set up properly
4. ✅ Frontend built and ready

**Access the Audit page at:** http://127.0.0.1:8000/audit

Start scanning! 🎉
