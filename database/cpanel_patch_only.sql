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
