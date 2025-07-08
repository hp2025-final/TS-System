-- Dress Items Seed Data
-- Insert individual dress items with different sizes and barcodes

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

-- Reset auto increment
ALTER TABLE `dress_items` AUTO_INCREMENT = 49;
