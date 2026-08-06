-- phpMyAdmin SQL Dump (Updated for E-Kasir)
-- Database: `pesonaas_db_ekasir`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_flows`
--

CREATE TABLE `cash_flows` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `cash_register_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `cash_flows`
--

INSERT INTO `cash_flows` (`id`, `tenant_id`, `cash_register_id`, `user_id`, `type`, `amount`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 3, 'out', 15000.00, 'Beli Es Batu 2 Plastik & Sedotan', '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(2, 2, 1, 3, 'in', 50000.00, 'Tambahan Pecahan Rp 5.000 untuk Kembalian', '2026-07-31 03:39:56', '2026-07-31 03:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `cash_registers`
--

CREATE TABLE `cash_registers` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `opening_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `closing_amount` decimal(15,2) DEFAULT NULL,
  `expected_amount` decimal(15,2) DEFAULT NULL,
  `variance` decimal(15,2) DEFAULT NULL,
  `opened_at` datetime NOT NULL DEFAULT current_timestamp(),
  `closed_at` datetime DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'open',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `cash_registers`
--

INSERT INTO `cash_registers` (`id`, `tenant_id`, `user_id`, `opening_amount`, `closing_amount`, `expected_amount`, `variance`, `opened_at`, `closed_at`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 200000.00, NULL, NULL, NULL, '2026-07-30 23:39:56', NULL, 'open', 'Shift Pagi Kemang', '2026-07-31 03:39:56', '2026-07-31 03:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` text NOT NULL,
  `exception` text NOT NULL,
  `failed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` text NOT NULL,
  `attempts` int(11) NOT NULL,
  `reserved_at` int(11) DEFAULT NULL,
  `available_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text NOT NULL,
  `options` text DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_31_000001_create_tenants_table', 1),
(5, '2026_07_31_000002_create_products_table', 1),
(6, '2026_07_31_000003_create_cash_registers_table', 1),
(7, '2026_07_31_000004_create_cash_flows_table', 1),
(8, '2026_07_31_000005_create_transactions_table', 1),
(9, '2026_07_31_000006_add_customer_order_fields_to_transactions', 1),
(10, '2026_08_05_000001_create_expenses_table', 2),
(11, '2026_08_06_000001_add_payment_settings_to_tenants_table', 2),
(12, '2026_08_06_000002_add_structured_payment_fields_to_tenants_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `hpp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `tenant_id`, `name`, `barcode`, `image`, `hpp`, `harga_jual`, `stock`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 3, 'Indomie Goreng Spesial', '8992388123456', 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80', 2800.00, 3500.00, 150, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(5, 1, 'Kopi Kapal Api Mix 25g', '8991001100114', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&q=80', 1200.00, 2000.00, 90, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(6, 3, 'Kopi Kapal Api Mix 25g', '8991001100114', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&q=80', 1200.00, 2000.00, 90, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(11, 1, 'Teh Botol Sosro 450ml', '8991002100021', 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=400&q=80', 4000.00, 6000.00, 75, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(12, 3, 'Teh Botol Sosro 450ml', '8991002100021', 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=400&q=80', 4000.00, 6000.00, 75, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(17, 1, 'Roti Sobek Cokelat', '8993005100099', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80', 11000.00, 15000.00, 35, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(18, 3, 'Roti Sobek Cokelat', '8993005100099', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80', 11000.00, 15000.00, 35, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(20, 1, 'Es Teh Manis Jumbo', 'POS-ESTEH-01', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80', 2000.00, 5000.00, 499, 1, '2026-07-31 03:39:56', '2026-08-05 08:25:28'),
(21, 3, 'Es Teh Manis Jumbo', 'POS-ESTEH-01', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80', 2000.00, 5000.00, 500, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(23, 1, 'Nasi Goreng Spesial', 'POS-NASGOR-01', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=400&q=80', 12000.00, 20000.00, 100, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(24, 3, 'Nasi Goreng Spesial', 'POS-NASGOR-01', 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=400&q=80', 12000.00, 20000.00, 100, 1, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(27, 1, 'Pena', 'BRD1785918560976', 'products/CsdXlaspZoIEHbZznQUPkkLj26sxn15Sx8Ogjbx3.png', 2000.00, 2500.00, 50, 1, '2026-08-05 08:29:20', '2026-08-05 08:29:20'),
(28, 2, 'Ciki 2.000 (Tos-tos,dll)', '8995077604225', 'products/LJXRB79eAPRrBj2iZWvwKcGAaRMSF4laHy0y2bgF.jpg', 1800.00, 2000.00, 76, 1, '2026-08-06 03:04:06', '2026-08-06 03:21:12'),
(30, 2, 'Ciki 1.000an  (Semua Ciki, Superstar, dll)', '8996001355046', 'products/PchcDXEpdmih1PfC4cNaMt5HXSZGLa6gHce8kvkg.jpg', 800.00, 1000.00, 43, 1, '2026-08-06 03:07:10', '2026-08-06 03:07:10'),
(31, 2, 'Ciki 500an', '123123123', NULL, 450.00, 500.00, 44, 1, '2026-08-06 03:11:32', '2026-08-06 03:21:36'),
(32, 2, 'Ciki 3.000 (Chitato, Bengbeng, dll)', 'BRD1785985966431', 'products/I1fkUkCoRsCfxdlPkfbMkocfVhhyIkDL8MnYn1ln.jpg', 2800.00, 3000.00, 1, 1, '2026-08-06 03:12:46', '2026-08-06 03:20:49'),
(33, 2, 'Fruit tea', '899', 'products/zqvAjtanCEGKz9qpDoh8zCEJONQK6FUEO7Fd8CfV.jpg', 2800.00, 3000.00, 35, 1, '2026-08-06 03:30:01', '2026-08-06 03:30:01'),
(34, 2, 'Aqua gelas', 'BRD1785987144144', NULL, 450.00, 650.00, 6, 1, '2026-08-06 03:32:24', '2026-08-06 03:32:24'),
(35, 2, 'Lee mineral', 'BRD1785987248406', 'products/jnr2mi03eEqGztKzSAcHghIVK5GWVLoFkf4PVf28.jpg', 3500.00, 4000.00, 6, 1, '2026-08-06 03:34:08', '2026-08-06 03:34:08'),
(36, 2, 'Floridina', 'BRD1785987304268', 'products/1k53nF6D6vD7FekeMiYOFCFftfnscdXTPMisih3Y.jpg', 3500.00, 4000.00, 10, 1, '2026-08-06 03:35:04', '2026-08-06 03:35:04'),
(37, 2, 'Isoplus', 'BRD1785987346112', 'products/bq2YOTM0jzK8MUVJ2knHuCN5JN70BD9abgsNmveb.jpg', 3500.00, 4000.00, 3, 1, '2026-08-06 03:35:46', '2026-08-06 03:35:46'),
(38, 2, 'Golda', 'BRD1785987394538', 'products/DwdcXTHkaFHBiygil7p6AWZIUaSKmqUFe9SPruf0.jpg', 3500.00, 4000.00, 10, 1, '2026-08-06 03:36:34', '2026-08-06 03:36:34'),
(39, 2, 'S tee', 'BRD1785987499661', 'products/o0rqgcGC8h3Op9I0iFmLnYz3e7b1dNtz2VIa9lOW.jpg', 3500.00, 4000.00, 17, 1, '2026-08-06 03:38:19', '2026-08-06 03:38:19'),
(40, 2, 'S tee', 'BRD1785987517312', 'products/BH6YsKKNDa4uCvJt4SUM4RgjRs869EQLyjxCwAlT.jpg', 3500.00, 4000.00, 17, 1, '2026-08-06 03:38:37', '2026-08-06 03:38:37');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `bank_info` varchar(255) DEFAULT NULL,
  `ewallet_info` varchar(255) DEFAULT NULL,
  `qris_info` varchar(500) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `bank_account_holder` varchar(255) DEFAULT NULL,
  `ewallet_name` varchar(255) DEFAULT NULL,
  `ewallet_account_number` varchar(255) DEFAULT NULL,
  `ewallet_account_holder` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `name`, `code`, `address`, `phone`, `bank_info`, `ewallet_info`, `qris_info`, `bank_name`, `bank_account_number`, `bank_account_holder`, `ewallet_name`, `ewallet_account_number`, `ewallet_account_holder`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Robbani Mart', 'OUT-001', 'Jl. Sarjana Blok A Timbangan', '082181898928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-31 03:39:53', '2026-08-05 08:35:15'),
(2, 'Kantin SD', 'OUT-002', 'Jl. Sarjana Blok A Timbangan', '082181898928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-31 03:39:53', '2026-08-05 08:35:22'),
(3, 'Kantin SMP', 'OUT-003', 'Jl. Pegagan Timbangan', '082181898928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-31 03:39:53', '2026-08-05 08:35:32'),
(4, 'Kedai Robbani', 'OUT-004', 'Jl. Pegagan Timbangan', '082181898928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-31 03:44:12', '2026-08-05 08:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cash_register_id` int(11) DEFAULT NULL,
  `total_hpp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cash_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) NOT NULL DEFAULT 'cash',
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `order_type` varchar(255) NOT NULL DEFAULT 'dine_in',
  `table_number` varchar(255) DEFAULT NULL,
  `order_source` varchar(255) NOT NULL DEFAULT 'pos',
  `order_status` varchar(255) NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `invoice_number`, `tenant_id`, `user_id`, `cash_register_id`, `total_hpp`, `total_amount`, `cash_paid`, `change_amount`, `payment_method`, `status`, `created_at`, `updated_at`, `customer_name`, `customer_phone`, `order_type`, `table_number`, `order_source`, `order_status`, `notes`) VALUES
(1, 'INV/20260731/0001', 2, 3, 1, 8100.00, 11000.00, 50000.00, 39000.00, 'cash', 'completed', '2026-07-31 00:39:56', '2026-07-31 03:39:56', NULL, NULL, 'dine_in', NULL, 'pos', 'completed', NULL),
(2, 'INV/20260731/0002', 2, 3, 1, 12000.00, 20000.00, 20000.00, 0.00, 'cash', 'completed', '2026-07-31 02:39:56', '2026-07-31 03:39:56', NULL, NULL, 'dine_in', NULL, 'pos', 'completed', NULL),
(3, 'INV/20260805/0001', 1, NULL, NULL, 8000.00, 11500.00, 11500.00, 0.00, 'qris', 'completed', '2026-08-05 07:09:49', '2026-08-05 07:09:49', 'tesr', '0888', 'dine_in', NULL, 'customer_app', 'paid', NULL),
(4, 'INV/20260805/0002', 1, 2, NULL, 10000.00, 16500.00, 16500.00, 0.00, 'qris', 'completed', '2026-08-05 08:25:28', '2026-08-05 09:03:25', 'Yuhu', NULL, 'dine_in', NULL, 'customer_app', 'ready', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `product_name`, `cost_price`, `selling_price`, `qty`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Indomie Goreng Spesial', 2800.00, 3500.00, 2, 7000.00, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(2, 1, 7, 'Air Mineral Aqua 600ml', 2500.00, 4000.00, 1, 4000.00, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(3, 2, 22, 'Nasi Goreng Spesial', 12000.00, 20000.00, 1, 20000.00, '2026-07-31 03:39:56', '2026-07-31 03:39:56'),
(4, 3, 14, 'Chitato Sapi Panggang 68g', 8000.00, 11500.00, 1, 11500.00, '2026-08-05 07:09:50', '2026-08-05 07:09:50'),
(5, 4, 14, 'Chitato Sapi Panggang 68g', 8000.00, 11500.00, 1, 11500.00, '2026-08-05 08:25:28', '2026-08-05 08:25:28'),
(6, 4, 20, 'Es Teh Manis Jumbo', 2000.00, 5000.00, 1, 5000.00, '2026-08-05 08:25:28', '2026-08-05 08:25:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'cashier',
  `pin` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `pin`, `phone`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Septa Ryan Hidayat (Owner)', 'owner@ekasir.com', NULL, '$2y$12$rHwCDG.tbP6MzH6ZsM0KOuZ/vPUCOOiDvR1FUVEDNCv6NhHco2LP2', 'superadmin', '123456', NULL, NULL, 'RzoAfCftEoQooSdyYucP35RzWZOGvX5gsaNeIu6C6GjpvhvcullWHqJLMske', '2026-07-31 03:39:53', '2026-07-31 03:42:33'),
(2, 1, 'Tutik Wahyuni (Manager)', 'manager@ekasir.com', NULL, '$2y$12$coWaa2kNTpjFwVdlg8itkeVPo.Uv5fw83qu4HBtaeN0g9OoGKO9F.', 'manager', '123456', NULL, NULL, NULL, '2026-07-31 03:39:54', '2026-08-05 08:19:38'),
(3, 1, 'Nazila', 'warung@ekasir.com', NULL, '$2y$12$jxI.E.7RD.KjJ2w/gzb5keIZ/Xf.cp2oUryKBckIPG/N7bYIHTS6K', 'cashier', '123456', NULL, NULL, NULL, '2026-07-31 03:39:55', '2026-08-05 08:21:49'),
(4, 2, 'Ria', 'kasirsd@ekasir.com', NULL, '$2y$12$Wo0VSotEWvCJNemcuO3K.ez.rVVR/qyicB/tsK5./YnPua2niGj4G', 'cashier', '123456', NULL, NULL, NULL, '2026-07-31 03:39:56', '2026-08-05 08:22:04'),
(5, 3, 'Aslamiyah', 'kasirsmp@ekasir.com', NULL, '$2y$12$z81khueJjQ8M2sQ8QcszjuPJgf6.2tqvQ6yiuQhmtfY9Wq/mkKCBK', 'cashier', '123456', NULL, NULL, NULL, '2026-08-05 08:22:36', '2026-08-05 08:22:36'),
(6, 4, 'Nazila', 'kedai@ekasir.com', NULL, '$2y$12$WjKgsRi1xz59JYqiIrWpauw8Bj9TEeYkE4cCnRKRCCal7zeGroAOC', 'manager', '123456', NULL, NULL, NULL, '2026-08-05 08:32:29', '2026-08-05 08:32:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cash_flows`
--
ALTER TABLE `cash_flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_flows_cash_register_id_index` (`cash_register_id`),
  ADD KEY `cash_flows_tenant_id_index` (`tenant_id`),
  ADD KEY `cash_flows_user_id_index` (`user_id`);

--
-- Indexes for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_registers_tenant_id_index` (`tenant_id`),
  ADD KEY `cash_registers_user_id_index` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_tenant_id_index` (`tenant_id`),
  ADD KEY `expenses_user_id_index` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_barcode_index` (`barcode`),
  ADD KEY `products_tenant_id_index` (`tenant_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`),
  ADD KEY `sessions_user_id_index` (`user_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_code_unique` (`code`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_invoice_number_unique` (`invoice_number`),
  ADD KEY `transactions_cash_register_id_index` (`cash_register_id`),
  ADD KEY `transactions_tenant_id_index` (`tenant_id`),
  ADD KEY `transactions_user_id_index` (`user_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `transaction_details_product_id_index` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_tenant_id_index` (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cash_flows`
--
ALTER TABLE `cash_flows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_registers`
--
ALTER TABLE `cash_registers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
