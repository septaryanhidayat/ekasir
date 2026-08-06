-- QUERY PATCH DENGAN SINTAKS MYSQL/MARIADB 100% KOMPATIBEL
-- Copy dan Paste di Tab 'SQL' pada phpMyAdmin cPanel Anda (`pesonaas_db_ekasir`):

ALTER TABLE `tenants` 
ADD `bank_info` varchar(255) DEFAULT NULL,
ADD `ewallet_info` varchar(255) DEFAULT NULL,
ADD `qris_info` varchar(500) DEFAULT NULL,
ADD `bank_name` varchar(255) DEFAULT NULL,
ADD `bank_account_number` varchar(255) DEFAULT NULL,
ADD `bank_account_holder` varchar(255) DEFAULT NULL,
ADD `ewallet_name` varchar(255) DEFAULT NULL,
ADD `ewallet_account_number` varchar(255) DEFAULT NULL,
ADD `ewallet_account_holder` varchar(255) DEFAULT NULL;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_08_06_000001_add_payment_settings_to_tenants_table', 2),
('2026_08_06_000002_add_structured_payment_fields_to_tenants_table', 2);

-- PATCH TABLE SUPPLIERS & PRODUCTS RELATION
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `suppliers_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `products` ADD `supplier_id` bigint(20) UNSIGNED NULL AFTER `tenant_id`;
ALTER TABLE `products` ADD CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_08_06_000003_create_suppliers_table', 3),
('2026_08_06_000004_add_supplier_id_to_products_table', 3);

