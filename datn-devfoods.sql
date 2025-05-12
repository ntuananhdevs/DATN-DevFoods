-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 12, 2025 at 09:48 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datn-devfoods`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `address_line` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ward` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `opening_hour` time NOT NULL,
  `closing_hour` time NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rating` decimal(3,2) NOT NULL DEFAULT '5.00',
  `reliability_score` int NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mì Ý', NULL, NULL, 1, '2025-05-09 07:11:43', '2025-05-09 07:11:43'),
(2, 'Gà Rán', NULL, NULL, 1, '2025-05-09 07:11:43', '2025-05-09 07:11:43'),
(3, 'Hamburger', NULL, NULL, 1, '2025-05-09 07:11:43', '2025-05-09 07:11:43'),
(4, 'Pizza', NULL, NULL, 1, '2025-05-09 07:11:43', '2025-05-09 07:11:43');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_at` datetime NOT NULL,
  `status` enum('sent','delivered','read') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `read_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_system_message` tinyint(1) NOT NULL DEFAULT '0',
  `related_order_id` bigint UNSIGNED DEFAULT NULL,
  `sender_type` enum('customer','branch_admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver_type` enum('customer','branch_admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_ranks`
--

CREATE TABLE `customer_ranks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` enum('bronze','silver','gold','diamond') COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_points` int NOT NULL,
  `max_points` int DEFAULT NULL,
  `discount_rate` decimal(5,2) NOT NULL,
  `points_multiplier` int NOT NULL DEFAULT '1',
  `free_shipping` tinyint(1) NOT NULL DEFAULT '0',
  `priority_support` int NOT NULL DEFAULT '0',
  `benefits` text COLLATE utf8mb4_unicode_ci,
  `badge_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `rank` enum('bronze','silver','gold','diamond') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_rate` decimal(5,2) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `minimum_order_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `usage_limit` int DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `license_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_registration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `current_latitude` decimal(10,8) DEFAULT NULL,
  `current_longitude` decimal(11,8) DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rating` decimal(3,2) NOT NULL DEFAULT '5.00',
  `cancellation_count` int NOT NULL DEFAULT '0',
  `reliability_score` int NOT NULL DEFAULT '100',
  `penalty_count` int NOT NULL DEFAULT '0',
  `auto_deposit_earnings` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_05_03_010001_create_roles_table', 1),
(4, '2025_05_03_010002_create_users_table', 1),
(5, '2025_05_03_010003_create_addresses_table', 1),
(6, '2025_05_03_020001_create_categories_table', 1),
(7, '2025_05_03_020002_create_products_table', 1),
(8, '2025_05_03_020003_create_product_variants_table', 1),
(9, '2025_05_03_020004_create_variant_attributes_table', 1),
(10, '2025_05_03_020005_create_variant_values_table', 1),
(11, '2025_05_03_030001_create_branches_table', 1),
(12, '2025_05_03_030002_create_drivers_table', 1),
(13, '2025_05_03_035000_create_payment_methods_table', 1),
(14, '2025_05_03_035001_create_payments_table', 1),
(15, '2025_05_03_035002_create_discount_codes_table', 1),
(16, '2025_05_03_035003_create_discounts_table', 1),
(17, '2025_05_03_040001_create_orders_table', 1),
(18, '2025_05_03_040002_create_order_details_table', 1),
(19, '2025_05_03_040003_create_order_tracking_table', 1),
(20, '2025_05_03_040004_create_order_cancellations_table', 1),
(21, '2025_05_03_055001_create_product_reviews_table', 1),
(22, '2025_05_03_055002_create_review_replies_table', 1),
(23, '2025_05_03_060001_create_customer_ranks_table', 1),
(24, '2025_05_03_060002_create_points_transactions_table', 1),
(25, '2025_05_03_080001_create_promotions_table', 1),
(26, '2025_05_03_080002_create_promotion_products_table', 1),
(27, '2025_05_03_100001_create_chat_messages_table', 1),
(28, '2025_05_08_161057_add_soft_delete_to_tables', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `address_id` bigint UNSIGNED NOT NULL,
  `payment_id` bigint UNSIGNED DEFAULT NULL,
  `discount_code_id` bigint UNSIGNED DEFAULT NULL,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` datetime NOT NULL,
  `estimated_delivery_time` datetime DEFAULT NULL,
  `actual_delivery_time` datetime DEFAULT NULL,
  `status` enum('new','processing','ready','delivery','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `points_earned` int NOT NULL DEFAULT '0',
  `points_status` enum('awarded','pending','cancelled','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `refund_status` enum('requested','processing','refunded','rejected') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `responsible_party` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_confirmation_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_confirmation_time` datetime DEFAULT NULL,
  `delivery_confirmation_gps` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_balance_payment` tinyint(1) NOT NULL DEFAULT '0',
  `transaction_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_cancellations`
--

CREATE TABLE `order_cancellations` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `cancelled_by` bigint UNSIGNED DEFAULT NULL,
  `cancellation_type` enum('customer_cancel','driver_cancel','restaurant_cancel','system_cancel') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cancellation_date` datetime NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cancellation_stage` enum('before_processing','processing','ready_for_delivery','during_delivery') COLLATE utf8mb4_unicode_ci NOT NULL,
  `penalty_applied` tinyint(1) NOT NULL DEFAULT '0',
  `penalty_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `points_deducted` int NOT NULL DEFAULT '0',
  `evidence` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_tracking`
--

CREATE TABLE `order_tracking` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `tracking_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_date` datetime NOT NULL,
  `payment_details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('cash','bank_transfer','credit_card','e_wallet','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `points_transactions`
--

CREATE TABLE `points_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `points` int NOT NULL,
  `balance` int NOT NULL,
  `type` enum('order','referral','review','promotion','adjustment','expiration') COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `review_id` bigint UNSIGNED DEFAULT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `transaction_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `is_expired` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(10,2) NOT NULL,
  `stock` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preparation_time` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `base_price`, `stock`, `image`, `preparation_time`, `created_at`, `updated_at`) VALUES
(1, 2, 'Gà rán nguyên con', 'Gà rán nguyên con giòn rụm', '100000.00', 1, NULL, 15, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(2, 2, 'Gà rán 2 miếng', '2 miếng gà rán giòn', '50000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(3, 2, 'Combo gà + khoai + nước', 'Gà rán, khoai tây chiên và nước ngọt', '85000.00', 1, NULL, 12, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(4, 2, 'Combo tiết kiệm 2', '2 miếng gà + nước', '80000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(5, 2, 'Cánh gà chiên mắm', 'Cánh gà chiên nước mắm đậm đà', '60000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(6, 3, 'Burger gà', 'Burger nhân gà giòn', '45000.00', 1, NULL, 6, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(7, 2, 'Cơm gà sốt cay', 'Cơm gà sốt cay Hàn Quốc', '55000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(8, 2, 'Gà lắc phô mai', 'Gà chiên giòn lắc phô mai', '40000.00', 1, NULL, 7, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(9, 2, 'Gà không xương', 'Miếng gà không xương chiên giòn', '65000.00', 1, NULL, 8, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(10, 2, 'Gà sốt Hàn Quốc', 'Gà rán phủ sốt Hàn Quốc', '70000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(11, 2, 'Gà chiên nước mắm', 'Gà chiên truyền thống vị mắm', '60000.00', 1, NULL, 9, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(12, 2, 'Khoai tây chiên', 'Khoai tây chiên giòn', '30000.00', 1, NULL, 5, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(13, 2, 'Cơm gà trứng ốp la', 'Cơm gà kèm trứng ốp la', '55000.00', 1, NULL, 9, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(14, 2, 'Combo tiết kiệm 1', '1 miếng gà + nước + khoai', '75000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(15, 2, 'Combo gia đình', 'Combo lớn cho 4 người', '180000.00', 1, NULL, 15, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(16, 2, 'Pepsi lon', 'Pepsi mát lạnh', '15000.00', 1, NULL, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(17, 2, '7Up lon', '7Up vị chanh', '15000.00', 1, NULL, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(18, 2, 'Trà chanh', 'Trà chanh tươi mát', '12000.00', 1, NULL, 2, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(19, 2, 'Trà đào', 'Trà đào miếng', '18000.00', 1, NULL, 2, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(20, 2, 'Gà viên chiên', 'Viên gà chiên giòn', '50000.00', 1, NULL, 8, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(21, 2, 'Gà popcorn', 'Gà popcorn mini', '45000.00', 1, NULL, 7, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(22, 2, 'Gà sốt mật ong', 'Gà sốt mật ong dịu ngọt', '65000.00', 1, NULL, 9, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(23, 2, 'Salad gà', 'Rau củ trộn gà', '40000.00', 1, NULL, 5, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(24, 2, 'Gà cay đặc biệt', 'Gà siêu cay cho người mê cay', '70000.00', 1, NULL, 10, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(25, 2, 'Combo đôi', '2 miếng gà + khoai + 2 nước', '95000.00', 1, NULL, 12, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(26, 2, 'Combo 3 người', '3 phần gà + 3 nước', '130000.00', 1, NULL, 13, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(27, 2, 'Combo 5 người', '5 phần gà + khoai + nước', '210000.00', 1, NULL, 15, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(28, 2, 'Gà tẩm bột chiên giòn', 'Gà tẩm bột đặc biệt', '60000.00', 1, NULL, 9, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(29, 2, 'Nước suối', 'Nước suối Aquafina', '10000.00', 1, NULL, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(30, 2, 'Gà quay tiêu', 'Gà quay ướp tiêu đen', '80000.00', 1, NULL, 12, '2025-05-12 02:28:00', '2025-05-12 02:28:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `rating` int NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci,
  `review_date` datetime NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `review_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT '1',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `helpful_count` int NOT NULL DEFAULT '0',
  `report_count` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `name`, `price`, `image`, `stock_quantity`, `active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cay - Nhỏ - Tương ớt', '95000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(2, 1, 'Không cay - Vừa - Tương cà', '100000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(3, 1, 'Siêu cay - Lớn - Mù tạt', '105000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(4, 2, 'Cay - Nhỏ - Tương ớt', '45000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(5, 2, 'Không cay - Vừa - Tương cà', '50000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(6, 2, 'Siêu cay - Lớn - Mù tạt', '55000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(7, 3, 'Cay - Nhỏ - Tương ớt', '80000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(8, 3, 'Không cay - Vừa - Tương cà', '85000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(9, 3, 'Siêu cay - Lớn - Mù tạt', '90000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(10, 4, 'Cay - Nhỏ - Tương ớt', '75000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(11, 4, 'Không cay - Vừa - Tương cà', '80000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(12, 4, 'Siêu cay - Lớn - Mù tạt', '85000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(13, 5, 'Cay - Nhỏ - Tương ớt', '55000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(14, 5, 'Không cay - Vừa - Tương cà', '60000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(15, 5, 'Siêu cay - Lớn - Mù tạt', '65000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(16, 6, 'Cay - Nhỏ - Tương ớt', '40000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(17, 6, 'Không cay - Vừa - Tương cà', '45000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(18, 6, 'Siêu cay - Lớn - Mù tạt', '50000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(19, 7, 'Cay - Nhỏ - Tương ớt', '50000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(20, 7, 'Không cay - Vừa - Tương cà', '55000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(21, 7, 'Siêu cay - Lớn - Mù tạt', '60000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(22, 8, 'Cay - Nhỏ - Tương ớt', '35000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(23, 8, 'Không cay - Vừa - Tương cà', '40000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(24, 8, 'Siêu cay - Lớn - Mù tạt', '45000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(25, 9, 'Cay - Nhỏ - Tương ớt', '60000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(26, 9, 'Không cay - Vừa - Tương cà', '65000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(27, 9, 'Siêu cay - Lớn - Mù tạt', '70000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(28, 10, 'Cay - Nhỏ - Tương ớt', '65000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(29, 10, 'Không cay - Vừa - Tương cà', '70000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(30, 10, 'Siêu cay - Lớn - Mù tạt', '75000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(31, 11, 'Cay - Nhỏ - Tương ớt', '55000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(32, 11, 'Không cay - Vừa - Tương cà', '60000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(33, 11, 'Siêu cay - Lớn - Mù tạt', '65000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(34, 12, 'Cay - Nhỏ - Tương ớt', '25000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(35, 12, 'Không cay - Vừa - Tương cà', '30000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(36, 12, 'Siêu cay - Lớn - Mù tạt', '35000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(37, 13, 'Cay - Nhỏ - Tương ớt', '50000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(38, 13, 'Không cay - Vừa - Tương cà', '55000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(39, 13, 'Siêu cay - Lớn - Mù tạt', '60000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(40, 14, 'Cay - Nhỏ - Tương ớt', '70000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(41, 14, 'Không cay - Vừa - Tương cà', '75000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(42, 14, 'Siêu cay - Lớn - Mù tạt', '80000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(43, 15, 'Cay - Nhỏ - Tương ớt', '170000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(44, 15, 'Không cay - Vừa - Tương cà', '180000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(45, 15, 'Siêu cay - Lớn - Mù tạt', '190000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(46, 16, 'Không cay - Nhỏ - Không chấm', '13000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(47, 16, 'Không cay - Vừa - Không chấm', '15000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(48, 16, 'Không cay - Lớn - Không chấm', '17000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(49, 17, 'Không cay - Nhỏ - Không chấm', '13000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(50, 17, 'Không cay - Vừa - Không chấm', '15000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(51, 17, 'Không cay - Lớn - Không chấm', '17000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(52, 18, 'Không cay - Nhỏ - Không chấm', '10000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(53, 18, 'Không cay - Vừa - Không chấm', '12000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(54, 18, 'Không cay - Lớn - Không chấm', '14000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(55, 19, 'Không cay - Nhỏ - Không chấm', '16000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(56, 19, 'Không cay - Vừa - Không chấm', '18000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(57, 19, 'Không cay - Lớn - Không chấm', '20000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(58, 20, 'Cay - Nhỏ - Tương ớt', '45000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(59, 20, 'Không cay - Vừa - Tương cà', '50000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(60, 20, 'Siêu cay - Lớn - Mù tạt', '55000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(61, 21, 'Cay - Nhỏ - Tương ớt', '40000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(62, 21, 'Không cay - Vừa - Tương cà', '45000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(63, 21, 'Siêu cay - Lớn - Mù tạt', '50000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(64, 22, 'Cay - Nhỏ - Tương ớt', '60000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(65, 22, 'Không cay - Vừa - Tương cà', '65000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(66, 22, 'Siêu cay - Lớn - Mù tạt', '70000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(67, 23, 'Cay - Nhỏ - Tương ớt', '35000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(68, 23, 'Không cay - Vừa - Tương cà', '40000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(69, 23, 'Siêu cay - Lớn - Mù tạt', '45000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(70, 24, 'Cay - Nhỏ - Tương ớt', '65000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(71, 24, 'Không cay - Vừa - Tương cà', '70000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(72, 24, 'Siêu cay - Lớn - Mù tạt', '75000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(73, 25, 'Cay - Nhỏ - Tương ớt', '90000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(74, 25, 'Không cay - Vừa - Tương cà', '95000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(75, 25, 'Siêu cay - Lớn - Mù tạt', '100000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(76, 26, 'Cay - Nhỏ - Tương ớt', '120000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(77, 26, 'Không cay - Vừa - Tương cà', '130000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(78, 26, 'Siêu cay - Lớn - Mù tạt', '140000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(79, 27, 'Cay - Nhỏ - Tương ớt', '200000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(80, 27, 'Không cay - Vừa - Tương cà', '210000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(81, 27, 'Siêu cay - Lớn - Mù tạt', '220000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(82, 28, 'Cay - Nhỏ - Tương ớt', '55000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(83, 28, 'Không cay - Vừa - Tương cà', '60000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(84, 28, 'Siêu cay - Lớn - Mù tạt', '65000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(85, 29, 'Không cay - Nhỏ - Không chấm', '8000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(86, 29, 'Không cay - Vừa - Không chấm', '10000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(87, 29, 'Không cay - Lớn - Không chấm', '12000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(88, 30, 'Cay - Nhỏ - Tương ớt', '75000.00', NULL, 20, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(89, 30, 'Không cay - Vừa - Tương cà', '80000.00', NULL, 15, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(90, 30, 'Siêu cay - Lớn - Mù tạt', '85000.00', NULL, 10, 1, '2025-05-12 02:28:00', '2025-05-12 02:28:00');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `promotion_type` enum('product','category','order','shipping') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `discount_unit` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_order_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `usage_limit` int DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion_products`
--

CREATE TABLE `promotion_products` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `special_price` decimal(10,2) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_replies`
--

CREATE TABLE `review_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_date` datetime NOT NULL,
  `is_official` tinyint(1) NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `helpful_count` int NOT NULL DEFAULT '0',
  `report_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', '[\"*\"]', NULL, NULL),
(2, 'manager', '[\"create\", \"edit\", \"view\"]', NULL, NULL),
(3, 'staff', '[\"view\"]', NULL, NULL),
(4, 'customer', '[\"view\"]', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9N6VFZR5dPHKcUqbozaAtmuzQYLmS18JJGPvJJzw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT0Fnb2p4Q2JPMXdwVjhxSnhIWGV1STE2cXB2RkdWQzE2STd3NnF3NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zaG9wL3Byb2R1Y3QvcHJvZHVjdC1kZXRhaWwvMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1747043202);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `user_name`, `full_name`, `email`, `phone`, `avatar`, `google_id`, `balance`, `active`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'sporer.shanna', 'Beryl Miller', 'carole52@example.net', '+1-713-654-2545', 'avatars/default.jpg', NULL, '272.47', 1, '2025-05-09 07:11:42', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'taxb6d2W0A', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(2, 1, 'ubarrows', 'Dr. Gus Kiehn', 'kathleen05@example.com', '+14637602177', 'avatars/default.jpg', NULL, '988.15', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'MBchDzdKLO', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(3, 2, 'jan.bayer', 'Norberto Beatty', 'moore.akeem@example.org', '323-405-1079', 'avatars/default.jpg', NULL, '282.36', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'uANmqchqOX', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(4, 4, 'raheem.kris', 'Kade Cummerata V', 'mebert@example.net', '727-763-1753', 'avatars/default.jpg', NULL, '463.62', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'BlQhavz16K', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(5, 4, 'domingo.conn', 'Prof. Dennis Hagenes Sr.', 'gcruickshank@example.net', '820-946-2077', 'avatars/default.jpg', NULL, '159.94', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'iJ8QdaPrYM', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(6, 4, 'jay.wiza', 'Magdalen Walsh', 'regan.bernhard@example.net', '820.606.4403', 'avatars/default.jpg', NULL, '402.03', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'lS0TsmUE5e', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(7, 4, 'stokes.carroll', 'Amalia Kassulke', 'emmett.wyman@example.com', '931-853-9105', 'avatars/default.jpg', NULL, '341.17', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'kLbfN1Ueri', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(8, 1, 'qveum', 'Tess Lehner III', 'chesley.stracke@example.net', '1-812-577-9906', 'avatars/default.jpg', NULL, '287.78', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'K96XzTB2X9', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(9, 3, 'bryon22', 'Montana Bergstrom', 'huels.mathias@example.com', '551.399.6519', 'avatars/default.jpg', NULL, '595.36', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', '5g0O0VeTGB', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(10, 1, 'pmckenzie', 'Corbin Jones', 'cwisozk@example.org', '(689) 489-3315', 'avatars/default.jpg', NULL, '85.76', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', 'QdfXjwD8Gi', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(11, 1, 'spadmin', 'Administrator', 'admin@example.com', '1-540-659-9607', 'avatars/default.jpg', NULL, '166.43', 1, '2025-05-09 07:11:43', '$2y$12$KYRBB94m0j7SOijgZtgNmeF35zC3rYBw.pjkWAVEiE4rd6fLOvfrO', 'rRcThD9iML', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL),
(12, 4, 'customer', 'Test Customer', 'customer@example.com', '925-438-1145', 'avatars/default.jpg', NULL, '768.47', 1, '2025-05-09 07:11:43', '$2y$12$ALGWbd9gcuR5jVnd3gpiZu/P5PMu3lZNTVpkcAYlU5GHpUeuyVtBe', '99gr30bwTC', '2025-05-09 07:11:43', '2025-05-09 07:11:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `variant_attributes`
--

CREATE TABLE `variant_attributes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_attributes`
--

INSERT INTO `variant_attributes` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Vị', NULL, '2025-05-12 01:38:37', '2025-05-12 01:38:37'),
(2, 'Size', NULL, '2025-05-12 01:38:37', '2025-05-12 01:38:37'),
(3, 'Nước chấm', NULL, '2025-05-12 01:38:37', '2025-05-12 01:38:37');

-- --------------------------------------------------------

--
-- Table structure for table `variant_values`
--

CREATE TABLE `variant_values` (
  `id` bigint UNSIGNED NOT NULL,
  `variant_attribute_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_values`
--

INSERT INTO `variant_values` (`id`, `variant_attribute_id`, `product_variant_id`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(2, 2, 1, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(3, 3, 1, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(4, 1, 2, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(5, 2, 2, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(6, 3, 2, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(7, 1, 3, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(8, 2, 3, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(9, 3, 3, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(10, 1, 4, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(11, 2, 4, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(12, 3, 4, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(13, 1, 5, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(14, 2, 5, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(15, 3, 5, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(16, 1, 6, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(17, 2, 6, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(18, 3, 6, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(19, 1, 7, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(20, 2, 7, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(21, 3, 7, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(22, 1, 8, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(23, 2, 8, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(24, 3, 8, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(25, 1, 9, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(26, 2, 9, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(27, 3, 9, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(28, 1, 10, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(29, 2, 10, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(30, 3, 10, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(31, 1, 11, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(32, 2, 11, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(33, 3, 11, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(34, 1, 12, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(35, 2, 12, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(36, 3, 12, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(37, 1, 13, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(38, 2, 13, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(39, 3, 13, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(40, 1, 14, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(41, 2, 14, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(42, 3, 14, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(43, 1, 15, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(44, 2, 15, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(45, 3, 15, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(46, 1, 16, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(47, 2, 16, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(48, 3, 16, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(49, 1, 17, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(50, 2, 17, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(51, 3, 17, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(52, 1, 18, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(53, 2, 18, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(54, 3, 18, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(55, 1, 19, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(56, 2, 19, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(57, 3, 19, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(58, 1, 20, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(59, 2, 20, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(60, 3, 20, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(61, 1, 21, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(62, 2, 21, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(63, 3, 21, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(64, 1, 22, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(65, 2, 22, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(66, 3, 22, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(67, 1, 23, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(68, 2, 23, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(69, 3, 23, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(70, 1, 24, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(71, 2, 24, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(72, 3, 24, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(73, 1, 25, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(74, 2, 25, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(75, 3, 25, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(76, 1, 26, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(77, 2, 26, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(78, 3, 26, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(79, 1, 27, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(80, 2, 27, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(81, 3, 27, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(82, 1, 28, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(83, 2, 28, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(84, 3, 28, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(85, 1, 29, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(86, 2, 29, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(87, 3, 29, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(88, 1, 30, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(89, 2, 30, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(90, 3, 30, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(91, 1, 31, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(92, 2, 31, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(93, 3, 31, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(94, 1, 32, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(95, 2, 32, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(96, 3, 32, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(97, 1, 33, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(98, 2, 33, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(99, 3, 33, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(100, 1, 34, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(101, 2, 34, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(102, 3, 34, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(103, 1, 35, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(104, 2, 35, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(105, 3, 35, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(106, 1, 36, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(107, 2, 36, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(108, 3, 36, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(109, 1, 37, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(110, 2, 37, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(111, 3, 37, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(112, 1, 38, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(113, 2, 38, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(114, 3, 38, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(115, 1, 39, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(116, 2, 39, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(117, 3, 39, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(118, 1, 40, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(119, 2, 40, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(120, 3, 40, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(121, 1, 41, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(122, 2, 41, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(123, 3, 41, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(124, 1, 42, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(125, 2, 42, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(126, 3, 42, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(127, 1, 43, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(128, 2, 43, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(129, 3, 43, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(130, 1, 44, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(131, 2, 44, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(132, 3, 44, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(133, 1, 45, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(134, 2, 45, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(135, 3, 45, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(142, 1, 48, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(143, 2, 48, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(144, 3, 48, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(145, 1, 49, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(146, 2, 49, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(147, 3, 49, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(148, 1, 50, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(149, 2, 50, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(150, 3, 50, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(151, 1, 51, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(152, 2, 51, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(153, 3, 51, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(154, 1, 52, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(155, 2, 52, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(156, 3, 52, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(157, 1, 53, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(158, 2, 53, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(159, 3, 53, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(160, 1, 54, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(161, 2, 54, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(162, 3, 54, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(163, 1, 55, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(164, 2, 55, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(165, 3, 55, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(166, 1, 56, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(167, 2, 56, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(168, 3, 56, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(169, 1, 57, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(170, 2, 57, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(171, 3, 57, 'Không chấm', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(172, 1, 58, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(173, 2, 58, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(174, 3, 58, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(175, 1, 59, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(176, 2, 59, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(177, 3, 59, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(178, 1, 60, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(179, 2, 60, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(180, 3, 60, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(181, 1, 61, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(182, 2, 61, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(183, 3, 61, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(184, 1, 62, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(185, 2, 62, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(186, 3, 62, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(187, 1, 63, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(188, 2, 63, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(189, 3, 63, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(190, 1, 64, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(191, 2, 64, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(192, 3, 64, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(193, 1, 65, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(194, 2, 65, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(195, 3, 65, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(196, 1, 66, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(197, 2, 66, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(198, 3, 66, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(199, 1, 67, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(200, 2, 67, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(201, 3, 67, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(202, 1, 68, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(203, 2, 68, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(204, 3, 68, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(205, 1, 69, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(206, 2, 69, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(207, 3, 69, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(208, 1, 70, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(209, 2, 70, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(210, 3, 70, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(211, 1, 71, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(212, 2, 71, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(213, 3, 71, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(214, 1, 72, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(215, 2, 72, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(216, 3, 72, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(217, 1, 73, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(218, 2, 73, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(219, 3, 73, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(220, 1, 74, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(221, 2, 74, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(222, 3, 74, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(223, 1, 75, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(224, 2, 75, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(225, 3, 75, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(226, 1, 76, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(227, 2, 76, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(228, 3, 76, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(229, 1, 77, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(230, 2, 77, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(231, 3, 77, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(232, 1, 78, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(233, 2, 78, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(234, 3, 78, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(235, 1, 79, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(236, 2, 79, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(237, 3, 79, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(238, 1, 80, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(239, 2, 80, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(240, 3, 80, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(241, 1, 81, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(242, 2, 81, 'Lớn', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(243, 3, 81, 'Mù tạt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(244, 1, 82, 'Cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(245, 2, 82, 'Nhỏ', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(246, 3, 82, 'Tương ớt', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(247, 1, 83, 'Không cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(248, 2, 83, 'Vừa', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(249, 3, 83, 'Tương cà', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(250, 1, 84, 'Siêu cay', '2025-05-12 02:28:00', '2025-05-12 02:28:00'),
(251, 2, 84, 'Lớn', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(252, 3, 84, 'Mù tạt', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(253, 1, 85, 'Không cay', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(254, 2, 85, 'Nhỏ', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(255, 3, 85, 'Không chấm', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(256, 1, 86, 'Không cay', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(257, 2, 86, 'Vừa', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(258, 3, 86, 'Không chấm', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(259, 1, 87, 'Không cay', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(260, 2, 87, 'Lớn', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(261, 3, 87, 'Không chấm', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(262, 1, 88, 'Cay', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(263, 2, 88, 'Nhỏ', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(264, 3, 88, 'Tương ớt', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(265, 1, 89, 'Không cay', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(266, 2, 89, 'Vừa', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(267, 3, 89, 'Tương cà', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(268, 1, 90, 'Siêu cay', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(269, 2, 90, 'Lớn', '2025-06-12 02:28:00', '2025-06-12 02:28:00'),
(270, 3, 90, 'Mù tạt', '2025-06-12 02:28:00', '2025-06-12 02:28:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_sender_id_foreign` (`sender_id`),
  ADD KEY `chat_messages_receiver_id_foreign` (`receiver_id`),
  ADD KEY `chat_messages_branch_id_foreign` (`branch_id`),
  ADD KEY `chat_messages_related_order_id_foreign` (`related_order_id`);

--
-- Indexes for table `customer_ranks`
--
ALTER TABLE `customer_ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_codes_code_unique` (`code`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drivers_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_branch_id_foreign` (`branch_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_driver_id_foreign` (`driver_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`),
  ADD KEY `orders_payment_id_foreign` (`payment_id`),
  ADD KEY `orders_discount_code_id_foreign` (`discount_code_id`);

--
-- Indexes for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_cancellations_order_id_foreign` (`order_id`),
  ADD KEY `order_cancellations_cancelled_by_foreign` (`cancelled_by`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_foreign` (`order_id`),
  ADD KEY `order_details_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_tracking_order_id_foreign` (`order_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_methods_code_unique` (`code`);

--
-- Indexes for table `points_transactions`
--
ALTER TABLE `points_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `points_transactions_user_id_foreign` (`user_id`),
  ADD KEY `points_transactions_order_id_foreign` (`order_id`),
  ADD KEY `points_transactions_review_id_foreign` (`review_id`),
  ADD KEY `points_transactions_created_by_foreign` (`created_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_reviews_user_id_foreign` (`user_id`),
  ADD KEY `product_reviews_product_id_foreign` (`product_id`),
  ADD KEY `product_reviews_order_id_foreign` (`order_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_products_promotion_id_foreign` (`promotion_id`),
  ADD KEY `promotion_products_product_id_foreign` (`product_id`);

--
-- Indexes for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_replies_review_id_foreign` (`review_id`),
  ADD KEY `review_replies_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variant_values`
--
ALTER TABLE `variant_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variant_values_variant_attribute_id_foreign` (`variant_attribute_id`),
  ADD KEY `variant_values_product_variant_id_foreign` (`product_variant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_ranks`
--
ALTER TABLE `customer_ranks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_tracking`
--
ALTER TABLE `order_tracking`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `points_transactions`
--
ALTER TABLE `points_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion_products`
--
ALTER TABLE `promotion_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `variant_values`
--
ALTER TABLE `variant_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `chat_messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chat_messages_related_order_id_foreign` FOREIGN KEY (`related_order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`),
  ADD CONSTRAINT `orders_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  ADD CONSTRAINT `orders_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`);

--
-- Constraints for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  ADD CONSTRAINT `order_cancellations_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `order_cancellations_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

--
-- Constraints for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD CONSTRAINT `order_tracking_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`);

--
-- Constraints for table `points_transactions`
--
ALTER TABLE `points_transactions`
  ADD CONSTRAINT `points_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `points_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `points_transactions_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `product_reviews` (`id`),
  ADD CONSTRAINT `points_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD CONSTRAINT `promotion_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `promotion_products_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`);

--
-- Constraints for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD CONSTRAINT `review_replies_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `product_reviews` (`id`),
  ADD CONSTRAINT `review_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `variant_values`
--
ALTER TABLE `variant_values`
  ADD CONSTRAINT `variant_values_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`),
  ADD CONSTRAINT `variant_values_variant_attribute_id_foreign` FOREIGN KEY (`variant_attribute_id`) REFERENCES `variant_attributes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
