-- ========================================================
-- Database: toko_handika
-- Aplikasi: Web Toko Handika (CodeIgniter 4)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `toko_handika` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `toko_handika`;

-- Nonaktifkan pengecekan foreign key sementara saat setup tabel
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Tabel: users
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `address` TEXT DEFAULT NULL,
    `role_id` INT NOT NULL DEFAULT 2 COMMENT '1: Admin, 2: User/Customer',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel: products
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `price` INT NOT NULL DEFAULT 0,
    `stock` INT NOT NULL DEFAULT 0,
    `description` TEXT DEFAULT NULL,
    `img` VARCHAR(255) NOT NULL DEFAULT 'img/default_helmet.jpg',
    `rating` DECIMAL(3,1) NOT NULL DEFAULT 5.0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel: product_variants
-- --------------------------------------------------------
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `colour` VARCHAR(50) NOT NULL,
    `size` VARCHAR(50) NOT NULL,
    `stock` INT NOT NULL DEFAULT 0,
    INDEX `idx_pv_product` (`product_id`),
    CONSTRAINT `fk_pv_product` FOREIGN KEY (`product_id`) 
        REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Tabel: orders
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `total` INT NOT NULL DEFAULT 0,
    `address` TEXT NOT NULL,
    `payment_method` VARCHAR(100) NOT NULL DEFAULT 'COD',
    `payment_info` TEXT DEFAULT NULL,
    `payment_status` VARCHAR(50) NOT NULL DEFAULT 'unpaid' COMMENT 'unpaid, pending, pending_cod, paid, cancelled',
    `shipping_service` VARCHAR(100) DEFAULT NULL,
    `shipping_cost` INT NOT NULL DEFAULT 0,
    `shipping_status` VARCHAR(50) DEFAULT 'belum_dikirim' COMMENT 'belum_dikirim, diproses, dikirim, sampai, selesai, cancelled',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_orders_user` (`user_id`),
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Tabel: order_items
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `price` INT NOT NULL DEFAULT 0,
    `qty` INT NOT NULL DEFAULT 1,
    `colour` VARCHAR(50) DEFAULT NULL,
    `size` VARCHAR(50) DEFAULT NULL,
    INDEX `idx_oi_order` (`order_id`),
    INDEX `idx_oi_product` (`product_id`),
    CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) 
        REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) 
        REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Tabel: wishlist
-- --------------------------------------------------------
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_wishlist_user` (`user_id`),
    INDEX `idx_wishlist_product` (`product_id`),
    CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) 
        REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- DATA AWAL / SAMPLE DATA
-- ========================================================

--
-- Data Dummy: users
-- Password default:
--   admin@gmail.com  -> admin123
--   user@gmail.com   -> user123
--
INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `role_id`, `created_at`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$xtovsczj/8W4Gx5pHW2uDOgRKXOzcSPW/8oxhVzUY/pzXgWJc7.Da', 'Kantor Pusat Toko Handika, Jl. Raya No. 1, Jakarta', 1, NOW()),
(2, 'Handika Pratama', 'user@gmail.com', '$2y$10$ETeNx6AP.scX03UnkFbIwuP8YWNSORej1aF.DBZn6BGTXgfqQ5m5y', 'Jl. Sudirman No. 45, RT 02 / RW 05, Jakarta Selatan', 2, NOW());

--
-- Data Dummy: products
--
INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`, `description`, `img`, `rating`, `created_at`) VALUES
(1, 'Helm NJS Kairoz DV White Solid', 'Half Face', 680000, 30, 'Helm Half Face NJS Kairoz DV warna White Glossy dengan visor ganda (Double Visor) anti silau, busa lembut dapat dilepas dan dicuci, sertifikasi SNI & DOT.', 'img/1782780091_Kairoz_DV_White_1760599384658_resized1024-jpg.webp', 4.9, NOW()),
(2, 'Helm NJS NR 80s Carbon Twill Doff', 'Retro', 850000, 24, 'Helm Retro Classic NJS NR 80s motif Carbon Twill finishing Doff. Desain elegan bergaya vintage dengan material kuat dan interior premium kulit sintetis.', 'img/1777908112_NR_80s_-_CARBON_TWILL_doff_1760759518106_resized512-png.webp', 4.8, NOW()),
(3, 'Helm NJS Freedom Skyline White', 'Half Face', 450000, 20, 'Helm harian NJS Freedom varian Skyline White. Ringan, ventilasi udara optimal, sistem pengunci micrometric buckle yang aman dan praktis.', 'img/1777976387_Freedom_Skyline_Wite_1759817777548_resized1024-jpg.webp', 4.7, NOW()),
(4, 'Helm Full Face Sport Racing Edition', 'Full Face', 1250000, 15, 'Helm Full Face aerodinamis untuk performa tinggi dan perlindungan maksimal saat touring atau berkendara kecepatan tinggi. Dilengkapi sistem emergency quick release.', 'img/1.webp', 5.0, NOW());

--
-- Data Dummy: product_variants
-- (Total stok varian harus sinkron dengan produk.stock)
--
INSERT INTO `product_variants` (`product_id`, `colour`, `size`, `stock`) VALUES
-- Produk 1 (Total: 30)
(1, 'White Glossy', 'M', 10),
(1, 'White Glossy', 'L', 12),
(1, 'White Glossy', 'XL', 8),

-- Produk 2 (Total: 24)
(2, 'Carbon Doff', 'M', 8),
(2, 'Carbon Doff', 'L', 10),
(2, 'Carbon Doff', 'XL', 6),

-- Produk 3 (Total: 20)
(3, 'Skyline White', 'M', 6),
(3, 'Skyline White', 'L', 8),
(3, 'Skyline White', 'XL', 6),

-- Produk 4 (Total: 15)
(4, 'Black Glossy', 'M', 5),
(4, 'Black Glossy', 'L', 6),
(4, 'Black Glossy', 'XL', 4);

--
-- Data Dummy: orders
--
INSERT INTO `orders` (`id`, `user_id`, `total`, `address`, `payment_method`, `payment_info`, `payment_status`, `shipping_service`, `shipping_cost`, `shipping_status`, `created_at`) VALUES
(1, 2, 695000, 'Jl. Sudirman No. 45, RT 02 / RW 05, Jakarta Selatan', 'BCA Virtual Account', '390184729103', 'paid', 'JNE REG', 15000, 'selesai', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 2, 865000, 'Jl. Sudirman No. 45, RT 02 / RW 05, Jakarta Selatan', 'COD', 'Siapkan uang tunai Rp 865.000 saat kurir datang.', 'pending_cod', 'SiCepat BEST', 15000, 'diproses', NOW());

--
-- Data Dummy: order_items
--
INSERT INTO `order_items` (`order_id`, `product_id`, `price`, `qty`, `colour`, `size`) VALUES
(1, 1, 680000, 1, 'White Glossy', 'L'),
(2, 2, 850000, 1, 'Carbon Doff', 'L');

--
-- Data Dummy: wishlist
--
INSERT INTO `wishlist` (`user_id`, `product_id`, `created_at`) VALUES
(2, 3, NOW()),
(2, 4, NOW());

