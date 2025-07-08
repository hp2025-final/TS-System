-- Dresses Seed Data
-- Insert sample dresses for each collection

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

-- Reset auto increment
ALTER TABLE `dresses` AUTO_INCREMENT = 15;
