-- Complete Seed Data for TS POS System
-- Run this file to populate the database with sample data
-- Execute in order: Collections -> Dresses -> Dress Items

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data (optional - uncomment if needed)
-- TRUNCATE TABLE `dress_items`;
-- TRUNCATE TABLE `dresses`;
-- TRUNCATE TABLE `collections`;

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ====================
-- COLLECTIONS SEED DATA
-- ====================

INSERT INTO `collections` (`id`, `name`, `description`, `discount_percentage`, `discount_active`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ghazal', 'Premium collection with elegant designs', 5.00, 1, 'active', NOW(), NOW()),
(2, 'Sultana', 'Traditional and contemporary mix', 0.00, 0, 'active', NOW(), NOW()),
(3, 'Laila', 'Casual wear collection', 10.00, 1, 'active', NOW(), NOW()),
(4, 'Rania', 'Luxury formal wear collection', 15.00, 1, 'active', NOW(), NOW()),
(5, 'Zara', 'Modern trendy designs', 8.00, 1, 'active', NOW(), NOW()),
(6, 'Farah', 'Bridal and party wear', 12.00, 1, 'active', NOW(), NOW()),
(7, 'Ayesha', 'Everyday comfort wear', 3.00, 1, 'active', NOW(), NOW()),
(8, 'Mariam', 'Spring summer collection', 20.00, 1, 'active', NOW(), NOW());

-- ====================
-- DRESSES SEED DATA
-- ====================

INSERT INTO `dresses` (`id`, `collection_id`, `name`, `sku`, `description`, `cost_price`, `sale_price`, `discount_percentage`, `discount_active`, `tax_percentage`, `status`, `created_at`, `updated_at`) VALUES
-- Ghazal Collection (ID: 1)
(1, 1, 'Naz', 'GZ001', 'Elegant dress with intricate embroidery', 5000.00, 7000.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),
(2, 1, 'Zara', 'GZ002', 'Contemporary design with traditional touch', 4500.00, 6500.00, 3.00, 1, 18.00, 'active', NOW(), NOW()),

-- Sultana Collection (ID: 2)
(3, 2, 'Meher', 'SU001', 'Traditional design with modern cut', 3500.00, 5500.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),

-- Laila Collection (ID: 3)
(4, 3, 'Casual Chic', 'LA001', 'Perfect for everyday comfort', 2500.00, 4000.00, 5.00, 1, 18.00, 'active', NOW(), NOW()),

-- Rania Collection (ID: 4)
(5, 4, 'Royal Touch', 'RA001', 'Luxury formal wear with golden work', 8000.00, 12000.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),
(6, 4, 'Elegance', 'RA002', 'Sophisticated evening wear', 7500.00, 11000.00, 2.00, 1, 18.00, 'active', NOW(), NOW()),

-- Zara Collection (ID: 5)
(7, 5, 'Trendy Plus', 'ZR001', 'Modern design for young generation', 3000.00, 4500.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),
(8, 5, 'Style Icon', 'ZR002', 'Fashion forward design', 3200.00, 4800.00, 1.00, 1, 18.00, 'active', NOW(), NOW()),

-- Farah Collection (ID: 6)
(9, 6, 'Bridal Dream', 'FA001', 'Perfect for special occasions', 15000.00, 25000.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),
(10, 6, 'Party Queen', 'FA002', 'Glamorous party wear', 12000.00, 18000.00, 5.00, 1, 18.00, 'active', NOW(), NOW()),

-- Ayesha Collection (ID: 7)
(11, 7, 'Comfort First', 'AY001', 'Comfortable daily wear', 2000.00, 3500.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),
(12, 7, 'Easy Elegance', 'AY002', 'Simple yet elegant design', 2200.00, 3800.00, 2.00, 1, 18.00, 'active', NOW(), NOW()),

-- Mariam Collection (ID: 8)
(13, 8, 'Spring Bloom', 'MA001', 'Fresh spring collection', 2800.00, 4200.00, 0.00, 0, 18.00, 'active', NOW(), NOW()),
(14, 8, 'Summer Breeze', 'MA002', 'Light and airy summer wear', 2600.00, 4000.00, 3.00, 1, 18.00, 'active', NOW(), NOW());

-- ====================
-- DRESS ITEMS SEED DATA
-- ====================

INSERT INTO `dress_items` (`id`, `dress_id`, `barcode`, `size`, `size_discount_percentage`, `size_discount_active`, `status`, `sold_at`, `returned_at`, `created_at`, `updated_at`) VALUES
-- Naz (Dress ID: 1) - 4 items
(1, 1, '2507071001', 'XS', 2.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(2, 1, '2507071002', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(3, 1, '2507071003', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(4, 1, '2507071004', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Zara (Dress ID: 2) - 3 items
(5, 2, '2507071005', 'XS', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(6, 2, '2507071006', 'S', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(7, 2, '2507071007', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Meher (Dress ID: 3) - 2 items
(8, 3, '2507071008', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(9, 3, '2507071009', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Casual Chic (Dress ID: 4) - 3 items
(10, 4, '2507071010', 'XS', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(11, 4, '2507071011', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(12, 4, '2507071012', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Royal Touch (Dress ID: 5) - 2 items
(13, 5, '2507071013', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(14, 5, '2507071014', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Elegance (Dress ID: 6) - 2 items
(15, 6, '2507071015', 'M', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(16, 6, '2507071016', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Trendy Plus (Dress ID: 7) - 2 items
(17, 7, '2507071017', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(18, 7, '2507071018', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Style Icon (Dress ID: 8) - 2 items
(19, 8, '2507071019', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(20, 8, '2507071020', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Bridal Dream (Dress ID: 9) - 4 items
(21, 9, '2507071021', 'XS', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(22, 9, '2507071022', 'S', 2.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(23, 9, '2507071023', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(24, 9, '2507071024', 'L', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),

-- Party Queen (Dress ID: 10) - 3 items
(25, 10, '2507071025', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(26, 10, '2507071026', 'M', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(27, 10, '2507071027', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Comfort First (Dress ID: 11) - 3 items
(28, 11, '2507071028', 'XS', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(29, 11, '2507071029', 'S', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(30, 11, '2507071030', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Easy Elegance (Dress ID: 12) - 3 items
(31, 12, '2507071031', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(32, 12, '2507071032', 'M', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(33, 12, '2507071033', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Spring Bloom (Dress ID: 13) - 4 items
(34, 13, '2507071034', 'XS', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(35, 13, '2507071035', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(36, 13, '2507071036', 'M', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(37, 13, '2507071037', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),

-- Summer Breeze (Dress ID: 14) - 4 items
(38, 14, '2507071038', 'XS', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(39, 14, '2507071039', 'S', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),
(40, 14, '2507071040', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(41, 14, '2507071041', 'L', 1.00, 1, 'available', NULL, NULL, NOW(), NOW()),

-- Additional items for better size distribution
(42, 1, '2507071042', 'XS', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(43, 2, '2507071043', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(44, 6, '2507071044', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(45, 9, '2507071045', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(46, 10, '2507071046', 'M', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(47, 12, '2507071047', 'S', 0.00, 0, 'available', NULL, NULL, NOW(), NOW()),
(48, 14, '2507071048', 'L', 0.00, 0, 'available', NULL, NULL, NOW(), NOW());

-- Reset auto increment values
ALTER TABLE `collections` AUTO_INCREMENT = 9;
ALTER TABLE `dresses` AUTO_INCREMENT = 15;
ALTER TABLE `dress_items` AUTO_INCREMENT = 49;

-- Summary
SELECT 
    'Seed data imported successfully!' as message,
    (SELECT COUNT(*) FROM collections) as collections_count,
    (SELECT COUNT(*) FROM dresses) as dresses_count,
    (SELECT COUNT(*) FROM dress_items) as dress_items_count;
