-- Collections Seed Data
-- Insert sample collections for the POS system

INSERT INTO `collections` (`id`, `name`, `description`, `discount_percentage`, `discount_active`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ghazal', 'Premium collection with elegant designs', 5.00, 1, 'active', NOW(), NOW()),
(2, 'Sultana', 'Traditional and contemporary mix', 0.00, 0, 'active', NOW(), NOW()),
(3, 'Laila', 'Casual wear collection', 10.00, 1, 'active', NOW(), NOW()),
(4, 'Rania', 'Luxury formal wear collection', 15.00, 1, 'active', NOW(), NOW()),
(5, 'Zara', 'Modern trendy designs', 8.00, 1, 'active', NOW(), NOW()),
(6, 'Farah', 'Bridal and party wear', 12.00, 1, 'active', NOW(), NOW()),
(7, 'Ayesha', 'Everyday comfort wear', 3.00, 1, 'active', NOW(), NOW()),
(8, 'Mariam', 'Spring summer collection', 20.00, 1, 'active', NOW(), NOW());

-- Reset auto increment
ALTER TABLE `collections` AUTO_INCREMENT = 9;
