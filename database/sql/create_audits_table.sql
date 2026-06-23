-- Simple Audit Table for MySQL
-- Stores collection name, dress name, size, and status directly

CREATE TABLE IF NOT EXISTS audits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dress_item_id BIGINT UNSIGNED NOT NULL,
    barcode VARCHAR(255) NOT NULL,
    collection_name VARCHAR(255) NOT NULL,
    dress_name VARCHAR(255) NOT NULL,
    size VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    scanned_by BIGINT UNSIGNED NULL,
    scan_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (dress_item_id) REFERENCES dress_items(id) ON DELETE CASCADE,
    FOREIGN KEY (scanned_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX (barcode),
    INDEX (scan_date),
    INDEX (collection_name),
    INDEX (dress_name),
    INDEX (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
