-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2025 at 04:22 PM
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
  `user_id` bigint UNSIGNED DEFAULT NULL,
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
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'homepage',
  `description` text COLLATE utf8mb4_unicode_ci,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `image_path`, `position`, `description`, `link`, `is_active`, `start_at`, `end_at`, `order`, `created_at`, `updated_at`) VALUES
(1, 'Banner mẫu 1', 'https://datnsum2025.s3.ap-southeast-2.amazonaws.com/banners/85uaZecTolXsa4lLxJaBqOzAsMiEtNlqtZf2iiXy.png', 'homepage', 'Banner được tạo tự động từ ảnh mẫu', '/shop/products/6', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', 0, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(2, 'Banner chân trang', 'https://example.com/banners/footer.jpg', 'footers', 'Banner cho phần chân trang', '/footer/info', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(3, 'Banner khuyến mãi', 'https://example.com/banners/promotion.jpg', 'promotions', 'Banner chương trình khuyến mãi', '/promotions', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(4, 'Banner menu', 'https://example.com/banners/menu.jpg', 'menu', 'Banner cho thanh menu chính', '/menu', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(5, 'Banner chi nhánh', 'https://example.com/banners/branch.jpg', 'branch', 'Banner giới thiệu chi nhánh', '/branches', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(6, 'Banner giới thiệu', 'https://example.com/banners/about.jpg', 'abouts', 'Banner phần giới thiệu', '/about-us', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(7, 'Banner hỗ trợ', 'https://example.com/banners/support.jpg', 'supports', 'Banner phần hỗ trợ khách hàng', '/support', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32'),
(8, 'Banner liên hệ', 'https://example.com/banners/contact.jpg', 'contacts', 'Banner phần liên hệ', '/contact', 1, '2025-05-30 21:27:32', '2025-06-06 21:27:32', NULL, '2025-05-30 21:27:32', '2025-05-30 21:27:32');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager_user_id` bigint UNSIGNED DEFAULT NULL,
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

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_code`, `name`, `address`, `phone`, `email`, `manager_user_id`, `latitude`, `longitude`, `opening_hour`, `closing_hour`, `active`, `balance`, `rating`, `reliability_score`, `created_at`, `updated_at`) VALUES
(1, 'HN001', 'Chi nhánh Hà Nội', '123 Đường Láng, Đống Đa, Hà Nội', '0243123456', 'hanoi@devfoods.com', 16, '21.02780000', '105.83420000', '07:30:00', '22:30:00', 1, '5103.00', '4.30', 98, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(2, 'HN002', 'Chi nhánh Hồ Chí Minh', '456 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', '0283456789', 'hcm@devfoods.com', 17, '10.77690000', '106.70090000', '07:00:00', '23:00:00', 1, '5717.00', '4.90', 92, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(3, 'HN003', 'Chi nhánh Đà Nẵng', '789 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', '0236789012', 'danang@devfoods.com', 18, '16.05440000', '108.20220000', '08:00:00', '22:00:00', 1, '9299.00', '4.60', 92, '2025-05-30 21:27:16', '2025-05-30 21:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `branch_images`
--

CREATE TABLE `branch_images` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_stocks`
--

CREATE TABLE `branch_stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_stocks`
--

INSERT INTO `branch_stocks` (`id`, `branch_id`, `product_variant_id`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 28, NULL, NULL),
(2, 1, 2, 65, NULL, NULL),
(3, 1, 3, 5, NULL, NULL),
(4, 1, 4, 4, NULL, NULL),
(5, 1, 5, 18, NULL, NULL),
(6, 1, 6, 77, NULL, NULL),
(7, 1, 7, 15, NULL, NULL),
(8, 1, 8, 53, NULL, NULL),
(9, 1, 9, 42, NULL, NULL),
(10, 1, 10, 20, NULL, NULL),
(11, 1, 11, 80, NULL, NULL),
(12, 1, 12, 13, NULL, NULL),
(13, 1, 13, 6, NULL, NULL),
(14, 1, 14, 75, NULL, NULL),
(15, 1, 15, 68, NULL, NULL),
(16, 1, 16, 4, NULL, NULL),
(17, 1, 17, 100, NULL, NULL),
(18, 1, 18, 28, NULL, NULL),
(19, 1, 19, 10, NULL, NULL),
(20, 1, 20, 33, NULL, NULL),
(21, 1, 21, 83, NULL, NULL),
(22, 1, 22, 71, NULL, NULL),
(23, 1, 23, 76, NULL, NULL),
(24, 1, 24, 63, NULL, NULL),
(25, 1, 25, 18, NULL, NULL),
(26, 1, 26, 73, NULL, NULL),
(27, 1, 27, 25, NULL, NULL),
(28, 1, 28, 75, NULL, NULL),
(29, 1, 29, 51, NULL, NULL),
(30, 1, 30, 98, NULL, NULL),
(31, 1, 31, 89, NULL, NULL),
(32, 1, 32, 13, NULL, NULL),
(33, 1, 33, 32, NULL, NULL),
(34, 1, 34, 75, NULL, NULL),
(35, 1, 35, 16, NULL, NULL),
(36, 1, 36, 95, NULL, NULL),
(37, 1, 37, 34, NULL, NULL),
(38, 1, 38, 89, NULL, NULL),
(39, 1, 39, 71, NULL, NULL),
(40, 1, 40, 25, NULL, NULL),
(41, 1, 41, 10, NULL, NULL),
(42, 1, 42, 20, NULL, NULL),
(43, 1, 43, 51, NULL, NULL),
(44, 1, 44, 39, NULL, NULL),
(45, 1, 45, 17, NULL, NULL),
(46, 1, 46, 36, NULL, NULL),
(47, 1, 47, 35, NULL, NULL),
(48, 1, 48, 48, NULL, NULL),
(49, 1, 49, 22, NULL, NULL),
(50, 1, 50, 41, NULL, NULL),
(51, 1, 51, 23, NULL, NULL),
(52, 1, 52, 49, NULL, NULL),
(53, 1, 53, 21, NULL, NULL),
(54, 1, 54, 99, NULL, NULL),
(55, 1, 55, 22, NULL, NULL),
(56, 1, 56, 86, NULL, NULL),
(57, 1, 57, 44, NULL, NULL),
(58, 1, 58, 37, NULL, NULL),
(59, 1, 59, 43, NULL, NULL),
(60, 1, 60, 88, NULL, NULL),
(61, 1, 61, 23, NULL, NULL),
(62, 1, 62, 59, NULL, NULL),
(63, 1, 63, 73, NULL, NULL),
(64, 1, 64, 3, NULL, NULL),
(65, 1, 65, 31, NULL, NULL),
(66, 1, 66, 66, NULL, NULL),
(67, 1, 67, 93, NULL, NULL),
(68, 1, 68, 32, NULL, NULL),
(69, 1, 69, 94, NULL, NULL),
(70, 1, 70, 12, NULL, NULL),
(71, 1, 71, 90, NULL, NULL),
(72, 1, 72, 44, NULL, NULL),
(73, 1, 73, 11, NULL, NULL),
(74, 1, 74, 29, NULL, NULL),
(75, 1, 75, 46, NULL, NULL),
(76, 1, 76, 1, NULL, NULL),
(77, 1, 77, 98, NULL, NULL),
(78, 1, 78, 8, NULL, NULL),
(79, 1, 79, 0, NULL, NULL),
(80, 1, 80, 68, NULL, NULL),
(81, 1, 81, 20, NULL, NULL),
(82, 1, 82, 48, NULL, NULL),
(83, 1, 83, 4, NULL, NULL),
(84, 1, 84, 61, NULL, NULL),
(85, 1, 85, 74, NULL, NULL),
(86, 1, 86, 76, NULL, NULL),
(87, 1, 87, 59, NULL, NULL),
(88, 1, 88, 20, NULL, NULL),
(89, 1, 89, 13, NULL, NULL),
(90, 1, 90, 81, NULL, NULL),
(91, 1, 91, 47, NULL, NULL),
(92, 1, 92, 6, NULL, NULL),
(93, 1, 93, 42, NULL, NULL),
(94, 1, 94, 65, NULL, NULL),
(95, 1, 95, 26, NULL, NULL),
(96, 1, 96, 17, NULL, NULL),
(97, 1, 97, 16, NULL, NULL),
(98, 1, 98, 72, NULL, NULL),
(99, 1, 99, 21, NULL, NULL),
(100, 1, 100, 95, NULL, NULL),
(101, 1, 101, 6, NULL, NULL),
(102, 1, 102, 99, NULL, NULL),
(103, 1, 103, 76, NULL, NULL),
(104, 1, 104, 86, NULL, NULL),
(105, 1, 105, 49, NULL, NULL),
(106, 1, 106, 18, NULL, NULL),
(107, 1, 107, 32, NULL, NULL),
(108, 1, 108, 62, NULL, NULL),
(109, 1, 109, 38, NULL, NULL),
(110, 1, 110, 92, NULL, NULL),
(111, 1, 111, 16, NULL, NULL),
(112, 1, 112, 40, NULL, NULL),
(113, 1, 113, 25, NULL, NULL),
(114, 1, 114, 58, NULL, NULL),
(115, 1, 115, 54, NULL, NULL),
(116, 1, 116, 65, NULL, NULL),
(117, 1, 117, 8, NULL, NULL),
(118, 1, 118, 56, NULL, NULL),
(119, 1, 119, 0, NULL, NULL),
(120, 1, 120, 49, NULL, NULL),
(121, 1, 121, 97, NULL, NULL),
(122, 1, 122, 18, NULL, NULL),
(123, 1, 123, 78, NULL, NULL),
(124, 1, 124, 26, NULL, NULL),
(125, 1, 125, 22, NULL, NULL),
(126, 1, 126, 23, NULL, NULL),
(127, 1, 127, 44, NULL, NULL),
(128, 1, 128, 31, NULL, NULL),
(129, 1, 129, 37, NULL, NULL),
(130, 1, 130, 90, NULL, NULL),
(131, 1, 131, 37, NULL, NULL),
(132, 1, 132, 36, NULL, NULL),
(133, 1, 133, 90, NULL, NULL),
(134, 1, 134, 79, NULL, NULL),
(135, 1, 135, 30, NULL, NULL),
(136, 1, 136, 5, NULL, NULL),
(137, 1, 137, 50, NULL, NULL),
(138, 1, 138, 97, NULL, NULL),
(139, 1, 139, 11, NULL, NULL),
(140, 1, 140, 34, NULL, NULL),
(141, 1, 141, 90, NULL, NULL),
(142, 1, 142, 87, NULL, NULL),
(143, 1, 143, 15, NULL, NULL),
(144, 1, 144, 38, NULL, NULL),
(145, 1, 145, 45, NULL, NULL),
(146, 1, 146, 22, NULL, NULL),
(147, 1, 147, 35, NULL, NULL),
(148, 1, 148, 0, NULL, NULL),
(149, 1, 149, 44, NULL, NULL),
(150, 1, 150, 100, NULL, NULL),
(151, 1, 151, 7, NULL, NULL),
(152, 1, 152, 16, NULL, NULL),
(153, 1, 153, 47, NULL, NULL),
(154, 1, 154, 1, NULL, NULL),
(155, 1, 155, 8, NULL, NULL),
(156, 1, 156, 97, NULL, NULL),
(157, 1, 157, 94, NULL, NULL),
(158, 1, 158, 61, NULL, NULL),
(159, 1, 159, 61, NULL, NULL),
(160, 1, 160, 70, NULL, NULL),
(161, 1, 161, 88, NULL, NULL),
(162, 1, 162, 88, NULL, NULL),
(163, 1, 163, 12, NULL, NULL),
(164, 1, 164, 55, NULL, NULL),
(165, 1, 165, 17, NULL, NULL),
(166, 1, 166, 46, NULL, NULL),
(167, 1, 167, 74, NULL, NULL),
(168, 1, 168, 42, NULL, NULL),
(169, 1, 169, 25, NULL, NULL),
(170, 1, 170, 4, NULL, NULL),
(171, 1, 171, 61, NULL, NULL),
(172, 1, 172, 58, NULL, NULL),
(173, 1, 173, 93, NULL, NULL),
(174, 1, 174, 95, NULL, NULL),
(175, 1, 175, 53, NULL, NULL),
(176, 1, 176, 93, NULL, NULL),
(177, 1, 177, 92, NULL, NULL),
(178, 1, 178, 65, NULL, NULL),
(179, 1, 179, 97, NULL, NULL),
(180, 1, 180, 25, NULL, NULL),
(181, 1, 181, 83, NULL, NULL),
(182, 1, 182, 72, NULL, NULL),
(183, 1, 183, 55, NULL, NULL),
(184, 1, 184, 73, NULL, NULL),
(185, 1, 185, 70, NULL, NULL),
(186, 1, 186, 42, NULL, NULL),
(187, 1, 187, 80, NULL, NULL),
(188, 1, 188, 60, NULL, NULL),
(189, 1, 189, 48, NULL, NULL),
(190, 1, 190, 67, NULL, NULL),
(191, 1, 191, 15, NULL, NULL),
(192, 1, 192, 35, NULL, NULL),
(193, 1, 193, 50, NULL, NULL),
(194, 1, 194, 56, NULL, NULL),
(195, 1, 195, 10, NULL, NULL),
(196, 1, 196, 40, NULL, NULL),
(197, 1, 197, 98, NULL, NULL),
(198, 1, 198, 60, NULL, NULL),
(199, 1, 199, 10, NULL, NULL),
(200, 1, 200, 46, NULL, NULL),
(201, 1, 201, 88, NULL, NULL),
(202, 1, 202, 31, NULL, NULL),
(203, 1, 203, 54, NULL, NULL),
(204, 1, 204, 96, NULL, NULL),
(205, 1, 205, 17, NULL, NULL),
(206, 1, 206, 3, NULL, NULL),
(207, 1, 207, 63, NULL, NULL),
(208, 1, 208, 92, NULL, NULL),
(209, 1, 209, 59, NULL, NULL),
(210, 1, 210, 31, NULL, NULL),
(211, 1, 211, 38, NULL, NULL),
(212, 1, 212, 8, NULL, NULL),
(213, 1, 213, 39, NULL, NULL),
(214, 1, 214, 68, NULL, NULL),
(215, 1, 215, 69, NULL, NULL),
(216, 1, 216, 16, NULL, NULL),
(217, 1, 217, 57, NULL, NULL),
(218, 1, 218, 84, NULL, NULL),
(219, 1, 219, 90, NULL, NULL),
(220, 1, 220, 15, NULL, NULL),
(221, 1, 221, 100, NULL, NULL),
(222, 1, 222, 54, NULL, NULL),
(223, 1, 223, 37, NULL, NULL),
(224, 1, 224, 49, NULL, NULL),
(225, 1, 225, 10, NULL, NULL),
(226, 1, 226, 23, NULL, NULL),
(227, 1, 227, 13, NULL, NULL),
(228, 1, 228, 74, NULL, NULL),
(229, 1, 229, 85, NULL, NULL),
(230, 1, 230, 78, NULL, NULL),
(231, 1, 231, 89, NULL, NULL),
(232, 1, 232, 80, NULL, NULL),
(233, 1, 233, 59, NULL, NULL),
(234, 1, 234, 28, NULL, NULL),
(235, 1, 235, 83, NULL, NULL),
(236, 1, 236, 31, NULL, NULL),
(237, 1, 237, 73, NULL, NULL),
(238, 1, 238, 21, NULL, NULL),
(239, 1, 239, 78, NULL, NULL),
(240, 1, 240, 28, NULL, NULL),
(241, 1, 241, 39, NULL, NULL),
(242, 1, 242, 60, NULL, NULL),
(243, 1, 243, 76, NULL, NULL),
(244, 1, 244, 50, NULL, NULL),
(245, 1, 245, 85, NULL, NULL),
(246, 1, 246, 17, NULL, NULL),
(247, 1, 247, 18, NULL, NULL),
(248, 1, 248, 64, NULL, NULL),
(249, 1, 249, 55, NULL, NULL),
(250, 1, 250, 100, NULL, NULL),
(251, 1, 251, 20, NULL, NULL),
(252, 1, 252, 43, NULL, NULL),
(253, 1, 253, 57, NULL, NULL),
(254, 1, 254, 60, NULL, NULL),
(255, 1, 255, 41, NULL, NULL),
(256, 1, 256, 3, NULL, NULL),
(257, 1, 257, 13, NULL, NULL),
(258, 1, 258, 33, NULL, NULL),
(259, 1, 259, 60, NULL, NULL),
(260, 1, 260, 97, NULL, NULL),
(261, 1, 261, 79, NULL, NULL),
(262, 1, 262, 94, NULL, NULL),
(263, 1, 263, 20, NULL, NULL),
(264, 1, 264, 25, NULL, NULL),
(265, 1, 265, 3, NULL, NULL),
(266, 1, 266, 64, NULL, NULL),
(267, 1, 267, 1, NULL, NULL),
(268, 1, 268, 12, NULL, NULL),
(269, 1, 269, 39, NULL, NULL),
(270, 1, 270, 34, NULL, NULL),
(271, 1, 271, 73, NULL, NULL),
(272, 1, 272, 9, NULL, NULL),
(273, 1, 273, 1, NULL, NULL),
(274, 1, 274, 0, NULL, NULL),
(275, 1, 275, 34, NULL, NULL),
(276, 1, 276, 95, NULL, NULL),
(277, 1, 277, 96, NULL, NULL),
(278, 1, 278, 53, NULL, NULL),
(279, 1, 279, 68, NULL, NULL),
(280, 1, 280, 72, NULL, NULL),
(281, 1, 281, 81, NULL, NULL),
(282, 1, 282, 22, NULL, NULL),
(283, 1, 283, 64, NULL, NULL),
(284, 1, 284, 60, NULL, NULL),
(285, 1, 285, 10, NULL, NULL),
(286, 1, 286, 16, NULL, NULL),
(287, 1, 287, 73, NULL, NULL),
(288, 1, 288, 17, NULL, NULL),
(289, 1, 289, 42, NULL, NULL),
(290, 1, 290, 47, NULL, NULL),
(291, 1, 291, 32, NULL, NULL),
(292, 1, 292, 92, NULL, NULL),
(293, 1, 293, 95, NULL, NULL),
(294, 1, 294, 76, NULL, NULL),
(295, 1, 295, 85, NULL, NULL),
(296, 1, 296, 82, NULL, NULL),
(297, 1, 297, 28, NULL, NULL),
(298, 1, 298, 26, NULL, NULL),
(299, 1, 299, 87, NULL, NULL),
(300, 1, 300, 58, NULL, NULL),
(301, 1, 301, 79, NULL, NULL),
(302, 1, 302, 18, NULL, NULL),
(303, 1, 303, 53, NULL, NULL),
(304, 1, 304, 96, NULL, NULL),
(305, 1, 305, 36, NULL, NULL),
(306, 1, 306, 49, NULL, NULL),
(307, 1, 307, 6, NULL, NULL),
(308, 1, 308, 97, NULL, NULL),
(309, 1, 309, 68, NULL, NULL),
(310, 1, 310, 0, NULL, NULL),
(311, 1, 311, 21, NULL, NULL),
(312, 1, 312, 52, NULL, NULL),
(313, 1, 313, 63, NULL, NULL),
(314, 1, 314, 67, NULL, NULL),
(315, 1, 315, 66, NULL, NULL),
(316, 1, 316, 100, NULL, NULL),
(317, 1, 317, 28, NULL, NULL),
(318, 1, 318, 52, NULL, NULL),
(319, 1, 319, 20, NULL, NULL),
(320, 1, 320, 75, NULL, NULL),
(321, 1, 321, 77, NULL, NULL),
(322, 1, 322, 42, NULL, NULL),
(323, 1, 323, 74, NULL, NULL),
(324, 1, 324, 74, NULL, NULL),
(325, 1, 325, 65, NULL, NULL),
(326, 1, 326, 67, NULL, NULL),
(327, 1, 327, 55, NULL, NULL),
(328, 1, 328, 40, NULL, NULL),
(329, 1, 329, 67, NULL, NULL),
(330, 1, 330, 33, NULL, NULL),
(331, 1, 331, 16, NULL, NULL),
(332, 1, 332, 32, NULL, NULL),
(333, 1, 333, 76, NULL, NULL),
(334, 1, 334, 47, NULL, NULL),
(335, 1, 335, 30, NULL, NULL),
(336, 1, 336, 70, NULL, NULL),
(337, 1, 337, 67, NULL, NULL),
(338, 1, 338, 42, NULL, NULL),
(339, 1, 339, 23, NULL, NULL),
(340, 1, 340, 51, NULL, NULL),
(341, 1, 341, 85, NULL, NULL),
(342, 1, 342, 37, NULL, NULL),
(343, 1, 343, 70, NULL, NULL),
(344, 1, 344, 48, NULL, NULL),
(345, 1, 345, 73, NULL, NULL),
(346, 1, 346, 52, NULL, NULL),
(347, 1, 347, 54, NULL, NULL),
(348, 1, 348, 55, NULL, NULL),
(349, 1, 349, 25, NULL, NULL),
(350, 1, 350, 81, NULL, NULL),
(351, 1, 351, 81, NULL, NULL),
(352, 1, 352, 17, NULL, NULL),
(353, 1, 353, 41, NULL, NULL),
(354, 1, 354, 100, NULL, NULL),
(355, 1, 355, 53, NULL, NULL),
(356, 1, 356, 86, NULL, NULL),
(357, 1, 357, 50, NULL, NULL),
(358, 1, 358, 46, NULL, NULL),
(359, 1, 359, 71, NULL, NULL),
(360, 1, 360, 45, NULL, NULL),
(361, 1, 361, 5, NULL, NULL),
(362, 1, 362, 79, NULL, NULL),
(363, 1, 363, 16, NULL, NULL),
(364, 1, 364, 84, NULL, NULL),
(365, 1, 365, 94, NULL, NULL),
(366, 1, 366, 88, NULL, NULL),
(367, 1, 367, 45, NULL, NULL),
(368, 1, 368, 88, NULL, NULL),
(369, 1, 369, 81, NULL, NULL),
(370, 1, 370, 13, NULL, NULL),
(371, 1, 371, 85, NULL, NULL),
(372, 1, 372, 68, NULL, NULL),
(373, 1, 373, 77, NULL, NULL),
(374, 1, 374, 26, NULL, NULL),
(375, 1, 375, 20, NULL, NULL),
(376, 1, 376, 59, NULL, NULL),
(377, 1, 377, 37, NULL, NULL),
(378, 1, 378, 58, NULL, NULL),
(379, 1, 379, 75, NULL, NULL),
(380, 1, 380, 55, NULL, NULL),
(381, 1, 381, 51, NULL, NULL),
(382, 1, 382, 92, NULL, NULL),
(383, 1, 383, 19, NULL, NULL),
(384, 1, 384, 40, NULL, NULL),
(385, 1, 385, 53, NULL, NULL),
(386, 1, 386, 20, NULL, NULL),
(387, 1, 387, 27, NULL, NULL),
(388, 1, 388, 40, NULL, NULL),
(389, 1, 389, 19, NULL, NULL),
(390, 1, 390, 100, NULL, NULL),
(391, 1, 391, 3, NULL, NULL),
(392, 1, 392, 94, NULL, NULL),
(393, 1, 393, 32, NULL, NULL),
(394, 1, 394, 74, NULL, NULL),
(395, 1, 395, 42, NULL, NULL),
(396, 1, 396, 65, NULL, NULL),
(397, 1, 397, 74, NULL, NULL),
(398, 1, 398, 74, NULL, NULL),
(399, 1, 399, 8, NULL, NULL),
(400, 1, 400, 45, NULL, NULL),
(401, 1, 401, 21, NULL, NULL),
(402, 1, 402, 73, NULL, NULL),
(403, 1, 403, 0, NULL, NULL),
(404, 1, 404, 25, NULL, NULL),
(405, 1, 405, 62, NULL, NULL),
(406, 1, 406, 40, NULL, NULL),
(407, 1, 407, 79, NULL, NULL),
(408, 1, 408, 59, NULL, NULL),
(409, 1, 409, 35, NULL, NULL),
(410, 1, 410, 69, NULL, NULL),
(411, 1, 411, 40, NULL, NULL),
(412, 1, 412, 34, NULL, NULL),
(413, 1, 413, 10, NULL, NULL),
(414, 1, 414, 30, NULL, NULL),
(415, 1, 415, 7, NULL, NULL),
(416, 1, 416, 6, NULL, NULL),
(417, 1, 417, 26, NULL, NULL),
(418, 1, 418, 2, NULL, NULL),
(419, 1, 419, 51, NULL, NULL),
(420, 1, 420, 27, NULL, NULL),
(421, 1, 421, 95, NULL, NULL),
(422, 1, 422, 69, NULL, NULL),
(423, 1, 423, 64, NULL, NULL),
(424, 1, 424, 0, NULL, NULL),
(425, 1, 425, 0, NULL, NULL),
(426, 1, 426, 59, NULL, NULL),
(427, 1, 427, 43, NULL, NULL),
(428, 1, 428, 75, NULL, NULL),
(429, 1, 429, 40, NULL, NULL),
(430, 1, 430, 76, NULL, NULL),
(431, 1, 431, 73, NULL, NULL),
(432, 1, 432, 42, NULL, NULL),
(433, 1, 433, 48, NULL, NULL),
(434, 1, 434, 66, NULL, NULL),
(435, 1, 435, 83, NULL, NULL),
(436, 1, 436, 82, NULL, NULL),
(437, 1, 437, 18, NULL, NULL),
(438, 1, 438, 1, NULL, NULL),
(439, 1, 439, 96, NULL, NULL),
(440, 1, 440, 65, NULL, NULL),
(441, 1, 441, 1, NULL, NULL),
(442, 1, 442, 60, NULL, NULL),
(443, 1, 443, 42, NULL, NULL),
(444, 1, 444, 54, NULL, NULL),
(445, 1, 445, 35, NULL, NULL),
(446, 1, 446, 15, NULL, NULL),
(447, 1, 447, 45, NULL, NULL),
(448, 1, 448, 0, NULL, NULL),
(449, 1, 449, 37, NULL, NULL),
(450, 1, 450, 79, NULL, NULL),
(451, 1, 451, 25, NULL, NULL),
(452, 1, 452, 87, NULL, NULL),
(453, 1, 453, 5, NULL, NULL),
(454, 1, 454, 1, NULL, NULL),
(455, 1, 455, 20, NULL, NULL),
(456, 1, 456, 69, NULL, NULL),
(457, 1, 457, 46, NULL, NULL),
(458, 1, 458, 66, NULL, NULL),
(459, 1, 459, 43, NULL, NULL),
(460, 1, 460, 51, NULL, NULL),
(461, 1, 461, 93, NULL, NULL),
(462, 1, 462, 69, NULL, NULL),
(463, 1, 463, 27, NULL, NULL),
(464, 1, 464, 74, NULL, NULL),
(465, 1, 465, 49, NULL, NULL),
(466, 1, 466, 100, NULL, NULL),
(467, 1, 467, 63, NULL, NULL),
(468, 1, 468, 10, NULL, NULL),
(469, 1, 469, 19, NULL, NULL),
(470, 1, 470, 92, NULL, NULL),
(471, 1, 471, 21, NULL, NULL),
(472, 1, 472, 81, NULL, NULL),
(473, 1, 473, 21, NULL, NULL),
(474, 1, 474, 12, NULL, NULL),
(475, 1, 475, 3, NULL, NULL),
(476, 1, 476, 83, NULL, NULL),
(477, 1, 477, 50, NULL, NULL),
(478, 1, 478, 76, NULL, NULL),
(479, 1, 479, 88, NULL, NULL),
(480, 1, 480, 50, NULL, NULL),
(481, 1, 481, 63, NULL, NULL),
(482, 1, 482, 24, NULL, NULL),
(483, 1, 483, 5, NULL, NULL),
(484, 1, 484, 15, NULL, NULL),
(485, 1, 485, 41, NULL, NULL),
(486, 1, 486, 50, NULL, NULL),
(487, 1, 487, 20, NULL, NULL),
(488, 1, 488, 12, NULL, NULL),
(489, 1, 489, 65, NULL, NULL),
(490, 1, 490, 18, NULL, NULL),
(491, 1, 491, 4, NULL, NULL),
(492, 1, 492, 14, NULL, NULL),
(493, 1, 493, 84, NULL, NULL),
(494, 1, 494, 61, NULL, NULL),
(495, 1, 495, 38, NULL, NULL),
(496, 1, 496, 53, NULL, NULL),
(497, 1, 497, 52, NULL, NULL),
(498, 1, 498, 76, NULL, NULL),
(499, 1, 499, 91, NULL, NULL),
(500, 1, 500, 64, NULL, NULL),
(501, 1, 501, 97, NULL, NULL),
(502, 1, 502, 50, NULL, NULL),
(503, 1, 503, 31, NULL, NULL),
(504, 1, 504, 75, NULL, NULL),
(505, 1, 505, 28, NULL, NULL),
(506, 1, 506, 19, NULL, NULL),
(507, 1, 507, 33, NULL, NULL),
(508, 1, 508, 51, NULL, NULL),
(509, 1, 509, 25, NULL, NULL),
(510, 1, 510, 49, NULL, NULL),
(511, 1, 511, 56, NULL, NULL),
(512, 1, 512, 76, NULL, NULL),
(513, 1, 513, 43, NULL, NULL),
(514, 1, 514, 34, NULL, NULL),
(515, 1, 515, 52, NULL, NULL),
(516, 1, 516, 19, NULL, NULL),
(517, 1, 517, 76, NULL, NULL),
(518, 1, 518, 93, NULL, NULL),
(519, 1, 519, 77, NULL, NULL),
(520, 1, 520, 66, NULL, NULL),
(521, 1, 521, 63, NULL, NULL),
(522, 1, 522, 64, NULL, NULL),
(523, 1, 523, 62, NULL, NULL),
(524, 1, 524, 48, NULL, NULL),
(525, 1, 525, 53, NULL, NULL),
(526, 1, 526, 66, NULL, NULL),
(527, 1, 527, 46, NULL, NULL),
(528, 1, 528, 64, NULL, NULL),
(529, 1, 529, 64, NULL, NULL),
(530, 1, 530, 78, NULL, NULL),
(531, 1, 531, 80, NULL, NULL),
(532, 1, 532, 60, NULL, NULL),
(533, 1, 533, 39, NULL, NULL),
(534, 1, 534, 87, NULL, NULL),
(535, 1, 535, 24, NULL, NULL),
(536, 1, 536, 52, NULL, NULL),
(537, 1, 537, 87, NULL, NULL),
(538, 1, 538, 48, NULL, NULL),
(539, 1, 539, 12, NULL, NULL),
(540, 1, 540, 82, NULL, NULL),
(541, 1, 541, 92, NULL, NULL),
(542, 1, 542, 93, NULL, NULL),
(543, 1, 543, 93, NULL, NULL),
(544, 1, 544, 71, NULL, NULL),
(545, 1, 545, 89, NULL, NULL),
(546, 1, 546, 77, NULL, NULL),
(547, 1, 547, 76, NULL, NULL),
(548, 1, 548, 57, NULL, NULL),
(549, 1, 549, 70, NULL, NULL),
(550, 1, 550, 76, NULL, NULL),
(551, 1, 551, 40, NULL, NULL),
(552, 1, 552, 74, NULL, NULL),
(553, 1, 553, 24, NULL, NULL),
(554, 1, 554, 26, NULL, NULL),
(555, 1, 555, 39, NULL, NULL),
(556, 1, 556, 51, NULL, NULL),
(557, 1, 557, 61, NULL, NULL),
(558, 1, 558, 92, NULL, NULL),
(559, 1, 559, 63, NULL, NULL),
(560, 1, 560, 98, NULL, NULL),
(561, 1, 561, 68, NULL, NULL),
(562, 1, 562, 67, NULL, NULL),
(563, 1, 563, 4, NULL, NULL),
(564, 1, 564, 8, NULL, NULL),
(565, 1, 565, 91, NULL, NULL),
(566, 1, 566, 99, NULL, NULL),
(567, 1, 567, 80, NULL, NULL),
(568, 1, 568, 23, NULL, NULL),
(569, 1, 569, 61, NULL, NULL),
(570, 1, 570, 7, NULL, NULL),
(571, 1, 571, 55, NULL, NULL),
(572, 1, 572, 57, NULL, NULL),
(573, 1, 573, 72, NULL, NULL),
(574, 1, 574, 55, NULL, NULL),
(575, 1, 575, 35, NULL, NULL),
(576, 1, 576, 15, NULL, NULL),
(577, 1, 577, 85, NULL, NULL),
(578, 1, 578, 0, NULL, NULL),
(579, 1, 579, 29, NULL, NULL),
(580, 1, 580, 30, NULL, NULL),
(581, 1, 581, 53, NULL, NULL),
(582, 1, 582, 41, NULL, NULL),
(583, 1, 583, 7, NULL, NULL),
(584, 1, 584, 45, NULL, NULL),
(585, 1, 585, 19, NULL, NULL),
(586, 1, 586, 78, NULL, NULL),
(587, 1, 587, 76, NULL, NULL),
(588, 1, 588, 45, NULL, NULL),
(589, 1, 589, 67, NULL, NULL),
(590, 1, 590, 78, NULL, NULL),
(591, 1, 591, 47, NULL, NULL),
(592, 1, 592, 16, NULL, NULL),
(593, 1, 593, 90, NULL, NULL),
(594, 1, 594, 84, NULL, NULL),
(595, 1, 595, 15, NULL, NULL),
(596, 1, 596, 94, NULL, NULL),
(597, 1, 597, 32, NULL, NULL),
(598, 1, 598, 31, NULL, NULL),
(599, 1, 599, 12, NULL, NULL),
(600, 1, 600, 61, NULL, NULL),
(601, 1, 601, 85, NULL, NULL),
(602, 1, 602, 12, NULL, NULL),
(603, 1, 603, 71, NULL, NULL),
(604, 1, 604, 84, NULL, NULL),
(605, 1, 605, 9, NULL, NULL),
(606, 1, 606, 87, NULL, NULL),
(607, 1, 607, 7, NULL, NULL),
(608, 1, 608, 15, NULL, NULL),
(609, 1, 609, 96, NULL, NULL),
(610, 1, 610, 14, NULL, NULL),
(611, 1, 611, 60, NULL, NULL),
(612, 1, 612, 50, NULL, NULL),
(613, 1, 613, 19, NULL, NULL),
(614, 1, 614, 39, NULL, NULL),
(615, 1, 615, 29, NULL, NULL),
(616, 1, 616, 1, NULL, NULL),
(617, 1, 617, 11, NULL, NULL),
(618, 1, 618, 2, NULL, NULL),
(619, 1, 619, 64, NULL, NULL),
(620, 1, 620, 30, NULL, NULL),
(621, 1, 621, 3, NULL, NULL),
(622, 1, 622, 60, NULL, NULL),
(623, 1, 623, 84, NULL, NULL),
(624, 1, 624, 40, NULL, NULL),
(625, 1, 625, 40, NULL, NULL),
(626, 1, 626, 10, NULL, NULL),
(627, 1, 627, 25, NULL, NULL),
(628, 1, 628, 63, NULL, NULL),
(629, 1, 629, 64, NULL, NULL),
(630, 1, 630, 63, NULL, NULL),
(631, 1, 631, 36, NULL, NULL),
(632, 1, 632, 34, NULL, NULL),
(633, 1, 633, 78, NULL, NULL),
(634, 1, 634, 85, NULL, NULL),
(635, 1, 635, 14, NULL, NULL),
(636, 1, 636, 50, NULL, NULL),
(637, 1, 637, 98, NULL, NULL),
(638, 1, 638, 11, NULL, NULL),
(639, 1, 639, 75, NULL, NULL),
(640, 1, 640, 79, NULL, NULL),
(641, 1, 641, 48, NULL, NULL),
(642, 1, 642, 65, NULL, NULL),
(643, 1, 643, 9, NULL, NULL),
(644, 1, 644, 33, NULL, NULL),
(645, 1, 645, 62, NULL, NULL),
(646, 1, 646, 26, NULL, NULL),
(647, 1, 647, 0, NULL, NULL),
(648, 1, 648, 15, NULL, NULL),
(649, 1, 649, 60, NULL, NULL),
(650, 1, 650, 70, NULL, NULL),
(651, 1, 651, 11, NULL, NULL),
(652, 1, 652, 51, NULL, NULL),
(653, 1, 653, 19, NULL, NULL),
(654, 1, 654, 73, NULL, NULL),
(655, 1, 655, 31, NULL, NULL),
(656, 1, 656, 5, NULL, NULL),
(657, 1, 657, 62, NULL, NULL),
(658, 1, 658, 49, NULL, NULL),
(659, 1, 659, 10, NULL, NULL),
(660, 1, 660, 5, NULL, NULL),
(661, 1, 661, 80, NULL, NULL),
(662, 1, 662, 94, NULL, NULL),
(663, 1, 663, 95, NULL, NULL),
(664, 1, 664, 4, NULL, NULL),
(665, 1, 665, 36, NULL, NULL),
(666, 1, 666, 54, NULL, NULL),
(667, 1, 667, 69, NULL, NULL),
(668, 1, 668, 48, NULL, NULL),
(669, 1, 669, 24, NULL, NULL),
(670, 1, 670, 64, NULL, NULL),
(671, 1, 671, 79, NULL, NULL),
(672, 1, 672, 56, NULL, NULL),
(673, 1, 673, 12, NULL, NULL),
(674, 1, 674, 87, NULL, NULL),
(675, 1, 675, 87, NULL, NULL),
(676, 1, 676, 57, NULL, NULL),
(677, 1, 677, 99, NULL, NULL),
(678, 1, 678, 49, NULL, NULL),
(679, 1, 679, 89, NULL, NULL),
(680, 1, 680, 72, NULL, NULL),
(681, 1, 681, 10, NULL, NULL),
(682, 1, 682, 75, NULL, NULL),
(683, 1, 683, 26, NULL, NULL),
(684, 1, 684, 5, NULL, NULL),
(685, 1, 685, 96, NULL, NULL),
(686, 1, 686, 80, NULL, NULL),
(687, 1, 687, 75, NULL, NULL),
(688, 1, 688, 51, NULL, NULL),
(689, 1, 689, 55, NULL, NULL),
(690, 1, 690, 70, NULL, NULL),
(691, 1, 691, 71, NULL, NULL),
(692, 1, 692, 19, NULL, NULL),
(693, 1, 693, 85, NULL, NULL),
(694, 1, 694, 9, NULL, NULL),
(695, 1, 695, 43, NULL, NULL),
(696, 1, 696, 43, NULL, NULL),
(697, 1, 697, 42, NULL, NULL),
(698, 1, 698, 47, NULL, NULL),
(699, 1, 699, 35, NULL, NULL),
(700, 1, 700, 44, NULL, NULL),
(701, 1, 701, 25, NULL, NULL),
(702, 1, 702, 5, NULL, NULL),
(703, 1, 703, 84, NULL, NULL),
(704, 1, 704, 8, NULL, NULL),
(705, 1, 705, 100, NULL, NULL),
(706, 1, 706, 72, NULL, NULL),
(707, 1, 707, 41, NULL, NULL),
(708, 1, 708, 85, NULL, NULL),
(709, 1, 709, 64, NULL, NULL),
(710, 1, 710, 65, NULL, NULL),
(711, 1, 711, 80, NULL, NULL),
(712, 1, 712, 72, NULL, NULL),
(713, 1, 713, 35, NULL, NULL),
(714, 1, 714, 88, NULL, NULL),
(715, 1, 715, 46, NULL, NULL),
(716, 1, 716, 6, NULL, NULL),
(717, 1, 717, 34, NULL, NULL),
(718, 1, 718, 85, NULL, NULL),
(719, 1, 719, 66, NULL, NULL),
(720, 1, 720, 66, NULL, NULL),
(721, 1, 721, 74, NULL, NULL),
(722, 1, 722, 91, NULL, NULL),
(723, 1, 723, 2, NULL, NULL),
(724, 1, 724, 38, NULL, NULL),
(725, 1, 725, 75, NULL, NULL),
(726, 1, 726, 97, NULL, NULL),
(727, 1, 727, 89, NULL, NULL),
(728, 1, 728, 49, NULL, NULL),
(729, 1, 729, 69, NULL, NULL),
(730, 1, 730, 55, NULL, NULL),
(731, 1, 731, 40, NULL, NULL),
(732, 1, 732, 15, NULL, NULL),
(733, 1, 733, 90, NULL, NULL),
(734, 1, 734, 76, NULL, NULL),
(735, 1, 735, 52, NULL, NULL),
(736, 1, 736, 97, NULL, NULL),
(737, 1, 737, 23, NULL, NULL),
(738, 1, 738, 21, NULL, NULL),
(739, 1, 739, 57, NULL, NULL),
(740, 1, 740, 39, NULL, NULL),
(741, 1, 741, 99, NULL, NULL),
(742, 1, 742, 41, NULL, NULL),
(743, 1, 743, 63, NULL, NULL),
(744, 1, 744, 54, NULL, NULL),
(745, 1, 745, 86, NULL, NULL),
(746, 1, 746, 100, NULL, NULL),
(747, 1, 747, 88, NULL, NULL),
(748, 1, 748, 19, NULL, NULL),
(749, 1, 749, 34, NULL, NULL),
(750, 1, 750, 72, NULL, NULL),
(751, 1, 751, 93, NULL, NULL),
(752, 1, 752, 68, NULL, NULL),
(753, 1, 753, 1, NULL, NULL),
(754, 1, 754, 77, NULL, NULL),
(755, 1, 755, 67, NULL, NULL),
(756, 1, 756, 38, NULL, NULL),
(757, 1, 757, 62, NULL, NULL),
(758, 1, 758, 8, NULL, NULL),
(759, 1, 759, 46, NULL, NULL),
(760, 1, 760, 68, NULL, NULL),
(761, 1, 761, 14, NULL, NULL),
(762, 1, 762, 42, NULL, NULL),
(763, 1, 763, 19, NULL, NULL),
(764, 1, 764, 75, NULL, NULL),
(765, 1, 765, 35, NULL, NULL),
(766, 1, 766, 68, NULL, NULL),
(767, 1, 767, 48, NULL, NULL),
(768, 1, 768, 56, NULL, NULL),
(769, 1, 769, 95, NULL, NULL),
(770, 1, 770, 60, NULL, NULL),
(771, 1, 771, 47, NULL, NULL),
(772, 1, 772, 64, NULL, NULL),
(773, 1, 773, 42, NULL, NULL),
(774, 1, 774, 30, NULL, NULL),
(775, 1, 775, 88, NULL, NULL),
(776, 1, 776, 13, NULL, NULL),
(777, 1, 777, 42, NULL, NULL),
(778, 1, 778, 83, NULL, NULL),
(779, 1, 779, 58, NULL, NULL),
(780, 1, 780, 48, NULL, NULL),
(781, 1, 781, 36, NULL, NULL),
(782, 1, 782, 54, NULL, NULL),
(783, 1, 783, 97, NULL, NULL),
(784, 1, 784, 35, NULL, NULL),
(785, 1, 785, 40, NULL, NULL),
(786, 1, 786, 94, NULL, NULL),
(787, 1, 787, 71, NULL, NULL),
(788, 1, 788, 11, NULL, NULL),
(789, 1, 789, 99, NULL, NULL),
(790, 1, 790, 48, NULL, NULL),
(791, 1, 791, 7, NULL, NULL),
(792, 1, 792, 13, NULL, NULL),
(793, 1, 793, 61, NULL, NULL),
(794, 1, 794, 3, NULL, NULL),
(795, 1, 795, 51, NULL, NULL),
(796, 1, 796, 95, NULL, NULL),
(797, 1, 797, 22, NULL, NULL),
(798, 1, 798, 46, NULL, NULL),
(799, 1, 799, 52, NULL, NULL),
(800, 1, 800, 47, NULL, NULL),
(801, 1, 801, 16, NULL, NULL),
(802, 1, 802, 27, NULL, NULL),
(803, 1, 803, 99, NULL, NULL),
(804, 1, 804, 87, NULL, NULL),
(805, 1, 805, 46, NULL, NULL),
(806, 1, 806, 63, NULL, NULL),
(807, 1, 807, 19, NULL, NULL),
(808, 1, 808, 45, NULL, NULL),
(809, 1, 809, 90, NULL, NULL),
(810, 1, 810, 95, NULL, NULL),
(811, 2, 1, 39, NULL, NULL),
(812, 2, 2, 66, NULL, NULL),
(813, 2, 3, 9, NULL, NULL),
(814, 2, 4, 49, NULL, NULL),
(815, 2, 5, 92, NULL, NULL),
(816, 2, 6, 31, NULL, NULL),
(817, 2, 7, 76, NULL, NULL),
(818, 2, 8, 55, NULL, NULL),
(819, 2, 9, 74, NULL, NULL),
(820, 2, 10, 17, NULL, NULL),
(821, 2, 11, 100, NULL, NULL),
(822, 2, 12, 89, NULL, NULL),
(823, 2, 13, 12, NULL, NULL),
(824, 2, 14, 37, NULL, NULL),
(825, 2, 15, 26, NULL, NULL),
(826, 2, 16, 87, NULL, NULL),
(827, 2, 17, 89, NULL, NULL),
(828, 2, 18, 90, NULL, NULL),
(829, 2, 19, 46, NULL, NULL),
(830, 2, 20, 18, NULL, NULL),
(831, 2, 21, 74, NULL, NULL),
(832, 2, 22, 91, NULL, NULL),
(833, 2, 23, 74, NULL, NULL),
(834, 2, 24, 79, NULL, NULL),
(835, 2, 25, 87, NULL, NULL),
(836, 2, 26, 9, NULL, NULL),
(837, 2, 27, 71, NULL, NULL),
(838, 2, 28, 31, NULL, NULL),
(839, 2, 29, 79, NULL, NULL),
(840, 2, 30, 13, NULL, NULL),
(841, 2, 31, 33, NULL, NULL),
(842, 2, 32, 90, NULL, NULL),
(843, 2, 33, 59, NULL, NULL),
(844, 2, 34, 8, NULL, NULL),
(845, 2, 35, 26, NULL, NULL),
(846, 2, 36, 0, NULL, NULL),
(847, 2, 37, 23, NULL, NULL),
(848, 2, 38, 90, NULL, NULL),
(849, 2, 39, 21, NULL, NULL),
(850, 2, 40, 23, NULL, NULL),
(851, 2, 41, 24, NULL, NULL),
(852, 2, 42, 71, NULL, NULL),
(853, 2, 43, 42, NULL, NULL),
(854, 2, 44, 65, NULL, NULL),
(855, 2, 45, 18, NULL, NULL),
(856, 2, 46, 34, NULL, NULL),
(857, 2, 47, 54, NULL, NULL),
(858, 2, 48, 88, NULL, NULL),
(859, 2, 49, 12, NULL, NULL),
(860, 2, 50, 48, NULL, NULL),
(861, 2, 51, 24, NULL, NULL),
(862, 2, 52, 73, NULL, NULL),
(863, 2, 53, 60, NULL, NULL),
(864, 2, 54, 65, NULL, NULL),
(865, 2, 55, 81, NULL, NULL),
(866, 2, 56, 28, NULL, NULL),
(867, 2, 57, 74, NULL, NULL),
(868, 2, 58, 93, NULL, NULL),
(869, 2, 59, 21, NULL, NULL),
(870, 2, 60, 33, NULL, NULL),
(871, 2, 61, 35, NULL, NULL),
(872, 2, 62, 18, NULL, NULL),
(873, 2, 63, 61, NULL, NULL),
(874, 2, 64, 44, NULL, NULL),
(875, 2, 65, 85, NULL, NULL),
(876, 2, 66, 92, NULL, NULL),
(877, 2, 67, 15, NULL, NULL),
(878, 2, 68, 99, NULL, NULL),
(879, 2, 69, 97, NULL, NULL),
(880, 2, 70, 33, NULL, NULL),
(881, 2, 71, 42, NULL, NULL),
(882, 2, 72, 7, NULL, NULL),
(883, 2, 73, 50, NULL, NULL),
(884, 2, 74, 50, NULL, NULL),
(885, 2, 75, 59, NULL, NULL),
(886, 2, 76, 81, NULL, NULL),
(887, 2, 77, 1, NULL, NULL),
(888, 2, 78, 16, NULL, NULL),
(889, 2, 79, 52, NULL, NULL),
(890, 2, 80, 56, NULL, NULL),
(891, 2, 81, 66, NULL, NULL),
(892, 2, 82, 72, NULL, NULL),
(893, 2, 83, 57, NULL, NULL),
(894, 2, 84, 34, NULL, NULL),
(895, 2, 85, 90, NULL, NULL),
(896, 2, 86, 30, NULL, NULL),
(897, 2, 87, 70, NULL, NULL),
(898, 2, 88, 2, NULL, NULL),
(899, 2, 89, 50, NULL, NULL),
(900, 2, 90, 9, NULL, NULL),
(901, 2, 91, 9, NULL, NULL),
(902, 2, 92, 81, NULL, NULL),
(903, 2, 93, 52, NULL, NULL),
(904, 2, 94, 2, NULL, NULL),
(905, 2, 95, 52, NULL, NULL),
(906, 2, 96, 9, NULL, NULL),
(907, 2, 97, 23, NULL, NULL),
(908, 2, 98, 15, NULL, NULL),
(909, 2, 99, 81, NULL, NULL),
(910, 2, 100, 61, NULL, NULL),
(911, 2, 101, 100, NULL, NULL),
(912, 2, 102, 35, NULL, NULL),
(913, 2, 103, 83, NULL, NULL),
(914, 2, 104, 37, NULL, NULL),
(915, 2, 105, 67, NULL, NULL),
(916, 2, 106, 26, NULL, NULL),
(917, 2, 107, 71, NULL, NULL),
(918, 2, 108, 86, NULL, NULL),
(919, 2, 109, 41, NULL, NULL),
(920, 2, 110, 86, NULL, NULL),
(921, 2, 111, 2, NULL, NULL),
(922, 2, 112, 73, NULL, NULL),
(923, 2, 113, 30, NULL, NULL),
(924, 2, 114, 81, NULL, NULL),
(925, 2, 115, 83, NULL, NULL),
(926, 2, 116, 87, NULL, NULL),
(927, 2, 117, 78, NULL, NULL),
(928, 2, 118, 61, NULL, NULL),
(929, 2, 119, 80, NULL, NULL),
(930, 2, 120, 88, NULL, NULL),
(931, 2, 121, 4, NULL, NULL),
(932, 2, 122, 50, NULL, NULL),
(933, 2, 123, 12, NULL, NULL),
(934, 2, 124, 93, NULL, NULL),
(935, 2, 125, 58, NULL, NULL),
(936, 2, 126, 24, NULL, NULL),
(937, 2, 127, 65, NULL, NULL),
(938, 2, 128, 8, NULL, NULL),
(939, 2, 129, 53, NULL, NULL),
(940, 2, 130, 50, NULL, NULL),
(941, 2, 131, 84, NULL, NULL),
(942, 2, 132, 100, NULL, NULL),
(943, 2, 133, 98, NULL, NULL),
(944, 2, 134, 88, NULL, NULL),
(945, 2, 135, 98, NULL, NULL),
(946, 2, 136, 63, NULL, NULL),
(947, 2, 137, 13, NULL, NULL),
(948, 2, 138, 72, NULL, NULL),
(949, 2, 139, 6, NULL, NULL),
(950, 2, 140, 45, NULL, NULL),
(951, 2, 141, 19, NULL, NULL),
(952, 2, 142, 20, NULL, NULL),
(953, 2, 143, 57, NULL, NULL),
(954, 2, 144, 98, NULL, NULL),
(955, 2, 145, 67, NULL, NULL),
(956, 2, 146, 14, NULL, NULL),
(957, 2, 147, 22, NULL, NULL),
(958, 2, 148, 37, NULL, NULL),
(959, 2, 149, 17, NULL, NULL),
(960, 2, 150, 78, NULL, NULL),
(961, 2, 151, 29, NULL, NULL),
(962, 2, 152, 2, NULL, NULL),
(963, 2, 153, 50, NULL, NULL),
(964, 2, 154, 56, NULL, NULL),
(965, 2, 155, 0, NULL, NULL),
(966, 2, 156, 66, NULL, NULL),
(967, 2, 157, 16, NULL, NULL),
(968, 2, 158, 74, NULL, NULL),
(969, 2, 159, 18, NULL, NULL),
(970, 2, 160, 99, NULL, NULL),
(971, 2, 161, 57, NULL, NULL),
(972, 2, 162, 89, NULL, NULL),
(973, 2, 163, 15, NULL, NULL),
(974, 2, 164, 3, NULL, NULL),
(975, 2, 165, 5, NULL, NULL),
(976, 2, 166, 75, NULL, NULL),
(977, 2, 167, 6, NULL, NULL),
(978, 2, 168, 67, NULL, NULL),
(979, 2, 169, 67, NULL, NULL),
(980, 2, 170, 27, NULL, NULL),
(981, 2, 171, 27, NULL, NULL),
(982, 2, 172, 44, NULL, NULL),
(983, 2, 173, 81, NULL, NULL),
(984, 2, 174, 50, NULL, NULL),
(985, 2, 175, 83, NULL, NULL),
(986, 2, 176, 44, NULL, NULL),
(987, 2, 177, 19, NULL, NULL),
(988, 2, 178, 42, NULL, NULL),
(989, 2, 179, 40, NULL, NULL),
(990, 2, 180, 20, NULL, NULL),
(991, 2, 181, 74, NULL, NULL),
(992, 2, 182, 7, NULL, NULL),
(993, 2, 183, 75, NULL, NULL),
(994, 2, 184, 99, NULL, NULL),
(995, 2, 185, 1, NULL, NULL),
(996, 2, 186, 67, NULL, NULL),
(997, 2, 187, 18, NULL, NULL),
(998, 2, 188, 19, NULL, NULL),
(999, 2, 189, 38, NULL, NULL),
(1000, 2, 190, 17, NULL, NULL),
(1001, 2, 191, 21, NULL, NULL),
(1002, 2, 192, 64, NULL, NULL),
(1003, 2, 193, 0, NULL, NULL),
(1004, 2, 194, 21, NULL, NULL),
(1005, 2, 195, 100, NULL, NULL),
(1006, 2, 196, 15, NULL, NULL),
(1007, 2, 197, 28, NULL, NULL),
(1008, 2, 198, 89, NULL, NULL),
(1009, 2, 199, 77, NULL, NULL),
(1010, 2, 200, 8, NULL, NULL),
(1011, 2, 201, 74, NULL, NULL),
(1012, 2, 202, 5, NULL, NULL),
(1013, 2, 203, 46, NULL, NULL),
(1014, 2, 204, 30, NULL, NULL),
(1015, 2, 205, 26, NULL, NULL),
(1016, 2, 206, 33, NULL, NULL),
(1017, 2, 207, 62, NULL, NULL),
(1018, 2, 208, 51, NULL, NULL),
(1019, 2, 209, 49, NULL, NULL),
(1020, 2, 210, 1, NULL, NULL),
(1021, 2, 211, 29, NULL, NULL),
(1022, 2, 212, 57, NULL, NULL),
(1023, 2, 213, 78, NULL, NULL),
(1024, 2, 214, 58, NULL, NULL),
(1025, 2, 215, 98, NULL, NULL),
(1026, 2, 216, 60, NULL, NULL),
(1027, 2, 217, 58, NULL, NULL),
(1028, 2, 218, 75, NULL, NULL),
(1029, 2, 219, 33, NULL, NULL),
(1030, 2, 220, 78, NULL, NULL),
(1031, 2, 221, 55, NULL, NULL),
(1032, 2, 222, 43, NULL, NULL),
(1033, 2, 223, 62, NULL, NULL),
(1034, 2, 224, 6, NULL, NULL),
(1035, 2, 225, 83, NULL, NULL),
(1036, 2, 226, 66, NULL, NULL),
(1037, 2, 227, 27, NULL, NULL),
(1038, 2, 228, 99, NULL, NULL),
(1039, 2, 229, 39, NULL, NULL),
(1040, 2, 230, 15, NULL, NULL),
(1041, 2, 231, 64, NULL, NULL),
(1042, 2, 232, 18, NULL, NULL),
(1043, 2, 233, 28, NULL, NULL),
(1044, 2, 234, 78, NULL, NULL),
(1045, 2, 235, 17, NULL, NULL),
(1046, 2, 236, 84, NULL, NULL),
(1047, 2, 237, 28, NULL, NULL),
(1048, 2, 238, 3, NULL, NULL),
(1049, 2, 239, 56, NULL, NULL),
(1050, 2, 240, 55, NULL, NULL),
(1051, 2, 241, 92, NULL, NULL),
(1052, 2, 242, 65, NULL, NULL),
(1053, 2, 243, 8, NULL, NULL),
(1054, 2, 244, 80, NULL, NULL),
(1055, 2, 245, 35, NULL, NULL),
(1056, 2, 246, 32, NULL, NULL),
(1057, 2, 247, 95, NULL, NULL),
(1058, 2, 248, 12, NULL, NULL),
(1059, 2, 249, 58, NULL, NULL),
(1060, 2, 250, 52, NULL, NULL),
(1061, 2, 251, 86, NULL, NULL),
(1062, 2, 252, 85, NULL, NULL),
(1063, 2, 253, 79, NULL, NULL),
(1064, 2, 254, 15, NULL, NULL),
(1065, 2, 255, 89, NULL, NULL),
(1066, 2, 256, 49, NULL, NULL),
(1067, 2, 257, 12, NULL, NULL),
(1068, 2, 258, 15, NULL, NULL),
(1069, 2, 259, 7, NULL, NULL),
(1070, 2, 260, 4, NULL, NULL),
(1071, 2, 261, 89, NULL, NULL),
(1072, 2, 262, 9, NULL, NULL),
(1073, 2, 263, 14, NULL, NULL),
(1074, 2, 264, 6, NULL, NULL),
(1075, 2, 265, 29, NULL, NULL),
(1076, 2, 266, 66, NULL, NULL),
(1077, 2, 267, 50, NULL, NULL),
(1078, 2, 268, 21, NULL, NULL),
(1079, 2, 269, 21, NULL, NULL),
(1080, 2, 270, 72, NULL, NULL),
(1081, 2, 271, 29, NULL, NULL),
(1082, 2, 272, 64, NULL, NULL),
(1083, 2, 273, 28, NULL, NULL),
(1084, 2, 274, 57, NULL, NULL),
(1085, 2, 275, 52, NULL, NULL),
(1086, 2, 276, 9, NULL, NULL),
(1087, 2, 277, 15, NULL, NULL),
(1088, 2, 278, 53, NULL, NULL),
(1089, 2, 279, 25, NULL, NULL),
(1090, 2, 280, 41, NULL, NULL),
(1091, 2, 281, 15, NULL, NULL),
(1092, 2, 282, 46, NULL, NULL),
(1093, 2, 283, 19, NULL, NULL),
(1094, 2, 284, 86, NULL, NULL),
(1095, 2, 285, 62, NULL, NULL),
(1096, 2, 286, 0, NULL, NULL),
(1097, 2, 287, 45, NULL, NULL),
(1098, 2, 288, 51, NULL, NULL),
(1099, 2, 289, 64, NULL, NULL),
(1100, 2, 290, 36, NULL, NULL),
(1101, 2, 291, 2, NULL, NULL),
(1102, 2, 292, 26, NULL, NULL),
(1103, 2, 293, 90, NULL, NULL),
(1104, 2, 294, 60, NULL, NULL),
(1105, 2, 295, 9, NULL, NULL),
(1106, 2, 296, 92, NULL, NULL),
(1107, 2, 297, 44, NULL, NULL),
(1108, 2, 298, 42, NULL, NULL),
(1109, 2, 299, 80, NULL, NULL),
(1110, 2, 300, 83, NULL, NULL),
(1111, 2, 301, 11, NULL, NULL),
(1112, 2, 302, 48, NULL, NULL),
(1113, 2, 303, 24, NULL, NULL),
(1114, 2, 304, 1, NULL, NULL),
(1115, 2, 305, 59, NULL, NULL),
(1116, 2, 306, 47, NULL, NULL),
(1117, 2, 307, 3, NULL, NULL),
(1118, 2, 308, 8, NULL, NULL),
(1119, 2, 309, 47, NULL, NULL),
(1120, 2, 310, 14, NULL, NULL),
(1121, 2, 311, 89, NULL, NULL),
(1122, 2, 312, 94, NULL, NULL),
(1123, 2, 313, 10, NULL, NULL),
(1124, 2, 314, 8, NULL, NULL),
(1125, 2, 315, 25, NULL, NULL),
(1126, 2, 316, 65, NULL, NULL),
(1127, 2, 317, 6, NULL, NULL),
(1128, 2, 318, 37, NULL, NULL),
(1129, 2, 319, 93, NULL, NULL),
(1130, 2, 320, 36, NULL, NULL),
(1131, 2, 321, 48, NULL, NULL),
(1132, 2, 322, 55, NULL, NULL),
(1133, 2, 323, 66, NULL, NULL),
(1134, 2, 324, 23, NULL, NULL),
(1135, 2, 325, 8, NULL, NULL),
(1136, 2, 326, 36, NULL, NULL),
(1137, 2, 327, 10, NULL, NULL),
(1138, 2, 328, 92, NULL, NULL),
(1139, 2, 329, 65, NULL, NULL),
(1140, 2, 330, 74, NULL, NULL),
(1141, 2, 331, 39, NULL, NULL),
(1142, 2, 332, 58, NULL, NULL),
(1143, 2, 333, 30, NULL, NULL),
(1144, 2, 334, 69, NULL, NULL),
(1145, 2, 335, 3, NULL, NULL),
(1146, 2, 336, 2, NULL, NULL),
(1147, 2, 337, 99, NULL, NULL),
(1148, 2, 338, 12, NULL, NULL),
(1149, 2, 339, 70, NULL, NULL),
(1150, 2, 340, 62, NULL, NULL),
(1151, 2, 341, 77, NULL, NULL),
(1152, 2, 342, 39, NULL, NULL),
(1153, 2, 343, 31, NULL, NULL),
(1154, 2, 344, 10, NULL, NULL),
(1155, 2, 345, 89, NULL, NULL),
(1156, 2, 346, 93, NULL, NULL),
(1157, 2, 347, 4, NULL, NULL),
(1158, 2, 348, 71, NULL, NULL),
(1159, 2, 349, 97, NULL, NULL),
(1160, 2, 350, 2, NULL, NULL),
(1161, 2, 351, 55, NULL, NULL),
(1162, 2, 352, 85, NULL, NULL),
(1163, 2, 353, 99, NULL, NULL),
(1164, 2, 354, 18, NULL, NULL),
(1165, 2, 355, 12, NULL, NULL),
(1166, 2, 356, 32, NULL, NULL),
(1167, 2, 357, 52, NULL, NULL),
(1168, 2, 358, 97, NULL, NULL),
(1169, 2, 359, 51, NULL, NULL),
(1170, 2, 360, 85, NULL, NULL),
(1171, 2, 361, 23, NULL, NULL),
(1172, 2, 362, 56, NULL, NULL),
(1173, 2, 363, 14, NULL, NULL),
(1174, 2, 364, 27, NULL, NULL),
(1175, 2, 365, 16, NULL, NULL),
(1176, 2, 366, 27, NULL, NULL),
(1177, 2, 367, 8, NULL, NULL),
(1178, 2, 368, 82, NULL, NULL),
(1179, 2, 369, 91, NULL, NULL),
(1180, 2, 370, 81, NULL, NULL),
(1181, 2, 371, 64, NULL, NULL),
(1182, 2, 372, 42, NULL, NULL),
(1183, 2, 373, 92, NULL, NULL),
(1184, 2, 374, 78, NULL, NULL),
(1185, 2, 375, 70, NULL, NULL),
(1186, 2, 376, 87, NULL, NULL),
(1187, 2, 377, 51, NULL, NULL),
(1188, 2, 378, 66, NULL, NULL),
(1189, 2, 379, 52, NULL, NULL),
(1190, 2, 380, 50, NULL, NULL),
(1191, 2, 381, 6, NULL, NULL),
(1192, 2, 382, 42, NULL, NULL),
(1193, 2, 383, 79, NULL, NULL),
(1194, 2, 384, 67, NULL, NULL),
(1195, 2, 385, 83, NULL, NULL),
(1196, 2, 386, 70, NULL, NULL),
(1197, 2, 387, 30, NULL, NULL),
(1198, 2, 388, 38, NULL, NULL),
(1199, 2, 389, 11, NULL, NULL),
(1200, 2, 390, 66, NULL, NULL),
(1201, 2, 391, 31, NULL, NULL),
(1202, 2, 392, 100, NULL, NULL),
(1203, 2, 393, 79, NULL, NULL),
(1204, 2, 394, 96, NULL, NULL),
(1205, 2, 395, 14, NULL, NULL),
(1206, 2, 396, 35, NULL, NULL),
(1207, 2, 397, 66, NULL, NULL),
(1208, 2, 398, 100, NULL, NULL),
(1209, 2, 399, 16, NULL, NULL),
(1210, 2, 400, 94, NULL, NULL),
(1211, 2, 401, 53, NULL, NULL),
(1212, 2, 402, 11, NULL, NULL),
(1213, 2, 403, 62, NULL, NULL),
(1214, 2, 404, 83, NULL, NULL),
(1215, 2, 405, 82, NULL, NULL),
(1216, 2, 406, 23, NULL, NULL),
(1217, 2, 407, 78, NULL, NULL),
(1218, 2, 408, 57, NULL, NULL),
(1219, 2, 409, 50, NULL, NULL),
(1220, 2, 410, 34, NULL, NULL),
(1221, 2, 411, 94, NULL, NULL),
(1222, 2, 412, 28, NULL, NULL),
(1223, 2, 413, 33, NULL, NULL),
(1224, 2, 414, 4, NULL, NULL),
(1225, 2, 415, 23, NULL, NULL),
(1226, 2, 416, 62, NULL, NULL),
(1227, 2, 417, 3, NULL, NULL),
(1228, 2, 418, 33, NULL, NULL),
(1229, 2, 419, 34, NULL, NULL),
(1230, 2, 420, 93, NULL, NULL),
(1231, 2, 421, 36, NULL, NULL),
(1232, 2, 422, 41, NULL, NULL),
(1233, 2, 423, 17, NULL, NULL),
(1234, 2, 424, 52, NULL, NULL),
(1235, 2, 425, 50, NULL, NULL),
(1236, 2, 426, 95, NULL, NULL),
(1237, 2, 427, 57, NULL, NULL),
(1238, 2, 428, 63, NULL, NULL),
(1239, 2, 429, 21, NULL, NULL),
(1240, 2, 430, 92, NULL, NULL),
(1241, 2, 431, 47, NULL, NULL),
(1242, 2, 432, 0, NULL, NULL),
(1243, 2, 433, 30, NULL, NULL),
(1244, 2, 434, 28, NULL, NULL),
(1245, 2, 435, 57, NULL, NULL),
(1246, 2, 436, 27, NULL, NULL),
(1247, 2, 437, 36, NULL, NULL),
(1248, 2, 438, 89, NULL, NULL),
(1249, 2, 439, 19, NULL, NULL),
(1250, 2, 440, 81, NULL, NULL),
(1251, 2, 441, 46, NULL, NULL),
(1252, 2, 442, 59, NULL, NULL),
(1253, 2, 443, 22, NULL, NULL),
(1254, 2, 444, 29, NULL, NULL),
(1255, 2, 445, 37, NULL, NULL),
(1256, 2, 446, 52, NULL, NULL),
(1257, 2, 447, 49, NULL, NULL),
(1258, 2, 448, 96, NULL, NULL),
(1259, 2, 449, 63, NULL, NULL),
(1260, 2, 450, 32, NULL, NULL),
(1261, 2, 451, 25, NULL, NULL),
(1262, 2, 452, 22, NULL, NULL),
(1263, 2, 453, 38, NULL, NULL),
(1264, 2, 454, 46, NULL, NULL),
(1265, 2, 455, 64, NULL, NULL),
(1266, 2, 456, 46, NULL, NULL),
(1267, 2, 457, 67, NULL, NULL),
(1268, 2, 458, 66, NULL, NULL),
(1269, 2, 459, 80, NULL, NULL),
(1270, 2, 460, 25, NULL, NULL),
(1271, 2, 461, 7, NULL, NULL),
(1272, 2, 462, 69, NULL, NULL),
(1273, 2, 463, 100, NULL, NULL),
(1274, 2, 464, 17, NULL, NULL),
(1275, 2, 465, 1, NULL, NULL),
(1276, 2, 466, 93, NULL, NULL),
(1277, 2, 467, 77, NULL, NULL),
(1278, 2, 468, 88, NULL, NULL),
(1279, 2, 469, 18, NULL, NULL),
(1280, 2, 470, 94, NULL, NULL),
(1281, 2, 471, 35, NULL, NULL),
(1282, 2, 472, 65, NULL, NULL),
(1283, 2, 473, 0, NULL, NULL),
(1284, 2, 474, 37, NULL, NULL),
(1285, 2, 475, 79, NULL, NULL),
(1286, 2, 476, 6, NULL, NULL),
(1287, 2, 477, 47, NULL, NULL),
(1288, 2, 478, 54, NULL, NULL),
(1289, 2, 479, 79, NULL, NULL),
(1290, 2, 480, 75, NULL, NULL),
(1291, 2, 481, 37, NULL, NULL),
(1292, 2, 482, 64, NULL, NULL),
(1293, 2, 483, 89, NULL, NULL),
(1294, 2, 484, 0, NULL, NULL),
(1295, 2, 485, 21, NULL, NULL),
(1296, 2, 486, 70, NULL, NULL),
(1297, 2, 487, 85, NULL, NULL),
(1298, 2, 488, 90, NULL, NULL),
(1299, 2, 489, 4, NULL, NULL),
(1300, 2, 490, 88, NULL, NULL),
(1301, 2, 491, 94, NULL, NULL),
(1302, 2, 492, 14, NULL, NULL),
(1303, 2, 493, 50, NULL, NULL),
(1304, 2, 494, 19, NULL, NULL),
(1305, 2, 495, 83, NULL, NULL),
(1306, 2, 496, 59, NULL, NULL),
(1307, 2, 497, 61, NULL, NULL),
(1308, 2, 498, 58, NULL, NULL),
(1309, 2, 499, 54, NULL, NULL),
(1310, 2, 500, 66, NULL, NULL),
(1311, 2, 501, 9, NULL, NULL),
(1312, 2, 502, 46, NULL, NULL),
(1313, 2, 503, 37, NULL, NULL),
(1314, 2, 504, 38, NULL, NULL),
(1315, 2, 505, 74, NULL, NULL),
(1316, 2, 506, 65, NULL, NULL),
(1317, 2, 507, 11, NULL, NULL),
(1318, 2, 508, 34, NULL, NULL),
(1319, 2, 509, 92, NULL, NULL),
(1320, 2, 510, 91, NULL, NULL),
(1321, 2, 511, 24, NULL, NULL),
(1322, 2, 512, 84, NULL, NULL),
(1323, 2, 513, 66, NULL, NULL),
(1324, 2, 514, 82, NULL, NULL),
(1325, 2, 515, 64, NULL, NULL),
(1326, 2, 516, 14, NULL, NULL),
(1327, 2, 517, 71, NULL, NULL),
(1328, 2, 518, 18, NULL, NULL),
(1329, 2, 519, 31, NULL, NULL),
(1330, 2, 520, 40, NULL, NULL),
(1331, 2, 521, 28, NULL, NULL),
(1332, 2, 522, 76, NULL, NULL),
(1333, 2, 523, 11, NULL, NULL),
(1334, 2, 524, 22, NULL, NULL),
(1335, 2, 525, 74, NULL, NULL),
(1336, 2, 526, 42, NULL, NULL),
(1337, 2, 527, 29, NULL, NULL),
(1338, 2, 528, 20, NULL, NULL),
(1339, 2, 529, 57, NULL, NULL),
(1340, 2, 530, 19, NULL, NULL),
(1341, 2, 531, 10, NULL, NULL),
(1342, 2, 532, 94, NULL, NULL),
(1343, 2, 533, 54, NULL, NULL),
(1344, 2, 534, 98, NULL, NULL),
(1345, 2, 535, 88, NULL, NULL),
(1346, 2, 536, 82, NULL, NULL),
(1347, 2, 537, 13, NULL, NULL),
(1348, 2, 538, 13, NULL, NULL),
(1349, 2, 539, 22, NULL, NULL),
(1350, 2, 540, 50, NULL, NULL),
(1351, 2, 541, 35, NULL, NULL),
(1352, 2, 542, 83, NULL, NULL),
(1353, 2, 543, 63, NULL, NULL),
(1354, 2, 544, 74, NULL, NULL),
(1355, 2, 545, 55, NULL, NULL),
(1356, 2, 546, 100, NULL, NULL),
(1357, 2, 547, 94, NULL, NULL),
(1358, 2, 548, 54, NULL, NULL),
(1359, 2, 549, 56, NULL, NULL),
(1360, 2, 550, 86, NULL, NULL),
(1361, 2, 551, 36, NULL, NULL),
(1362, 2, 552, 62, NULL, NULL),
(1363, 2, 553, 48, NULL, NULL),
(1364, 2, 554, 19, NULL, NULL),
(1365, 2, 555, 28, NULL, NULL),
(1366, 2, 556, 59, NULL, NULL),
(1367, 2, 557, 8, NULL, NULL),
(1368, 2, 558, 22, NULL, NULL),
(1369, 2, 559, 33, NULL, NULL),
(1370, 2, 560, 12, NULL, NULL),
(1371, 2, 561, 57, NULL, NULL),
(1372, 2, 562, 13, NULL, NULL),
(1373, 2, 563, 48, NULL, NULL),
(1374, 2, 564, 2, NULL, NULL),
(1375, 2, 565, 9, NULL, NULL),
(1376, 2, 566, 61, NULL, NULL),
(1377, 2, 567, 26, NULL, NULL),
(1378, 2, 568, 37, NULL, NULL),
(1379, 2, 569, 47, NULL, NULL),
(1380, 2, 570, 23, NULL, NULL),
(1381, 2, 571, 56, NULL, NULL),
(1382, 2, 572, 91, NULL, NULL),
(1383, 2, 573, 75, NULL, NULL),
(1384, 2, 574, 21, NULL, NULL),
(1385, 2, 575, 49, NULL, NULL),
(1386, 2, 576, 10, NULL, NULL),
(1387, 2, 577, 3, NULL, NULL),
(1388, 2, 578, 49, NULL, NULL),
(1389, 2, 579, 15, NULL, NULL),
(1390, 2, 580, 4, NULL, NULL),
(1391, 2, 581, 25, NULL, NULL),
(1392, 2, 582, 45, NULL, NULL),
(1393, 2, 583, 1, NULL, NULL),
(1394, 2, 584, 25, NULL, NULL),
(1395, 2, 585, 28, NULL, NULL),
(1396, 2, 586, 83, NULL, NULL),
(1397, 2, 587, 46, NULL, NULL),
(1398, 2, 588, 74, NULL, NULL),
(1399, 2, 589, 22, NULL, NULL),
(1400, 2, 590, 7, NULL, NULL),
(1401, 2, 591, 29, NULL, NULL),
(1402, 2, 592, 89, NULL, NULL),
(1403, 2, 593, 45, NULL, NULL),
(1404, 2, 594, 47, NULL, NULL),
(1405, 2, 595, 18, NULL, NULL),
(1406, 2, 596, 32, NULL, NULL),
(1407, 2, 597, 70, NULL, NULL),
(1408, 2, 598, 13, NULL, NULL),
(1409, 2, 599, 17, NULL, NULL),
(1410, 2, 600, 72, NULL, NULL),
(1411, 2, 601, 60, NULL, NULL),
(1412, 2, 602, 18, NULL, NULL),
(1413, 2, 603, 68, NULL, NULL),
(1414, 2, 604, 77, NULL, NULL),
(1415, 2, 605, 91, NULL, NULL),
(1416, 2, 606, 32, NULL, NULL),
(1417, 2, 607, 89, NULL, NULL),
(1418, 2, 608, 99, NULL, NULL),
(1419, 2, 609, 51, NULL, NULL),
(1420, 2, 610, 79, NULL, NULL),
(1421, 2, 611, 51, NULL, NULL),
(1422, 2, 612, 89, NULL, NULL),
(1423, 2, 613, 42, NULL, NULL),
(1424, 2, 614, 76, NULL, NULL),
(1425, 2, 615, 67, NULL, NULL),
(1426, 2, 616, 24, NULL, NULL),
(1427, 2, 617, 96, NULL, NULL),
(1428, 2, 618, 88, NULL, NULL),
(1429, 2, 619, 95, NULL, NULL),
(1430, 2, 620, 86, NULL, NULL),
(1431, 2, 621, 67, NULL, NULL),
(1432, 2, 622, 75, NULL, NULL),
(1433, 2, 623, 47, NULL, NULL),
(1434, 2, 624, 74, NULL, NULL),
(1435, 2, 625, 17, NULL, NULL),
(1436, 2, 626, 90, NULL, NULL),
(1437, 2, 627, 54, NULL, NULL),
(1438, 2, 628, 86, NULL, NULL),
(1439, 2, 629, 14, NULL, NULL),
(1440, 2, 630, 30, NULL, NULL),
(1441, 2, 631, 83, NULL, NULL),
(1442, 2, 632, 4, NULL, NULL),
(1443, 2, 633, 38, NULL, NULL),
(1444, 2, 634, 95, NULL, NULL),
(1445, 2, 635, 90, NULL, NULL),
(1446, 2, 636, 83, NULL, NULL),
(1447, 2, 637, 85, NULL, NULL),
(1448, 2, 638, 49, NULL, NULL),
(1449, 2, 639, 41, NULL, NULL),
(1450, 2, 640, 55, NULL, NULL),
(1451, 2, 641, 56, NULL, NULL),
(1452, 2, 642, 76, NULL, NULL),
(1453, 2, 643, 74, NULL, NULL),
(1454, 2, 644, 99, NULL, NULL),
(1455, 2, 645, 33, NULL, NULL),
(1456, 2, 646, 73, NULL, NULL),
(1457, 2, 647, 64, NULL, NULL),
(1458, 2, 648, 33, NULL, NULL),
(1459, 2, 649, 86, NULL, NULL),
(1460, 2, 650, 79, NULL, NULL),
(1461, 2, 651, 39, NULL, NULL),
(1462, 2, 652, 56, NULL, NULL),
(1463, 2, 653, 80, NULL, NULL),
(1464, 2, 654, 36, NULL, NULL),
(1465, 2, 655, 31, NULL, NULL),
(1466, 2, 656, 40, NULL, NULL),
(1467, 2, 657, 97, NULL, NULL),
(1468, 2, 658, 22, NULL, NULL),
(1469, 2, 659, 21, NULL, NULL),
(1470, 2, 660, 23, NULL, NULL),
(1471, 2, 661, 54, NULL, NULL),
(1472, 2, 662, 33, NULL, NULL),
(1473, 2, 663, 8, NULL, NULL),
(1474, 2, 664, 36, NULL, NULL),
(1475, 2, 665, 33, NULL, NULL),
(1476, 2, 666, 97, NULL, NULL),
(1477, 2, 667, 88, NULL, NULL),
(1478, 2, 668, 29, NULL, NULL),
(1479, 2, 669, 39, NULL, NULL),
(1480, 2, 670, 14, NULL, NULL),
(1481, 2, 671, 9, NULL, NULL),
(1482, 2, 672, 54, NULL, NULL),
(1483, 2, 673, 47, NULL, NULL),
(1484, 2, 674, 61, NULL, NULL),
(1485, 2, 675, 90, NULL, NULL),
(1486, 2, 676, 7, NULL, NULL),
(1487, 2, 677, 5, NULL, NULL),
(1488, 2, 678, 70, NULL, NULL),
(1489, 2, 679, 67, NULL, NULL),
(1490, 2, 680, 97, NULL, NULL),
(1491, 2, 681, 56, NULL, NULL),
(1492, 2, 682, 89, NULL, NULL),
(1493, 2, 683, 89, NULL, NULL),
(1494, 2, 684, 57, NULL, NULL),
(1495, 2, 685, 54, NULL, NULL),
(1496, 2, 686, 75, NULL, NULL),
(1497, 2, 687, 52, NULL, NULL),
(1498, 2, 688, 87, NULL, NULL),
(1499, 2, 689, 10, NULL, NULL),
(1500, 2, 690, 72, NULL, NULL),
(1501, 2, 691, 79, NULL, NULL),
(1502, 2, 692, 33, NULL, NULL),
(1503, 2, 693, 53, NULL, NULL),
(1504, 2, 694, 69, NULL, NULL),
(1505, 2, 695, 1, NULL, NULL),
(1506, 2, 696, 39, NULL, NULL),
(1507, 2, 697, 7, NULL, NULL),
(1508, 2, 698, 72, NULL, NULL),
(1509, 2, 699, 95, NULL, NULL),
(1510, 2, 700, 16, NULL, NULL),
(1511, 2, 701, 18, NULL, NULL),
(1512, 2, 702, 49, NULL, NULL),
(1513, 2, 703, 37, NULL, NULL),
(1514, 2, 704, 12, NULL, NULL),
(1515, 2, 705, 99, NULL, NULL),
(1516, 2, 706, 50, NULL, NULL),
(1517, 2, 707, 94, NULL, NULL),
(1518, 2, 708, 21, NULL, NULL),
(1519, 2, 709, 92, NULL, NULL),
(1520, 2, 710, 68, NULL, NULL),
(1521, 2, 711, 80, NULL, NULL),
(1522, 2, 712, 74, NULL, NULL),
(1523, 2, 713, 87, NULL, NULL),
(1524, 2, 714, 78, NULL, NULL),
(1525, 2, 715, 85, NULL, NULL),
(1526, 2, 716, 36, NULL, NULL),
(1527, 2, 717, 8, NULL, NULL),
(1528, 2, 718, 88, NULL, NULL),
(1529, 2, 719, 0, NULL, NULL),
(1530, 2, 720, 88, NULL, NULL),
(1531, 2, 721, 17, NULL, NULL),
(1532, 2, 722, 74, NULL, NULL),
(1533, 2, 723, 15, NULL, NULL),
(1534, 2, 724, 57, NULL, NULL),
(1535, 2, 725, 39, NULL, NULL),
(1536, 2, 726, 54, NULL, NULL),
(1537, 2, 727, 25, NULL, NULL),
(1538, 2, 728, 73, NULL, NULL),
(1539, 2, 729, 16, NULL, NULL),
(1540, 2, 730, 79, NULL, NULL),
(1541, 2, 731, 24, NULL, NULL),
(1542, 2, 732, 78, NULL, NULL),
(1543, 2, 733, 30, NULL, NULL),
(1544, 2, 734, 51, NULL, NULL),
(1545, 2, 735, 3, NULL, NULL),
(1546, 2, 736, 24, NULL, NULL),
(1547, 2, 737, 8, NULL, NULL),
(1548, 2, 738, 94, NULL, NULL),
(1549, 2, 739, 36, NULL, NULL),
(1550, 2, 740, 80, NULL, NULL),
(1551, 2, 741, 39, NULL, NULL),
(1552, 2, 742, 85, NULL, NULL),
(1553, 2, 743, 11, NULL, NULL),
(1554, 2, 744, 66, NULL, NULL),
(1555, 2, 745, 99, NULL, NULL),
(1556, 2, 746, 44, NULL, NULL),
(1557, 2, 747, 17, NULL, NULL),
(1558, 2, 748, 64, NULL, NULL),
(1559, 2, 749, 78, NULL, NULL),
(1560, 2, 750, 70, NULL, NULL),
(1561, 2, 751, 94, NULL, NULL),
(1562, 2, 752, 33, NULL, NULL),
(1563, 2, 753, 48, NULL, NULL),
(1564, 2, 754, 42, NULL, NULL),
(1565, 2, 755, 8, NULL, NULL),
(1566, 2, 756, 27, NULL, NULL),
(1567, 2, 757, 68, NULL, NULL),
(1568, 2, 758, 67, NULL, NULL),
(1569, 2, 759, 71, NULL, NULL),
(1570, 2, 760, 30, NULL, NULL),
(1571, 2, 761, 92, NULL, NULL),
(1572, 2, 762, 26, NULL, NULL),
(1573, 2, 763, 57, NULL, NULL),
(1574, 2, 764, 92, NULL, NULL),
(1575, 2, 765, 31, NULL, NULL),
(1576, 2, 766, 23, NULL, NULL),
(1577, 2, 767, 47, NULL, NULL),
(1578, 2, 768, 18, NULL, NULL),
(1579, 2, 769, 56, NULL, NULL),
(1580, 2, 770, 26, NULL, NULL),
(1581, 2, 771, 43, NULL, NULL),
(1582, 2, 772, 86, NULL, NULL),
(1583, 2, 773, 90, NULL, NULL),
(1584, 2, 774, 85, NULL, NULL),
(1585, 2, 775, 60, NULL, NULL),
(1586, 2, 776, 6, NULL, NULL),
(1587, 2, 777, 20, NULL, NULL),
(1588, 2, 778, 82, NULL, NULL),
(1589, 2, 779, 51, NULL, NULL),
(1590, 2, 780, 1, NULL, NULL),
(1591, 2, 781, 81, NULL, NULL),
(1592, 2, 782, 67, NULL, NULL),
(1593, 2, 783, 3, NULL, NULL),
(1594, 2, 784, 58, NULL, NULL),
(1595, 2, 785, 43, NULL, NULL),
(1596, 2, 786, 92, NULL, NULL),
(1597, 2, 787, 88, NULL, NULL),
(1598, 2, 788, 31, NULL, NULL),
(1599, 2, 789, 80, NULL, NULL),
(1600, 2, 790, 41, NULL, NULL),
(1601, 2, 791, 26, NULL, NULL),
(1602, 2, 792, 75, NULL, NULL),
(1603, 2, 793, 61, NULL, NULL),
(1604, 2, 794, 41, NULL, NULL),
(1605, 2, 795, 82, NULL, NULL),
(1606, 2, 796, 77, NULL, NULL),
(1607, 2, 797, 43, NULL, NULL),
(1608, 2, 798, 67, NULL, NULL),
(1609, 2, 799, 12, NULL, NULL),
(1610, 2, 800, 55, NULL, NULL),
(1611, 2, 801, 47, NULL, NULL),
(1612, 2, 802, 73, NULL, NULL),
(1613, 2, 803, 88, NULL, NULL),
(1614, 2, 804, 42, NULL, NULL),
(1615, 2, 805, 17, NULL, NULL),
(1616, 2, 806, 58, NULL, NULL),
(1617, 2, 807, 67, NULL, NULL),
(1618, 2, 808, 30, NULL, NULL),
(1619, 2, 809, 43, NULL, NULL),
(1620, 2, 810, 6, NULL, NULL),
(1621, 3, 1, 39, NULL, NULL),
(1622, 3, 2, 2, NULL, NULL),
(1623, 3, 3, 13, NULL, NULL),
(1624, 3, 4, 36, NULL, NULL),
(1625, 3, 5, 12, NULL, NULL),
(1626, 3, 6, 1, NULL, NULL),
(1627, 3, 7, 27, NULL, NULL),
(1628, 3, 8, 3, NULL, NULL),
(1629, 3, 9, 81, NULL, NULL),
(1630, 3, 10, 15, NULL, NULL),
(1631, 3, 11, 69, NULL, NULL),
(1632, 3, 12, 100, NULL, NULL),
(1633, 3, 13, 95, NULL, NULL),
(1634, 3, 14, 41, NULL, NULL),
(1635, 3, 15, 81, NULL, NULL),
(1636, 3, 16, 67, NULL, NULL),
(1637, 3, 17, 35, NULL, NULL),
(1638, 3, 18, 66, NULL, NULL),
(1639, 3, 19, 49, NULL, NULL),
(1640, 3, 20, 97, NULL, NULL),
(1641, 3, 21, 50, NULL, NULL),
(1642, 3, 22, 31, NULL, NULL),
(1643, 3, 23, 54, NULL, NULL),
(1644, 3, 24, 97, NULL, NULL),
(1645, 3, 25, 62, NULL, NULL),
(1646, 3, 26, 36, NULL, NULL),
(1647, 3, 27, 12, NULL, NULL),
(1648, 3, 28, 15, NULL, NULL),
(1649, 3, 29, 14, NULL, NULL),
(1650, 3, 30, 53, NULL, NULL),
(1651, 3, 31, 13, NULL, NULL),
(1652, 3, 32, 86, NULL, NULL),
(1653, 3, 33, 68, NULL, NULL),
(1654, 3, 34, 24, NULL, NULL),
(1655, 3, 35, 53, NULL, NULL),
(1656, 3, 36, 53, NULL, NULL),
(1657, 3, 37, 22, NULL, NULL),
(1658, 3, 38, 44, NULL, NULL),
(1659, 3, 39, 8, NULL, NULL),
(1660, 3, 40, 84, NULL, NULL),
(1661, 3, 41, 85, NULL, NULL),
(1662, 3, 42, 0, NULL, NULL),
(1663, 3, 43, 29, NULL, NULL),
(1664, 3, 44, 46, NULL, NULL),
(1665, 3, 45, 22, NULL, NULL),
(1666, 3, 46, 16, NULL, NULL),
(1667, 3, 47, 25, NULL, NULL),
(1668, 3, 48, 81, NULL, NULL),
(1669, 3, 49, 30, NULL, NULL),
(1670, 3, 50, 35, NULL, NULL),
(1671, 3, 51, 90, NULL, NULL),
(1672, 3, 52, 100, NULL, NULL),
(1673, 3, 53, 100, NULL, NULL),
(1674, 3, 54, 88, NULL, NULL),
(1675, 3, 55, 67, NULL, NULL),
(1676, 3, 56, 62, NULL, NULL),
(1677, 3, 57, 93, NULL, NULL),
(1678, 3, 58, 48, NULL, NULL),
(1679, 3, 59, 51, NULL, NULL),
(1680, 3, 60, 13, NULL, NULL),
(1681, 3, 61, 23, NULL, NULL),
(1682, 3, 62, 44, NULL, NULL),
(1683, 3, 63, 61, NULL, NULL),
(1684, 3, 64, 1, NULL, NULL),
(1685, 3, 65, 86, NULL, NULL),
(1686, 3, 66, 3, NULL, NULL),
(1687, 3, 67, 97, NULL, NULL),
(1688, 3, 68, 17, NULL, NULL),
(1689, 3, 69, 92, NULL, NULL),
(1690, 3, 70, 63, NULL, NULL),
(1691, 3, 71, 24, NULL, NULL),
(1692, 3, 72, 0, NULL, NULL),
(1693, 3, 73, 49, NULL, NULL),
(1694, 3, 74, 28, NULL, NULL),
(1695, 3, 75, 84, NULL, NULL),
(1696, 3, 76, 72, NULL, NULL),
(1697, 3, 77, 11, NULL, NULL),
(1698, 3, 78, 51, NULL, NULL),
(1699, 3, 79, 0, NULL, NULL),
(1700, 3, 80, 39, NULL, NULL),
(1701, 3, 81, 40, NULL, NULL),
(1702, 3, 82, 68, NULL, NULL),
(1703, 3, 83, 21, NULL, NULL),
(1704, 3, 84, 69, NULL, NULL),
(1705, 3, 85, 5, NULL, NULL),
(1706, 3, 86, 48, NULL, NULL),
(1707, 3, 87, 60, NULL, NULL),
(1708, 3, 88, 39, NULL, NULL),
(1709, 3, 89, 22, NULL, NULL),
(1710, 3, 90, 4, NULL, NULL),
(1711, 3, 91, 65, NULL, NULL),
(1712, 3, 92, 11, NULL, NULL),
(1713, 3, 93, 31, NULL, NULL),
(1714, 3, 94, 45, NULL, NULL);
INSERT INTO `branch_stocks` (`id`, `branch_id`, `product_variant_id`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1715, 3, 95, 67, NULL, NULL),
(1716, 3, 96, 28, NULL, NULL),
(1717, 3, 97, 58, NULL, NULL),
(1718, 3, 98, 6, NULL, NULL),
(1719, 3, 99, 12, NULL, NULL),
(1720, 3, 100, 56, NULL, NULL),
(1721, 3, 101, 29, NULL, NULL),
(1722, 3, 102, 89, NULL, NULL),
(1723, 3, 103, 39, NULL, NULL),
(1724, 3, 104, 96, NULL, NULL),
(1725, 3, 105, 47, NULL, NULL),
(1726, 3, 106, 27, NULL, NULL),
(1727, 3, 107, 97, NULL, NULL),
(1728, 3, 108, 77, NULL, NULL),
(1729, 3, 109, 35, NULL, NULL),
(1730, 3, 110, 35, NULL, NULL),
(1731, 3, 111, 89, NULL, NULL),
(1732, 3, 112, 99, NULL, NULL),
(1733, 3, 113, 14, NULL, NULL),
(1734, 3, 114, 90, NULL, NULL),
(1735, 3, 115, 35, NULL, NULL),
(1736, 3, 116, 57, NULL, NULL),
(1737, 3, 117, 46, NULL, NULL),
(1738, 3, 118, 18, NULL, NULL),
(1739, 3, 119, 29, NULL, NULL),
(1740, 3, 120, 92, NULL, NULL),
(1741, 3, 121, 71, NULL, NULL),
(1742, 3, 122, 62, NULL, NULL),
(1743, 3, 123, 83, NULL, NULL),
(1744, 3, 124, 48, NULL, NULL),
(1745, 3, 125, 5, NULL, NULL),
(1746, 3, 126, 90, NULL, NULL),
(1747, 3, 127, 87, NULL, NULL),
(1748, 3, 128, 54, NULL, NULL),
(1749, 3, 129, 46, NULL, NULL),
(1750, 3, 130, 20, NULL, NULL),
(1751, 3, 131, 5, NULL, NULL),
(1752, 3, 132, 30, NULL, NULL),
(1753, 3, 133, 88, NULL, NULL),
(1754, 3, 134, 89, NULL, NULL),
(1755, 3, 135, 6, NULL, NULL),
(1756, 3, 136, 17, NULL, NULL),
(1757, 3, 137, 67, NULL, NULL),
(1758, 3, 138, 97, NULL, NULL),
(1759, 3, 139, 8, NULL, NULL),
(1760, 3, 140, 91, NULL, NULL),
(1761, 3, 141, 55, NULL, NULL),
(1762, 3, 142, 49, NULL, NULL),
(1763, 3, 143, 31, NULL, NULL),
(1764, 3, 144, 79, NULL, NULL),
(1765, 3, 145, 25, NULL, NULL),
(1766, 3, 146, 47, NULL, NULL),
(1767, 3, 147, 26, NULL, NULL),
(1768, 3, 148, 15, NULL, NULL),
(1769, 3, 149, 96, NULL, NULL),
(1770, 3, 150, 45, NULL, NULL),
(1771, 3, 151, 30, NULL, NULL),
(1772, 3, 152, 78, NULL, NULL),
(1773, 3, 153, 56, NULL, NULL),
(1774, 3, 154, 56, NULL, NULL),
(1775, 3, 155, 91, NULL, NULL),
(1776, 3, 156, 88, NULL, NULL),
(1777, 3, 157, 92, NULL, NULL),
(1778, 3, 158, 95, NULL, NULL),
(1779, 3, 159, 69, NULL, NULL),
(1780, 3, 160, 1, NULL, NULL),
(1781, 3, 161, 2, NULL, NULL),
(1782, 3, 162, 94, NULL, NULL),
(1783, 3, 163, 7, NULL, NULL),
(1784, 3, 164, 6, NULL, NULL),
(1785, 3, 165, 41, NULL, NULL),
(1786, 3, 166, 53, NULL, NULL),
(1787, 3, 167, 86, NULL, NULL),
(1788, 3, 168, 64, NULL, NULL),
(1789, 3, 169, 57, NULL, NULL),
(1790, 3, 170, 38, NULL, NULL),
(1791, 3, 171, 22, NULL, NULL),
(1792, 3, 172, 68, NULL, NULL),
(1793, 3, 173, 56, NULL, NULL),
(1794, 3, 174, 84, NULL, NULL),
(1795, 3, 175, 12, NULL, NULL),
(1796, 3, 176, 55, NULL, NULL),
(1797, 3, 177, 19, NULL, NULL),
(1798, 3, 178, 86, NULL, NULL),
(1799, 3, 179, 71, NULL, NULL),
(1800, 3, 180, 55, NULL, NULL),
(1801, 3, 181, 10, NULL, NULL),
(1802, 3, 182, 20, NULL, NULL),
(1803, 3, 183, 60, NULL, NULL),
(1804, 3, 184, 23, NULL, NULL),
(1805, 3, 185, 29, NULL, NULL),
(1806, 3, 186, 91, NULL, NULL),
(1807, 3, 187, 55, NULL, NULL),
(1808, 3, 188, 92, NULL, NULL),
(1809, 3, 189, 1, NULL, NULL),
(1810, 3, 190, 39, NULL, NULL),
(1811, 3, 191, 11, NULL, NULL),
(1812, 3, 192, 77, NULL, NULL),
(1813, 3, 193, 48, NULL, NULL),
(1814, 3, 194, 75, NULL, NULL),
(1815, 3, 195, 70, NULL, NULL),
(1816, 3, 196, 33, NULL, NULL),
(1817, 3, 197, 53, NULL, NULL),
(1818, 3, 198, 75, NULL, NULL),
(1819, 3, 199, 65, NULL, NULL),
(1820, 3, 200, 26, NULL, NULL),
(1821, 3, 201, 3, NULL, NULL),
(1822, 3, 202, 100, NULL, NULL),
(1823, 3, 203, 84, NULL, NULL),
(1824, 3, 204, 57, NULL, NULL),
(1825, 3, 205, 74, NULL, NULL),
(1826, 3, 206, 43, NULL, NULL),
(1827, 3, 207, 22, NULL, NULL),
(1828, 3, 208, 84, NULL, NULL),
(1829, 3, 209, 56, NULL, NULL),
(1830, 3, 210, 17, NULL, NULL),
(1831, 3, 211, 88, NULL, NULL),
(1832, 3, 212, 13, NULL, NULL),
(1833, 3, 213, 76, NULL, NULL),
(1834, 3, 214, 60, NULL, NULL),
(1835, 3, 215, 54, NULL, NULL),
(1836, 3, 216, 84, NULL, NULL),
(1837, 3, 217, 83, NULL, NULL),
(1838, 3, 218, 36, NULL, NULL),
(1839, 3, 219, 83, NULL, NULL),
(1840, 3, 220, 88, NULL, NULL),
(1841, 3, 221, 95, NULL, NULL),
(1842, 3, 222, 39, NULL, NULL),
(1843, 3, 223, 76, NULL, NULL),
(1844, 3, 224, 37, NULL, NULL),
(1845, 3, 225, 95, NULL, NULL),
(1846, 3, 226, 65, NULL, NULL),
(1847, 3, 227, 21, NULL, NULL),
(1848, 3, 228, 0, NULL, NULL),
(1849, 3, 229, 85, NULL, NULL),
(1850, 3, 230, 69, NULL, NULL),
(1851, 3, 231, 79, NULL, NULL),
(1852, 3, 232, 91, NULL, NULL),
(1853, 3, 233, 56, NULL, NULL),
(1854, 3, 234, 97, NULL, NULL),
(1855, 3, 235, 1, NULL, NULL),
(1856, 3, 236, 18, NULL, NULL),
(1857, 3, 237, 98, NULL, NULL),
(1858, 3, 238, 62, NULL, NULL),
(1859, 3, 239, 33, NULL, NULL),
(1860, 3, 240, 16, NULL, NULL),
(1861, 3, 241, 16, NULL, NULL),
(1862, 3, 242, 93, NULL, NULL),
(1863, 3, 243, 60, NULL, NULL),
(1864, 3, 244, 55, NULL, NULL),
(1865, 3, 245, 37, NULL, NULL),
(1866, 3, 246, 76, NULL, NULL),
(1867, 3, 247, 57, NULL, NULL),
(1868, 3, 248, 45, NULL, NULL),
(1869, 3, 249, 98, NULL, NULL),
(1870, 3, 250, 14, NULL, NULL),
(1871, 3, 251, 61, NULL, NULL),
(1872, 3, 252, 41, NULL, NULL),
(1873, 3, 253, 9, NULL, NULL),
(1874, 3, 254, 83, NULL, NULL),
(1875, 3, 255, 40, NULL, NULL),
(1876, 3, 256, 50, NULL, NULL),
(1877, 3, 257, 62, NULL, NULL),
(1878, 3, 258, 85, NULL, NULL),
(1879, 3, 259, 27, NULL, NULL),
(1880, 3, 260, 7, NULL, NULL),
(1881, 3, 261, 42, NULL, NULL),
(1882, 3, 262, 30, NULL, NULL),
(1883, 3, 263, 66, NULL, NULL),
(1884, 3, 264, 27, NULL, NULL),
(1885, 3, 265, 98, NULL, NULL),
(1886, 3, 266, 63, NULL, NULL),
(1887, 3, 267, 79, NULL, NULL),
(1888, 3, 268, 64, NULL, NULL),
(1889, 3, 269, 57, NULL, NULL),
(1890, 3, 270, 79, NULL, NULL),
(1891, 3, 271, 88, NULL, NULL),
(1892, 3, 272, 1, NULL, NULL),
(1893, 3, 273, 67, NULL, NULL),
(1894, 3, 274, 94, NULL, NULL),
(1895, 3, 275, 52, NULL, NULL),
(1896, 3, 276, 55, NULL, NULL),
(1897, 3, 277, 87, NULL, NULL),
(1898, 3, 278, 4, NULL, NULL),
(1899, 3, 279, 59, NULL, NULL),
(1900, 3, 280, 64, NULL, NULL),
(1901, 3, 281, 58, NULL, NULL),
(1902, 3, 282, 2, NULL, NULL),
(1903, 3, 283, 55, NULL, NULL),
(1904, 3, 284, 100, NULL, NULL),
(1905, 3, 285, 15, NULL, NULL),
(1906, 3, 286, 46, NULL, NULL),
(1907, 3, 287, 47, NULL, NULL),
(1908, 3, 288, 49, NULL, NULL),
(1909, 3, 289, 92, NULL, NULL),
(1910, 3, 290, 31, NULL, NULL),
(1911, 3, 291, 99, NULL, NULL),
(1912, 3, 292, 30, NULL, NULL),
(1913, 3, 293, 9, NULL, NULL),
(1914, 3, 294, 65, NULL, NULL),
(1915, 3, 295, 36, NULL, NULL),
(1916, 3, 296, 72, NULL, NULL),
(1917, 3, 297, 22, NULL, NULL),
(1918, 3, 298, 72, NULL, NULL),
(1919, 3, 299, 74, NULL, NULL),
(1920, 3, 300, 50, NULL, NULL),
(1921, 3, 301, 1, NULL, NULL),
(1922, 3, 302, 57, NULL, NULL),
(1923, 3, 303, 21, NULL, NULL),
(1924, 3, 304, 67, NULL, NULL),
(1925, 3, 305, 33, NULL, NULL),
(1926, 3, 306, 1, NULL, NULL),
(1927, 3, 307, 97, NULL, NULL),
(1928, 3, 308, 45, NULL, NULL),
(1929, 3, 309, 95, NULL, NULL),
(1930, 3, 310, 12, NULL, NULL),
(1931, 3, 311, 89, NULL, NULL),
(1932, 3, 312, 68, NULL, NULL),
(1933, 3, 313, 71, NULL, NULL),
(1934, 3, 314, 56, NULL, NULL),
(1935, 3, 315, 84, NULL, NULL),
(1936, 3, 316, 5, NULL, NULL),
(1937, 3, 317, 61, NULL, NULL),
(1938, 3, 318, 44, NULL, NULL),
(1939, 3, 319, 40, NULL, NULL),
(1940, 3, 320, 88, NULL, NULL),
(1941, 3, 321, 63, NULL, NULL),
(1942, 3, 322, 46, NULL, NULL),
(1943, 3, 323, 55, NULL, NULL),
(1944, 3, 324, 58, NULL, NULL),
(1945, 3, 325, 78, NULL, NULL),
(1946, 3, 326, 47, NULL, NULL),
(1947, 3, 327, 82, NULL, NULL),
(1948, 3, 328, 76, NULL, NULL),
(1949, 3, 329, 18, NULL, NULL),
(1950, 3, 330, 38, NULL, NULL),
(1951, 3, 331, 63, NULL, NULL),
(1952, 3, 332, 95, NULL, NULL),
(1953, 3, 333, 69, NULL, NULL),
(1954, 3, 334, 76, NULL, NULL),
(1955, 3, 335, 50, NULL, NULL),
(1956, 3, 336, 89, NULL, NULL),
(1957, 3, 337, 25, NULL, NULL),
(1958, 3, 338, 20, NULL, NULL),
(1959, 3, 339, 60, NULL, NULL),
(1960, 3, 340, 96, NULL, NULL),
(1961, 3, 341, 80, NULL, NULL),
(1962, 3, 342, 15, NULL, NULL),
(1963, 3, 343, 85, NULL, NULL),
(1964, 3, 344, 36, NULL, NULL),
(1965, 3, 345, 69, NULL, NULL),
(1966, 3, 346, 61, NULL, NULL),
(1967, 3, 347, 52, NULL, NULL),
(1968, 3, 348, 54, NULL, NULL),
(1969, 3, 349, 99, NULL, NULL),
(1970, 3, 350, 42, NULL, NULL),
(1971, 3, 351, 56, NULL, NULL),
(1972, 3, 352, 79, NULL, NULL),
(1973, 3, 353, 88, NULL, NULL),
(1974, 3, 354, 53, NULL, NULL),
(1975, 3, 355, 97, NULL, NULL),
(1976, 3, 356, 92, NULL, NULL),
(1977, 3, 357, 26, NULL, NULL),
(1978, 3, 358, 67, NULL, NULL),
(1979, 3, 359, 60, NULL, NULL),
(1980, 3, 360, 65, NULL, NULL),
(1981, 3, 361, 14, NULL, NULL),
(1982, 3, 362, 45, NULL, NULL),
(1983, 3, 363, 73, NULL, NULL),
(1984, 3, 364, 97, NULL, NULL),
(1985, 3, 365, 33, NULL, NULL),
(1986, 3, 366, 85, NULL, NULL),
(1987, 3, 367, 71, NULL, NULL),
(1988, 3, 368, 82, NULL, NULL),
(1989, 3, 369, 92, NULL, NULL),
(1990, 3, 370, 88, NULL, NULL),
(1991, 3, 371, 51, NULL, NULL),
(1992, 3, 372, 1, NULL, NULL),
(1993, 3, 373, 43, NULL, NULL),
(1994, 3, 374, 53, NULL, NULL),
(1995, 3, 375, 92, NULL, NULL),
(1996, 3, 376, 24, NULL, NULL),
(1997, 3, 377, 91, NULL, NULL),
(1998, 3, 378, 64, NULL, NULL),
(1999, 3, 379, 96, NULL, NULL),
(2000, 3, 380, 67, NULL, NULL),
(2001, 3, 381, 22, NULL, NULL),
(2002, 3, 382, 33, NULL, NULL),
(2003, 3, 383, 39, NULL, NULL),
(2004, 3, 384, 28, NULL, NULL),
(2005, 3, 385, 5, NULL, NULL),
(2006, 3, 386, 57, NULL, NULL),
(2007, 3, 387, 79, NULL, NULL),
(2008, 3, 388, 23, NULL, NULL),
(2009, 3, 389, 66, NULL, NULL),
(2010, 3, 390, 46, NULL, NULL),
(2011, 3, 391, 3, NULL, NULL),
(2012, 3, 392, 61, NULL, NULL),
(2013, 3, 393, 68, NULL, NULL),
(2014, 3, 394, 97, NULL, NULL),
(2015, 3, 395, 27, NULL, NULL),
(2016, 3, 396, 11, NULL, NULL),
(2017, 3, 397, 9, NULL, NULL),
(2018, 3, 398, 61, NULL, NULL),
(2019, 3, 399, 93, NULL, NULL),
(2020, 3, 400, 99, NULL, NULL),
(2021, 3, 401, 23, NULL, NULL),
(2022, 3, 402, 82, NULL, NULL),
(2023, 3, 403, 37, NULL, NULL),
(2024, 3, 404, 3, NULL, NULL),
(2025, 3, 405, 7, NULL, NULL),
(2026, 3, 406, 64, NULL, NULL),
(2027, 3, 407, 80, NULL, NULL),
(2028, 3, 408, 20, NULL, NULL),
(2029, 3, 409, 91, NULL, NULL),
(2030, 3, 410, 28, NULL, NULL),
(2031, 3, 411, 32, NULL, NULL),
(2032, 3, 412, 83, NULL, NULL),
(2033, 3, 413, 28, NULL, NULL),
(2034, 3, 414, 73, NULL, NULL),
(2035, 3, 415, 66, NULL, NULL),
(2036, 3, 416, 69, NULL, NULL),
(2037, 3, 417, 77, NULL, NULL),
(2038, 3, 418, 26, NULL, NULL),
(2039, 3, 419, 22, NULL, NULL),
(2040, 3, 420, 98, NULL, NULL),
(2041, 3, 421, 54, NULL, NULL),
(2042, 3, 422, 33, NULL, NULL),
(2043, 3, 423, 57, NULL, NULL),
(2044, 3, 424, 26, NULL, NULL),
(2045, 3, 425, 37, NULL, NULL),
(2046, 3, 426, 28, NULL, NULL),
(2047, 3, 427, 20, NULL, NULL),
(2048, 3, 428, 60, NULL, NULL),
(2049, 3, 429, 14, NULL, NULL),
(2050, 3, 430, 50, NULL, NULL),
(2051, 3, 431, 92, NULL, NULL),
(2052, 3, 432, 31, NULL, NULL),
(2053, 3, 433, 23, NULL, NULL),
(2054, 3, 434, 43, NULL, NULL),
(2055, 3, 435, 98, NULL, NULL),
(2056, 3, 436, 46, NULL, NULL),
(2057, 3, 437, 77, NULL, NULL),
(2058, 3, 438, 71, NULL, NULL),
(2059, 3, 439, 83, NULL, NULL),
(2060, 3, 440, 26, NULL, NULL),
(2061, 3, 441, 8, NULL, NULL),
(2062, 3, 442, 4, NULL, NULL),
(2063, 3, 443, 60, NULL, NULL),
(2064, 3, 444, 9, NULL, NULL),
(2065, 3, 445, 97, NULL, NULL),
(2066, 3, 446, 75, NULL, NULL),
(2067, 3, 447, 4, NULL, NULL),
(2068, 3, 448, 45, NULL, NULL),
(2069, 3, 449, 45, NULL, NULL),
(2070, 3, 450, 18, NULL, NULL),
(2071, 3, 451, 33, NULL, NULL),
(2072, 3, 452, 44, NULL, NULL),
(2073, 3, 453, 21, NULL, NULL),
(2074, 3, 454, 44, NULL, NULL),
(2075, 3, 455, 36, NULL, NULL),
(2076, 3, 456, 47, NULL, NULL),
(2077, 3, 457, 3, NULL, NULL),
(2078, 3, 458, 38, NULL, NULL),
(2079, 3, 459, 38, NULL, NULL),
(2080, 3, 460, 98, NULL, NULL),
(2081, 3, 461, 34, NULL, NULL),
(2082, 3, 462, 73, NULL, NULL),
(2083, 3, 463, 0, NULL, NULL),
(2084, 3, 464, 88, NULL, NULL),
(2085, 3, 465, 72, NULL, NULL),
(2086, 3, 466, 6, NULL, NULL),
(2087, 3, 467, 98, NULL, NULL),
(2088, 3, 468, 77, NULL, NULL),
(2089, 3, 469, 38, NULL, NULL),
(2090, 3, 470, 59, NULL, NULL),
(2091, 3, 471, 66, NULL, NULL),
(2092, 3, 472, 36, NULL, NULL),
(2093, 3, 473, 59, NULL, NULL),
(2094, 3, 474, 47, NULL, NULL),
(2095, 3, 475, 36, NULL, NULL),
(2096, 3, 476, 36, NULL, NULL),
(2097, 3, 477, 48, NULL, NULL),
(2098, 3, 478, 59, NULL, NULL),
(2099, 3, 479, 46, NULL, NULL),
(2100, 3, 480, 62, NULL, NULL),
(2101, 3, 481, 99, NULL, NULL),
(2102, 3, 482, 64, NULL, NULL),
(2103, 3, 483, 1, NULL, NULL),
(2104, 3, 484, 43, NULL, NULL),
(2105, 3, 485, 17, NULL, NULL),
(2106, 3, 486, 32, NULL, NULL),
(2107, 3, 487, 71, NULL, NULL),
(2108, 3, 488, 11, NULL, NULL),
(2109, 3, 489, 83, NULL, NULL),
(2110, 3, 490, 37, NULL, NULL),
(2111, 3, 491, 90, NULL, NULL),
(2112, 3, 492, 73, NULL, NULL),
(2113, 3, 493, 51, NULL, NULL),
(2114, 3, 494, 67, NULL, NULL),
(2115, 3, 495, 99, NULL, NULL),
(2116, 3, 496, 61, NULL, NULL),
(2117, 3, 497, 57, NULL, NULL),
(2118, 3, 498, 1, NULL, NULL),
(2119, 3, 499, 80, NULL, NULL),
(2120, 3, 500, 48, NULL, NULL),
(2121, 3, 501, 11, NULL, NULL),
(2122, 3, 502, 50, NULL, NULL),
(2123, 3, 503, 39, NULL, NULL),
(2124, 3, 504, 83, NULL, NULL),
(2125, 3, 505, 91, NULL, NULL),
(2126, 3, 506, 68, NULL, NULL),
(2127, 3, 507, 25, NULL, NULL),
(2128, 3, 508, 49, NULL, NULL),
(2129, 3, 509, 21, NULL, NULL),
(2130, 3, 510, 25, NULL, NULL),
(2131, 3, 511, 76, NULL, NULL),
(2132, 3, 512, 49, NULL, NULL),
(2133, 3, 513, 32, NULL, NULL),
(2134, 3, 514, 87, NULL, NULL),
(2135, 3, 515, 75, NULL, NULL),
(2136, 3, 516, 44, NULL, NULL),
(2137, 3, 517, 10, NULL, NULL),
(2138, 3, 518, 57, NULL, NULL),
(2139, 3, 519, 57, NULL, NULL),
(2140, 3, 520, 58, NULL, NULL),
(2141, 3, 521, 77, NULL, NULL),
(2142, 3, 522, 100, NULL, NULL),
(2143, 3, 523, 71, NULL, NULL),
(2144, 3, 524, 64, NULL, NULL),
(2145, 3, 525, 20, NULL, NULL),
(2146, 3, 526, 38, NULL, NULL),
(2147, 3, 527, 70, NULL, NULL),
(2148, 3, 528, 91, NULL, NULL),
(2149, 3, 529, 26, NULL, NULL),
(2150, 3, 530, 11, NULL, NULL),
(2151, 3, 531, 75, NULL, NULL),
(2152, 3, 532, 8, NULL, NULL),
(2153, 3, 533, 48, NULL, NULL),
(2154, 3, 534, 73, NULL, NULL),
(2155, 3, 535, 31, NULL, NULL),
(2156, 3, 536, 55, NULL, NULL),
(2157, 3, 537, 5, NULL, NULL),
(2158, 3, 538, 59, NULL, NULL),
(2159, 3, 539, 0, NULL, NULL),
(2160, 3, 540, 75, NULL, NULL),
(2161, 3, 541, 49, NULL, NULL),
(2162, 3, 542, 47, NULL, NULL),
(2163, 3, 543, 23, NULL, NULL),
(2164, 3, 544, 97, NULL, NULL),
(2165, 3, 545, 24, NULL, NULL),
(2166, 3, 546, 79, NULL, NULL),
(2167, 3, 547, 44, NULL, NULL),
(2168, 3, 548, 51, NULL, NULL),
(2169, 3, 549, 91, NULL, NULL),
(2170, 3, 550, 38, NULL, NULL),
(2171, 3, 551, 48, NULL, NULL),
(2172, 3, 552, 74, NULL, NULL),
(2173, 3, 553, 96, NULL, NULL),
(2174, 3, 554, 45, NULL, NULL),
(2175, 3, 555, 4, NULL, NULL),
(2176, 3, 556, 76, NULL, NULL),
(2177, 3, 557, 0, NULL, NULL),
(2178, 3, 558, 15, NULL, NULL),
(2179, 3, 559, 81, NULL, NULL),
(2180, 3, 560, 34, NULL, NULL),
(2181, 3, 561, 99, NULL, NULL),
(2182, 3, 562, 58, NULL, NULL),
(2183, 3, 563, 59, NULL, NULL),
(2184, 3, 564, 35, NULL, NULL),
(2185, 3, 565, 16, NULL, NULL),
(2186, 3, 566, 94, NULL, NULL),
(2187, 3, 567, 57, NULL, NULL),
(2188, 3, 568, 62, NULL, NULL),
(2189, 3, 569, 85, NULL, NULL),
(2190, 3, 570, 28, NULL, NULL),
(2191, 3, 571, 68, NULL, NULL),
(2192, 3, 572, 30, NULL, NULL),
(2193, 3, 573, 58, NULL, NULL),
(2194, 3, 574, 50, NULL, NULL),
(2195, 3, 575, 92, NULL, NULL),
(2196, 3, 576, 51, NULL, NULL),
(2197, 3, 577, 36, NULL, NULL),
(2198, 3, 578, 40, NULL, NULL),
(2199, 3, 579, 46, NULL, NULL),
(2200, 3, 580, 31, NULL, NULL),
(2201, 3, 581, 97, NULL, NULL),
(2202, 3, 582, 66, NULL, NULL),
(2203, 3, 583, 41, NULL, NULL),
(2204, 3, 584, 84, NULL, NULL),
(2205, 3, 585, 26, NULL, NULL),
(2206, 3, 586, 72, NULL, NULL),
(2207, 3, 587, 88, NULL, NULL),
(2208, 3, 588, 82, NULL, NULL),
(2209, 3, 589, 54, NULL, NULL),
(2210, 3, 590, 23, NULL, NULL),
(2211, 3, 591, 63, NULL, NULL),
(2212, 3, 592, 93, NULL, NULL),
(2213, 3, 593, 81, NULL, NULL),
(2214, 3, 594, 56, NULL, NULL),
(2215, 3, 595, 35, NULL, NULL),
(2216, 3, 596, 76, NULL, NULL),
(2217, 3, 597, 47, NULL, NULL),
(2218, 3, 598, 8, NULL, NULL),
(2219, 3, 599, 48, NULL, NULL),
(2220, 3, 600, 38, NULL, NULL),
(2221, 3, 601, 3, NULL, NULL),
(2222, 3, 602, 3, NULL, NULL),
(2223, 3, 603, 65, NULL, NULL),
(2224, 3, 604, 80, NULL, NULL),
(2225, 3, 605, 73, NULL, NULL),
(2226, 3, 606, 9, NULL, NULL),
(2227, 3, 607, 47, NULL, NULL),
(2228, 3, 608, 44, NULL, NULL),
(2229, 3, 609, 37, NULL, NULL),
(2230, 3, 610, 33, NULL, NULL),
(2231, 3, 611, 35, NULL, NULL),
(2232, 3, 612, 3, NULL, NULL),
(2233, 3, 613, 13, NULL, NULL),
(2234, 3, 614, 87, NULL, NULL),
(2235, 3, 615, 79, NULL, NULL),
(2236, 3, 616, 97, NULL, NULL),
(2237, 3, 617, 69, NULL, NULL),
(2238, 3, 618, 8, NULL, NULL),
(2239, 3, 619, 5, NULL, NULL),
(2240, 3, 620, 100, NULL, NULL),
(2241, 3, 621, 88, NULL, NULL),
(2242, 3, 622, 64, NULL, NULL),
(2243, 3, 623, 1, NULL, NULL),
(2244, 3, 624, 77, NULL, NULL),
(2245, 3, 625, 56, NULL, NULL),
(2246, 3, 626, 70, NULL, NULL),
(2247, 3, 627, 18, NULL, NULL),
(2248, 3, 628, 82, NULL, NULL),
(2249, 3, 629, 93, NULL, NULL),
(2250, 3, 630, 52, NULL, NULL),
(2251, 3, 631, 28, NULL, NULL),
(2252, 3, 632, 65, NULL, NULL),
(2253, 3, 633, 41, NULL, NULL),
(2254, 3, 634, 87, NULL, NULL),
(2255, 3, 635, 75, NULL, NULL),
(2256, 3, 636, 52, NULL, NULL),
(2257, 3, 637, 57, NULL, NULL),
(2258, 3, 638, 95, NULL, NULL),
(2259, 3, 639, 62, NULL, NULL),
(2260, 3, 640, 48, NULL, NULL),
(2261, 3, 641, 17, NULL, NULL),
(2262, 3, 642, 60, NULL, NULL),
(2263, 3, 643, 96, NULL, NULL),
(2264, 3, 644, 66, NULL, NULL),
(2265, 3, 645, 52, NULL, NULL),
(2266, 3, 646, 65, NULL, NULL),
(2267, 3, 647, 15, NULL, NULL),
(2268, 3, 648, 20, NULL, NULL),
(2269, 3, 649, 45, NULL, NULL),
(2270, 3, 650, 44, NULL, NULL),
(2271, 3, 651, 26, NULL, NULL),
(2272, 3, 652, 88, NULL, NULL),
(2273, 3, 653, 62, NULL, NULL),
(2274, 3, 654, 48, NULL, NULL),
(2275, 3, 655, 95, NULL, NULL),
(2276, 3, 656, 48, NULL, NULL),
(2277, 3, 657, 10, NULL, NULL),
(2278, 3, 658, 23, NULL, NULL),
(2279, 3, 659, 92, NULL, NULL),
(2280, 3, 660, 90, NULL, NULL),
(2281, 3, 661, 25, NULL, NULL),
(2282, 3, 662, 74, NULL, NULL),
(2283, 3, 663, 93, NULL, NULL),
(2284, 3, 664, 3, NULL, NULL),
(2285, 3, 665, 8, NULL, NULL),
(2286, 3, 666, 90, NULL, NULL),
(2287, 3, 667, 68, NULL, NULL),
(2288, 3, 668, 25, NULL, NULL),
(2289, 3, 669, 31, NULL, NULL),
(2290, 3, 670, 86, NULL, NULL),
(2291, 3, 671, 38, NULL, NULL),
(2292, 3, 672, 84, NULL, NULL),
(2293, 3, 673, 93, NULL, NULL),
(2294, 3, 674, 30, NULL, NULL),
(2295, 3, 675, 75, NULL, NULL),
(2296, 3, 676, 95, NULL, NULL),
(2297, 3, 677, 57, NULL, NULL),
(2298, 3, 678, 70, NULL, NULL),
(2299, 3, 679, 7, NULL, NULL),
(2300, 3, 680, 54, NULL, NULL),
(2301, 3, 681, 13, NULL, NULL),
(2302, 3, 682, 42, NULL, NULL),
(2303, 3, 683, 14, NULL, NULL),
(2304, 3, 684, 95, NULL, NULL),
(2305, 3, 685, 14, NULL, NULL),
(2306, 3, 686, 100, NULL, NULL),
(2307, 3, 687, 47, NULL, NULL),
(2308, 3, 688, 99, NULL, NULL),
(2309, 3, 689, 6, NULL, NULL),
(2310, 3, 690, 74, NULL, NULL),
(2311, 3, 691, 10, NULL, NULL),
(2312, 3, 692, 25, NULL, NULL),
(2313, 3, 693, 36, NULL, NULL),
(2314, 3, 694, 34, NULL, NULL),
(2315, 3, 695, 33, NULL, NULL),
(2316, 3, 696, 84, NULL, NULL),
(2317, 3, 697, 55, NULL, NULL),
(2318, 3, 698, 57, NULL, NULL),
(2319, 3, 699, 64, NULL, NULL),
(2320, 3, 700, 17, NULL, NULL),
(2321, 3, 701, 36, NULL, NULL),
(2322, 3, 702, 28, NULL, NULL),
(2323, 3, 703, 21, NULL, NULL),
(2324, 3, 704, 24, NULL, NULL),
(2325, 3, 705, 1, NULL, NULL),
(2326, 3, 706, 39, NULL, NULL),
(2327, 3, 707, 65, NULL, NULL),
(2328, 3, 708, 44, NULL, NULL),
(2329, 3, 709, 14, NULL, NULL),
(2330, 3, 710, 86, NULL, NULL),
(2331, 3, 711, 68, NULL, NULL),
(2332, 3, 712, 19, NULL, NULL),
(2333, 3, 713, 39, NULL, NULL),
(2334, 3, 714, 8, NULL, NULL),
(2335, 3, 715, 87, NULL, NULL),
(2336, 3, 716, 3, NULL, NULL),
(2337, 3, 717, 61, NULL, NULL),
(2338, 3, 718, 57, NULL, NULL),
(2339, 3, 719, 42, NULL, NULL),
(2340, 3, 720, 69, NULL, NULL),
(2341, 3, 721, 7, NULL, NULL),
(2342, 3, 722, 56, NULL, NULL),
(2343, 3, 723, 82, NULL, NULL),
(2344, 3, 724, 68, NULL, NULL),
(2345, 3, 725, 66, NULL, NULL),
(2346, 3, 726, 54, NULL, NULL),
(2347, 3, 727, 92, NULL, NULL),
(2348, 3, 728, 69, NULL, NULL),
(2349, 3, 729, 58, NULL, NULL),
(2350, 3, 730, 36, NULL, NULL),
(2351, 3, 731, 66, NULL, NULL),
(2352, 3, 732, 12, NULL, NULL),
(2353, 3, 733, 89, NULL, NULL),
(2354, 3, 734, 58, NULL, NULL),
(2355, 3, 735, 79, NULL, NULL),
(2356, 3, 736, 58, NULL, NULL),
(2357, 3, 737, 36, NULL, NULL),
(2358, 3, 738, 44, NULL, NULL),
(2359, 3, 739, 72, NULL, NULL),
(2360, 3, 740, 16, NULL, NULL),
(2361, 3, 741, 73, NULL, NULL),
(2362, 3, 742, 55, NULL, NULL),
(2363, 3, 743, 0, NULL, NULL),
(2364, 3, 744, 100, NULL, NULL),
(2365, 3, 745, 91, NULL, NULL),
(2366, 3, 746, 75, NULL, NULL),
(2367, 3, 747, 27, NULL, NULL),
(2368, 3, 748, 10, NULL, NULL),
(2369, 3, 749, 89, NULL, NULL),
(2370, 3, 750, 95, NULL, NULL),
(2371, 3, 751, 61, NULL, NULL),
(2372, 3, 752, 32, NULL, NULL),
(2373, 3, 753, 66, NULL, NULL),
(2374, 3, 754, 75, NULL, NULL),
(2375, 3, 755, 58, NULL, NULL),
(2376, 3, 756, 33, NULL, NULL),
(2377, 3, 757, 17, NULL, NULL),
(2378, 3, 758, 36, NULL, NULL),
(2379, 3, 759, 76, NULL, NULL),
(2380, 3, 760, 76, NULL, NULL),
(2381, 3, 761, 1, NULL, NULL),
(2382, 3, 762, 98, NULL, NULL),
(2383, 3, 763, 52, NULL, NULL),
(2384, 3, 764, 79, NULL, NULL),
(2385, 3, 765, 84, NULL, NULL),
(2386, 3, 766, 12, NULL, NULL),
(2387, 3, 767, 9, NULL, NULL),
(2388, 3, 768, 96, NULL, NULL),
(2389, 3, 769, 32, NULL, NULL),
(2390, 3, 770, 6, NULL, NULL),
(2391, 3, 771, 80, NULL, NULL),
(2392, 3, 772, 59, NULL, NULL),
(2393, 3, 773, 31, NULL, NULL),
(2394, 3, 774, 34, NULL, NULL),
(2395, 3, 775, 24, NULL, NULL),
(2396, 3, 776, 64, NULL, NULL),
(2397, 3, 777, 46, NULL, NULL),
(2398, 3, 778, 81, NULL, NULL),
(2399, 3, 779, 46, NULL, NULL),
(2400, 3, 780, 69, NULL, NULL),
(2401, 3, 781, 8, NULL, NULL),
(2402, 3, 782, 42, NULL, NULL),
(2403, 3, 783, 99, NULL, NULL),
(2404, 3, 784, 40, NULL, NULL),
(2405, 3, 785, 67, NULL, NULL),
(2406, 3, 786, 0, NULL, NULL),
(2407, 3, 787, 33, NULL, NULL),
(2408, 3, 788, 72, NULL, NULL),
(2409, 3, 789, 5, NULL, NULL),
(2410, 3, 790, 92, NULL, NULL),
(2411, 3, 791, 5, NULL, NULL),
(2412, 3, 792, 11, NULL, NULL),
(2413, 3, 793, 22, NULL, NULL),
(2414, 3, 794, 10, NULL, NULL),
(2415, 3, 795, 79, NULL, NULL),
(2416, 3, 796, 46, NULL, NULL),
(2417, 3, 797, 96, NULL, NULL),
(2418, 3, 798, 52, NULL, NULL),
(2419, 3, 799, 60, NULL, NULL),
(2420, 3, 800, 33, NULL, NULL),
(2421, 3, 801, 21, NULL, NULL),
(2422, 3, 802, 49, NULL, NULL),
(2423, 3, 803, 1, NULL, NULL),
(2424, 3, 804, 57, NULL, NULL),
(2425, 3, 805, 17, NULL, NULL),
(2426, 3, 806, 61, NULL, NULL),
(2427, 3, 807, 28, NULL, NULL),
(2428, 3, 808, 77, NULL, NULL),
(2429, 3, 809, 96, NULL, NULL),
(2430, 3, 810, 100, NULL, NULL);

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
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `combo_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item_toppings`
--

CREATE TABLE `cart_item_toppings` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_item_id` bigint UNSIGNED NOT NULL,
  `topping_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(1, 'Burger', 'Burger với nhiều lớp nhân thịt và rau củ tươi ngon', 'categories/burger.jpg', 1, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(2, 'Pizza', 'Pizza đa dạng hương vị', 'categories/pizza.jpg', 1, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(3, 'Gà Rán', 'Gà rán giòn rụm, thơm ngon', 'categories/chicken.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(4, 'Cơm', 'Các món cơm đặc sắc', 'categories/rice.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(5, 'Mì', 'Các loại mì ngon', 'categories/noodles.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(6, 'Đồ Uống', 'Đồ uống giải khát', 'categories/drinks.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17');

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
-- Table structure for table `combos`
--

CREATE TABLE `combos` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `combos`
--

INSERT INTO `combos` (`id`, `name`, `image`, `description`, `price`, `active`, `created_at`, `updated_at`) VALUES
(1, 'et at', 'https://via.placeholder.com/640x480.png/00ff55?text=food+sed', 'Consequatur qui culpa consequatur amet.', '315351.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(2, 'dolores pariatur', 'https://via.placeholder.com/640x480.png/00ff55?text=food+aspernatur', 'In est qui eum suscipit.', '337818.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(3, 'sit animi', 'https://via.placeholder.com/640x480.png/00ff55?text=food+beatae', 'Libero est praesentium aut quos voluptate itaque.', '140844.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(4, 'saepe natus', 'https://via.placeholder.com/640x480.png/00ff55?text=food+quia', 'Ducimus repudiandae at sapiente.', '346899.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(5, 'amet iure', 'https://via.placeholder.com/640x480.png/00ff55?text=food+quia', 'Est aut aliquam corrupti voluptas.', '445434.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(6, 'rerum alias', 'https://via.placeholder.com/640x480.png/00ff55?text=food+eos', 'Sint quae aut ipsa sapiente cum quam quis neque.', '166657.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(7, 'repellendus labore', 'https://via.placeholder.com/640x480.png/00ff55?text=food+possimus', 'Sed ducimus officiis nobis quas eveniet quidem.', '144537.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(8, 'similique voluptas', 'https://via.placeholder.com/640x480.png/00ff55?text=food+et', 'Doloribus sed debitis sed consequuntur id amet dignissimos.', '430217.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(9, 'error temporibus', 'https://via.placeholder.com/640x480.png/00ff55?text=food+voluptatem', 'Ex voluptatibus recusandae et tempore.', '309120.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(10, 'qui harum', 'https://via.placeholder.com/640x480.png/00ff55?text=food+aliquid', 'Voluptates harum mollitia necessitatibus natus aut.', '149883.00', 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26');

-- --------------------------------------------------------

--
-- Table structure for table `combo_items`
--

CREATE TABLE `combo_items` (
  `id` bigint UNSIGNED NOT NULL,
  `combo_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `combo_items`
--

INSERT INTO `combo_items` (`id`, `combo_id`, `product_variant_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 643, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(2, 1, 119, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(3, 1, 536, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(4, 2, 478, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(5, 2, 55, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(6, 3, 789, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(7, 3, 391, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(8, 3, 687, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(9, 4, 47, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(10, 4, 480, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(11, 4, 772, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(12, 4, 389, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(13, 5, 39, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(14, 5, 396, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(15, 6, 106, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(16, 6, 672, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(17, 7, 786, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(18, 7, 186, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(19, 7, 147, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(20, 7, 256, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(21, 8, 107, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(22, 8, 623, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(23, 8, 321, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(24, 8, 206, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(25, 9, 356, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(26, 9, 472, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(27, 9, 70, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(28, 9, 172, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(29, 10, 245, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(30, 10, 145, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(31, 10, 504, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(32, 10, 754, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` enum('percentage','fixed_amount','free_shipping') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `max_discount_amount` decimal(12,2) DEFAULT NULL,
  `applicable_scope` enum('all_branches','specific_branches') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all_branches',
  `applicable_items` enum('all_items','specific_products','specific_categories','combos_only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all_items',
  `applicable_ranks` json DEFAULT NULL,
  `rank_exclusive` tinyint(1) NOT NULL DEFAULT '0',
  `valid_days_of_week` json DEFAULT NULL,
  `valid_from_time` time DEFAULT NULL,
  `valid_to_time` time DEFAULT NULL,
  `usage_type` enum('public','personal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `max_total_usage` int DEFAULT NULL,
  `max_usage_per_user` int NOT NULL DEFAULT '1',
  `current_usage_count` int NOT NULL DEFAULT '0',
  `start_date` timestamp NOT NULL,
  `end_date` timestamp NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_code_branches`
--

CREATE TABLE `discount_code_branches` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_code_products`
--

CREATE TABLE `discount_code_products` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `combo_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_usage_history`
--

CREATE TABLE `discount_usage_history` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `guest_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application_id` bigint UNSIGNED NOT NULL,
  `license_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_registration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `current_latitude` decimal(10,7) DEFAULT NULL,
  `current_longitude` decimal(10,7) DEFAULT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `cancellation_count` int NOT NULL DEFAULT '0',
  `reliability_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `penalty_count` int NOT NULL DEFAULT '0',
  `auto_deposit_earnings` tinyint(1) NOT NULL DEFAULT '0',
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `email`, `password`, `full_name`, `phone_number`, `application_id`, `license_number`, `vehicle_type`, `vehicle_registration`, `vehicle_color`, `status`, `is_available`, `current_latitude`, `current_longitude`, `balance`, `rating`, `cancellation_count`, `reliability_score`, `penalty_count`, `auto_deposit_earnings`, `otp`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'wiza.consuelo@example.org', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Tavares Schumm', '458-364-5365', 6, 'DL5117059554', 'motorcycle', 'kn-228-16', 'GreenYellow', 'active', 1, '-58.7890550', '102.4317730', '599.84', '3.09', 6, '99.00', 0, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(2, 'fmarquardt@example.org', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Mr. Cory Wiegand DDS', '1-351-933-7538', 3, 'DL3890327614', 'motorcycle', 'vr-269-35', 'LavenderBlush', 'active', 1, '-52.5550930', '53.2313940', '73.88', '4.89', 9, '100.00', 0, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(3, 'jpowlowski@example.com', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Amber Frami', '+1.816.566.4387', 5, 'DL4265587766', 'motorcycle', 'cv-214-17', 'Gainsboro', 'active', 1, '75.0206870', '-44.7961750', '589.53', '4.59', 10, '97.00', 4, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(4, 'koch.kimberly@example.net', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Aleen Kiehn', '689.579.8657', 9, 'DL8994159155', 'motorcycle', 'ao-377-41', 'PaleGreen', 'active', 1, '36.4476760', '-59.8382300', '421.77', '3.44', 0, '93.00', 0, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(5, 'holly05@example.org', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Anjali Trantow', '+1 (463) 480-0852', 7, 'DL9552422090', 'bicycle', 'oz-862-94', 'LightGoldenRodYellow', 'active', 1, '39.1229780', '-96.5729970', '998.21', '3.79', 7, '100.00', 0, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(6, 'bailey08@example.net', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Yvonne Wuckert', '928.349.2813', 10, 'DL2180354476', 'motorcycle', 'yw-454-54', 'Turquoise', 'active', 1, '-19.7685800', '176.6139660', '550.53', '4.18', 5, '71.00', 5, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(7, 'makenzie99@example.net', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Haleigh Lowe', '(918) 693-9743', 9, 'DL8470735328', 'motorcycle', 'ao-377-41', 'PaleGreen', 'active', 1, '23.2488530', '-166.0582920', '668.05', '3.60', 0, '93.00', 0, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(8, 'lacey.toy@example.com', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Prof. Darrion Strosin II', '+18657864725', 8, 'DL6890045867', 'car', 'ls-523-40', 'Linen', 'active', 1, '-31.4821230', '-96.1463380', '905.89', '4.17', 0, '81.00', 2, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(9, 'wbreitenberg@example.org', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Dakota Wehner', '1-878-745-0444', 8, 'DL9762946489', 'car', 'ls-523-40', 'Linen', 'inactive', 1, '32.4404930', '56.4297870', '381.84', '4.60', 9, '75.00', 5, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(10, 'stehr.wilma@example.org', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', 'Meagan Mohr II', '+1-385-570-6914', 9, 'DL0064854970', 'motorcycle', 'ao-377-41', 'PaleGreen', 'inactive', 1, '-18.2019120', '44.5562290', '644.20', '3.18', 0, '75.00', 5, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `driver_applications`
--

CREATE TABLE `driver_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_card_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_card_issue_date` date NOT NULL,
  `id_card_issue_place` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` enum('motorcycle','car','bicycle') COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_model` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_plate` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_license_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_card_front_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_card_back_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_license_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_registration_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact_relationship` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_applications`
--

INSERT INTO `driver_applications` (`id`, `full_name`, `email`, `phone_number`, `date_of_birth`, `gender`, `id_card_number`, `id_card_issue_date`, `id_card_issue_place`, `address`, `city`, `district`, `vehicle_type`, `vehicle_model`, `vehicle_color`, `license_plate`, `driver_license_number`, `id_card_front_image`, `id_card_back_image`, `driver_license_image`, `profile_image`, `vehicle_registration_image`, `bank_name`, `bank_account_number`, `bank_account_name`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'Enoch Upton', 'zpowlowski@example.net', '+84 819869666', '1983-09-05', 'other', '3029304467', '2015-03-10', 'East Kristianhaven', '90473 Mireille Expressway Suite 177\nLake Brenna, IN 84922', 'Port Brionnaborough', 'consequatur', 'motorcycle', 'sunt', 'SkyBlue', 'sz-573-62', 'DL9223115926', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Crist-Sauer', '88052281443093', 'Dr. Jany Rice I', 'Bernhard Bayer', '+84 339073077', 'parent', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(2, 'Fermin O\'Kon', 'berniece62@example.org', '+84 368700980', '1994-05-07', 'other', '5566703400', '2008-09-02', 'Carterview', '93160 Ola Gardens\nLednerland, NC 45593-4937', 'Lake Alanaburgh', 'esse', 'bicycle', 'error', 'FireBrick', 'sh-574-41', 'DL6956344931', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Durgan, Marks and Schamberger', '30145144383210', 'Marcella Watsica', 'Prof. Nakia Windler MD', '+84 697882876', 'parent', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(3, 'Miss Alvena Senger I', 'pablo.dooley@example.org', '+84 124685499', '1992-10-27', 'male', '6594876730', '1974-04-07', 'Ullrichside', '5345 Reinger Via\nWest Jevonmouth, WV 91065-7163', 'New Pansy', 'expedita', 'motorcycle', 'aut', 'LavenderBlush', 'vr-269-35', 'DL0934761743', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Turner Ltd', '15967191061227', 'Lorena Ruecker', 'Cordie Kulas', '+84 529817751', 'spouse', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(4, 'Dr. Ruby Fadel', 'wrenner@example.com', '+84 174738452', '1992-01-17', 'male', '8318544102', '2016-06-11', 'New Deshawnstad', '7864 Elsie Fall\nReaganborough, KS 72836', 'East Edytheport', 'voluptates', 'motorcycle', 'et', 'AliceBlue', 'gu-585-93', 'DL5604465991', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Braun, Nader and Stiedemann', '91845934418657', 'Vaughn Kohler', 'Marisol Emard DDS', '+84 581685216', 'parent', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(5, 'Greg Kub', 'zblanda@example.com', '+84 517053437', '1994-08-30', 'male', '4121724481', '1998-02-20', 'Lake Benny', '7441 Arne Cove\nWest Shannonmouth, AL 45151', 'West Karl', 'voluptas', 'motorcycle', 'repellendus', 'Gainsboro', 'cv-214-17', 'DL2651042501', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Hintz, Mann and Krajcik', '77297472183995', 'Hannah Walter', 'Miss Pasquale Zieme Jr.', '+84 635139818', 'parent', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(6, 'Mr. Einar Miller', 'boris18@example.com', '+84 685609758', '1989-09-29', 'other', '0919985845', '1976-06-26', 'Lake Lamont', '44367 Balistreri Extension Suite 043\nVonRuedenbury, LA 37472', 'New Grayson', 'qui', 'motorcycle', 'necessitatibus', 'GreenYellow', 'kn-228-16', 'DL4718261178', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Streich LLC', '44020310148522', 'Allan Pollich Jr.', 'Kurt Barrows', '+84 891280139', 'friend', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(7, 'Rosanna Beatty', 'joy.ferry@example.org', '+84 761056493', '2006-01-22', 'male', '4509712212', '1981-01-18', 'South Lorine', '84742 Jayce Parkways Apt. 608\nNew Sarinaside, NM 19878', 'Jacobsfurt', 'eligendi', 'bicycle', 'sunt', 'LightGoldenRodYellow', 'oz-862-94', 'DL4290717543', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'O\'Hara Inc', '23164029257964', 'Dovie Ankunding', 'Carey Stanton III', '+84 276137491', 'spouse', 'approved', 'Dolores quia dolorum hic voluptas eaque porro et.', '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(8, 'Friedrich Predovic', 'ajacobs@example.net', '+84 674565969', '1973-05-08', 'female', '3063664036', '1978-11-29', 'Altenwerthburgh', '6163 Kunze Walks Suite 576\nGoldnerhaven, IL 31758-5269', 'Juwanbury', 'aliquam', 'car', 'ut', 'Linen', 'ls-523-40', 'DL3184179621', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Schroeder-Schultz', '57304279364255', 'Katarina Prosacco', 'Dr. Darrel Schneider I', '+84 254507574', 'spouse', 'approved', 'Aliquid quibusdam provident dignissimos nulla quae.', '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(9, 'Gladys Jaskolski II', 'ferry.velma@example.com', '+84 230474443', '1977-05-01', 'other', '9378352744', '2018-05-09', 'Ronaldostad', '86318 Demario Spring Suite 209\nKingtown, MN 06660', 'East Itzel', 'sed', 'motorcycle', 'aut', 'PaleGreen', 'ao-377-41', 'DL0850342438', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Reynolds PLC', '99889460435094', 'Prof. Elijah Kuphal DVM', 'Chelsea Ryan', '+84 446934507', 'sibling', 'approved', 'Animi id et quia minus ullam quos.', '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(10, 'Zachery Maggio', 'wbeahan@example.com', '+84 539343171', '1989-02-04', 'male', '0230399259', '2001-06-29', 'East Tessmouth', '3905 Rice Green Suite 822\nTrantowborough, MD 29510', 'West Virginieville', 'eos', 'motorcycle', 'non', 'Turquoise', 'yw-454-54', 'DL8556333635', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Armstrong and Sons', '40833648364951', 'Mrs. Eula Kub', 'Mona Auer', '+84 400497018', 'friend', 'approved', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(11, 'Shany Raynor', 'kozey.kian@example.com', '+84 466543622', '1993-04-27', 'female', '9061637698', '2014-12-29', 'North Amy', '268 Anderson Isle Apt. 283\nSchadenton, HI 07030-7038', 'North Julia', 'exercitationem', 'bicycle', 'ducimus', 'PaleTurquoise', 'th-460-42', 'DL0205535589', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Keeling, Haley and Towne', '63731818319343', 'Xavier Robel', 'Kyla Goldner PhD', '+84 643899294', 'sibling', 'pending', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(12, 'Tad Cassin', 'turcotte.nyah@example.net', '+84 469366459', '1984-12-26', 'female', '9153432090', '1987-12-05', 'South Betteside', '9248 Cordell Rue\nSouth Irma, SD 85713', 'Port Judd', 'illo', 'car', 'sed', 'MediumBlue', 'ub-559-83', 'DL7003527404', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Marvin PLC', '44308598694188', 'Prof. Hiram Torp', 'Reece Koch', '+84 325498026', 'friend', 'pending', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(13, 'Brooks King', 'joan50@example.net', '+84 355618904', '1978-10-01', 'other', '5475316256', '1980-09-27', 'Halmouth', '220 Garnett Highway\nLake Hellentown, GA 51864', 'Michalefurt', 'veniam', 'bicycle', 'placeat', 'Crimson', 'fr-900-61', 'DL7243597720', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Maggio, Collier and Howell', '84527484438257', 'Katelyn Kris', 'Dr. Boyd Stehr', '+84 935635795', 'spouse', 'pending', 'Aut dolores pariatur quia dolor veniam laborum itaque.', '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(14, 'Kelsie Hagenes', 'royal92@example.net', '+84 341415001', '1970-09-02', 'other', '7468907581', '1989-09-02', 'West Myrtle', '6992 Dandre Mission Apt. 205\nNorth Abigaleshire, WV 25287', 'Kentonborough', 'harum', 'bicycle', 'dicta', 'Coral', 'wa-320-90', 'DL3538326290', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Bosco-Mueller', '17649526719879', 'Mrs. Leilani Murphy', 'Ali Legros DDS', '+84 800245320', 'sibling', 'pending', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(15, 'Jimmie Herzog', 'alicia96@example.net', '+84 147867468', '1972-10-23', 'male', '5721694626', '1974-12-29', 'North Evan', '4441 Beer Ferry\nWest Arianestad, NM 87317', 'South Vanessaburgh', 'non', 'car', 'aut', 'FloralWhite', 'tv-319-21', 'DL8412059256', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Huel, Feeney and Erdman', '12224715774975', 'Marco Crona PhD', 'Ms. Trisha Green', '+84 630300225', 'friend', 'pending', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(16, 'Benjamin Mitchell', 'renee.williamson@example.com', '+84 331697085', '1981-05-24', 'male', '2478485411', '1971-05-14', 'Fadelside', '3880 Muller Loop\nLedaview, NH 94553', 'North Simonetown', 'mollitia', 'bicycle', 'ut', 'Gainsboro', 'if-129-50', 'DL5786380196', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'McGlynn-Gaylord', '11425555845219', 'Dr. Bonita Stanton', 'Melyna McGlynn', '+84 180387351', 'sibling', 'rejected', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(17, 'Missouri Wisoky', 'qmills@example.net', '+84 236347257', '1990-08-02', 'female', '0773209763', '2001-04-16', 'New Quinn', '824 Ana Crescent Suite 161\nDaronville, MA 74761', 'West Casimerport', 'natus', 'motorcycle', 'explicabo', 'DarkGoldenRod', 'mr-665-84', 'DL3505579665', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Gerhold, Mayert and Hansen', '90332704578075', 'Jordi Botsford', 'Dr. Shana Mills PhD', '+84 782204050', 'sibling', 'rejected', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(18, 'Adelbert Kulas', 'carolanne47@example.com', '+84 280180186', '1992-04-23', 'other', '9532155570', '2007-04-15', 'Stammhaven', '2461 Karlee Cliff Apt. 527\nNorth Gwendolynview, IL 90169', 'South Corrinefort', 'quae', 'car', 'quam', 'SeaGreen', 'ia-995-49', 'DL1054113094', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Dach LLC', '72093061938796', 'Lillian Blanda', 'Allie Cremin', '+84 952656648', 'sibling', 'rejected', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(19, 'Randall Gibson', 'dolly75@example.org', '+84 392840727', '1992-12-01', 'female', '2270009959', '1984-07-05', 'Rogahnland', '56736 Alvis Circles\nErdmanfurt, NC 97292-1349', 'Thielborough', 'quis', 'motorcycle', 'sit', 'Fuchsia', 'ma-090-35', 'DL7272330597', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Smitham-Nienow', '69883314287182', 'Neil Wolff', 'Dr. Moshe Swaniawski', '+84 620352304', 'parent', 'rejected', 'Fugiat officiis officia pariatur.', '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(20, 'Ethan Feeney', 'rowena.hackett@example.net', '+84 444351069', '1998-07-24', 'male', '9666692945', '1985-04-20', 'Lake Mollie', '559 Juliet Ville Apt. 333\nEast Laishamouth, HI 84062-3481', 'Braunbury', 'quia', 'car', 'ea', 'AntiqueWhite', 'ht-652-04', 'DL4144322219', 'images/id_cards/front_default.jpg', 'images/id_cards/back_default.jpg', 'images/licenses/default.jpg', 'images/profiles/default.jpg', 'images/vehicles/default.jpg', 'Bosco, Feest and Brakus', '67476518170561', 'Skyla Stanton', 'Dr. Vergie Rodriguez', '+84 187701252', 'parent', 'rejected', NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16');

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
(1, '2025_01_01_000001_create_roles_table', 1),
(2, '2025_01_01_000001_create_user_ranks_table', 1),
(3, '2025_01_01_000002_create_users_table', 1),
(4, '2025_01_01_000003_create_user_roles_table', 1),
(5, '2025_01_01_000004_create_categories_table', 1),
(6, '2025_01_01_000005_create_products_table', 1),
(7, '2025_01_01_000006_create_product_imgs_table', 1),
(8, '2025_01_01_000007_create_product_variants_table', 1),
(9, '2025_01_01_000008_create_addresses_table', 1),
(10, '2025_01_01_000009_create_combos_table', 1),
(11, '2025_01_01_000010_create_banners_table', 1),
(12, '2025_01_01_000011_create_payments_table', 1),
(13, '2025_01_01_000012_create_carts_table', 1),
(14, '2025_01_01_000013_create_branches_table', 1),
(15, '2025_01_01_000014_create_branch_images_table', 1),
(16, '2025_01_01_000015_create_branch_stocks_table', 1),
(17, '2025_01_01_000016_create_driver_applications_table', 1),
(18, '2025_01_01_000017_create_drivers_table', 1),
(19, '2025_01_01_000019_create_discount_codes_table', 1),
(20, '2025_01_01_000020_create_point_rules_table', 1),
(21, '2025_01_01_000021_create_cache_table', 1),
(22, '2025_01_01_000022_create_jobs_table', 1),
(23, '2025_01_01_000023_create_orders_table', 1),
(24, '2025_01_01_000024_create_toppings_table', 1),
(25, '2025_01_01_000025_create_order_status_histories_table', 1),
(26, '2025_01_01_000026_create_order_cancellations_table', 1),
(27, '2025_01_01_000027_create_product_reviews_table', 1),
(28, '2025_01_01_000028_create_points_transactions_table', 1),
(29, '2025_01_01_000029_create_review_replies_table', 1),
(30, '2025_01_01_000030_create_return_orders_table', 1),
(31, '2025_01_01_000031_create_chat_messages_table', 1),
(32, '2025_01_01_000032_create_wishlist_items_table', 1),
(33, '2025_01_01_000033_create_contacts_table', 1),
(34, '2025_05_31_040917_create_promotion_programs_table', 1),
(35, '2025_05_31_040922_create_promotion_discount_codes_table', 1),
(36, '2025_05_31_040925_create_user_discount_codes_table', 1),
(37, '2025_05_31_040928_create_discount_code_branches_table', 1),
(38, '2025_05_31_040932_create_promotion_branches_table', 1),
(39, '2025_05_31_040934_create_discount_code_products_table', 1),
(40, '2025_05_31_040937_create_discount_usage_history_table', 1),
(41, '2025_05_31_040941_create_user_rank_history_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `address_id` bigint UNSIGNED DEFAULT NULL,
  `discount_code_id` bigint UNSIGNED DEFAULT NULL,
  `payment_id` bigint UNSIGNED DEFAULT NULL,
  `guest_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_ward` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_district` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_latitude` decimal(10,8) DEFAULT NULL,
  `guest_longitude` decimal(11,8) DEFAULT NULL,
  `estimated_delivery_time` timestamp NULL DEFAULT NULL,
  `actual_delivery_time` timestamp NULL DEFAULT NULL,
  `delivery_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_date` timestamp NULL DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points_earned` int NOT NULL DEFAULT '0',
  `subtotal` decimal(12,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `combo_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_toppings`
--

CREATE TABLE `order_item_toppings` (
  `id` bigint UNSIGNED NOT NULL,
  `order_item_id` bigint UNSIGNED NOT NULL,
  `topping_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_status_histories`
--

CREATE TABLE `order_status_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `old_status` enum('new','processing','ready','delivery','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` enum('new','processing','ready','delivery','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `changed_by_role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  `payer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `txn_ref` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_amount` bigint UNSIGNED NOT NULL,
  `payment_currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `payment_status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_date` datetime DEFAULT NULL,
  `payment_method_detail` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_response` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_data` text COLLATE utf8mb4_unicode_ci,
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
  `description` text COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `point_rules`
--

CREATE TABLE `point_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `point_per_currency` decimal(10,4) NOT NULL DEFAULT '0.0100',
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_type` enum('all','regular','vip') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
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
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_price` decimal(12,2) NOT NULL,
  `preparation_time` int DEFAULT NULL,
  `ingredients` json DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('coming_soon','selling','discontinued') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'selling',
  `release_at` timestamp NULL DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `base_price`, `preparation_time`, `ingredients`, `short_description`, `description`, `status`, `release_at`, `is_featured`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'BG-2DmXs', 'Burger Bò Phô Mai', '47180.00', 23, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"],\\\"cheese\\\":[\\\"Ph\\\\u00f4 mai Mozzarella\\\",\\\"Ph\\\\u00f4 mai Cheddar\\\"]}\"', 'Món Burger Bò Phô Mai đặc biệt', 'Đây là món Burger Bò Phô Mai ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(2, 1, 'BG-x2Kqy', 'Burger Gà Giòn', '72231.00', 16, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Burger Gà Giòn đặc biệt', 'Đây là món Burger Gà Giòn ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(3, 1, 'BG-z9Yoi', 'Burger Cá', '187211.00', 17, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"]}\"', 'Món Burger Cá đặc biệt', 'Đây là món Burger Cá ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(4, 1, 'BG-kJMib', 'Burger Bò Nướng BBQ', '184696.00', 18, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"],\\\"sauce\\\":[\\\"S\\\\u1ed1t BBQ\\\",\\\"S\\\\u1ed1t ti\\\\u00eau \\\\u0111en\\\"]}\"', 'Món Burger Bò Nướng BBQ đặc biệt', 'Đây là món Burger Bò Nướng BBQ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(5, 1, 'BG-n0gnJ', 'Burger Tôm', '107144.00', 15, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"]}\"', 'Món Burger Tôm đặc biệt', 'Đây là món Burger Tôm ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(6, 1, 'BG-MOM3l', 'Burger Bò 2 Lớp', '79318.00', 27, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Burger Bò 2 Lớp đặc biệt', 'Đây là món Burger Bò 2 Lớp ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(7, 1, 'BG-a7ojg', 'Burger Gà Nướng', '129423.00', 15, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Burger Gà Nướng đặc biệt', 'Đây là món Burger Gà Nướng ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(8, 1, 'BG-xp4Xk', 'Burger Bò Trứng', '185770.00', 30, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Burger Bò Trứng đặc biệt', 'Đây là món Burger Bò Trứng ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(9, 1, 'BG-oKwrN', 'Burger Phô Mai', '146527.00', 20, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"cheese\\\":[\\\"Ph\\\\u00f4 mai Mozzarella\\\",\\\"Ph\\\\u00f4 mai Cheddar\\\"]}\"', 'Món Burger Phô Mai đặc biệt', 'Đây là món Burger Phô Mai ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(10, 1, 'BG-NhaIU', 'Burger Bò Xông Khói', '151109.00', 17, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt x\\\\u00f4ng kh\\\\u00f3i\\\",\\\"Bacon\\\"]}\"', 'Món Burger Bò Xông Khói đặc biệt', 'Đây là món Burger Bò Xông Khói ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(11, 1, 'BG-OUhiL', 'Burger Gà Phô Mai', '59886.00', 26, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"],\\\"cheese\\\":[\\\"Ph\\\\u00f4 mai Mozzarella\\\",\\\"Ph\\\\u00f4 mai Cheddar\\\"]}\"', 'Món Burger Gà Phô Mai đặc biệt', 'Đây là món Burger Gà Phô Mai ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(12, 1, 'BG-vffSj', 'Burger Cá Ngừ', '153434.00', 15, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"]}\"', 'Món Burger Cá Ngừ đặc biệt', 'Đây là món Burger Cá Ngừ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(13, 1, 'BG-BJd6Z', 'Burger Bò Teriyaki', '128169.00', 16, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Burger Bò Teriyaki đặc biệt', 'Đây là món Burger Bò Teriyaki ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(14, 1, 'BG-fEOl2', 'Burger Gà Sốt Cay', '72695.00', 13, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"],\\\"spices\\\":[\\\"\\\\u1edat t\\\\u01b0\\\\u01a1i\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\",\\\"S\\\\u1ed1t cay\\\"]}\"', 'Món Burger Gà Sốt Cay đặc biệt', 'Đây là món Burger Gà Sốt Cay ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(15, 1, 'BG-XXVAl', 'Burger Bò Deluxe', '71886.00', 29, '\"{\\\"base\\\":[\\\"B\\\\u00e1nh m\\\\u00ec burger\\\"],\\\"vegetables\\\":[\\\"X\\\\u00e0 l\\\\u00e1ch\\\",\\\"C\\\\u00e0 chua\\\",\\\"D\\\\u01b0a chu\\\\u1ed9t\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"sauces\\\":[\\\"S\\\\u1ed1t mayonnaise\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Burger Bò Deluxe đặc biệt', 'Đây là món Burger Bò Deluxe ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(16, 2, 'PZ-pc0SP', 'Pizza Hải Sản', '185693.00', 20, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"seafood\\\":[\\\"T\\\\u00f4m\\\",\\\"M\\\\u1ef1c\\\",\\\"C\\\\u00e1 h\\\\u1ed3i\\\"]}\"', 'Món Pizza Hải Sản đặc biệt', 'Đây là món Pizza Hải Sản ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(17, 2, 'PZ-foFZC', 'Pizza Bò', '40123.00', 30, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Pizza Bò đặc biệt', 'Đây là món Pizza Bò ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(18, 2, 'PZ-9zPTG', 'Pizza Gà', '95887.00', 27, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Pizza Gà đặc biệt', 'Đây là món Pizza Gà ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(19, 2, 'PZ-VjKd2', 'Pizza Xúc Xích', '43135.00', 15, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Xúc Xích đặc biệt', 'Đây là món Pizza Xúc Xích ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(20, 2, 'PZ-78GM2', 'Pizza Phô Mai', '172724.00', 13, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"cheese\\\":[\\\"Ph\\\\u00f4 mai Mozzarella\\\",\\\"Ph\\\\u00f4 mai Cheddar\\\"]}\"', 'Món Pizza Phô Mai đặc biệt', 'Đây là món Pizza Phô Mai ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(21, 2, 'PZ-rp1Zf', 'Pizza Nấm', '178230.00', 28, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Nấm đặc biệt', 'Đây là món Pizza Nấm ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(22, 2, 'PZ-kU1MS', 'Pizza Thịt Nguội', '124810.00', 13, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Thịt Nguội đặc biệt', 'Đây là món Pizza Thịt Nguội ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(23, 2, 'PZ-SXUhg', 'Pizza Hawaii', '45334.00', 28, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Hawaii đặc biệt', 'Đây là món Pizza Hawaii ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(24, 2, 'PZ-a2B2X', 'Pizza 5 Loại Thịt', '162466.00', 15, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza 5 Loại Thịt đặc biệt', 'Đây là món Pizza 5 Loại Thịt ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(25, 2, 'PZ-rUAai', 'Pizza Rau Củ', '83284.00', 19, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Rau Củ đặc biệt', 'Đây là món Pizza Rau Củ ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(26, 2, 'PZ-YseGP', 'Pizza Bò BBQ', '152486.00', 20, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"],\\\"sauce\\\":[\\\"S\\\\u1ed1t BBQ\\\",\\\"S\\\\u1ed1t ti\\\\u00eau \\\\u0111en\\\"]}\"', 'Món Pizza Bò BBQ đặc biệt', 'Đây là món Pizza Bò BBQ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(27, 2, 'PZ-neEhM', 'Pizza Gà Nướng', '71982.00', 20, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Pizza Gà Nướng đặc biệt', 'Đây là món Pizza Gà Nướng ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(28, 2, 'PZ-eeItp', 'Pizza Hải Sản Cao Cấp', '45813.00', 13, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"],\\\"seafood\\\":[\\\"T\\\\u00f4m\\\",\\\"M\\\\u1ef1c\\\",\\\"C\\\\u00e1 h\\\\u1ed3i\\\"]}\"', 'Món Pizza Hải Sản Cao Cấp đặc biệt', 'Đây là món Pizza Hải Sản Cao Cấp ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(29, 2, 'PZ-Bp2ng', 'Pizza Thập Cẩm', '127715.00', 23, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Thập Cẩm đặc biệt', 'Đây là món Pizza Thập Cẩm ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(30, 2, 'PZ-UMcZL', 'Pizza Margherita', '192222.00', 10, '\"{\\\"base\\\":[\\\"\\\\u0110\\\\u1ebf b\\\\u00e1nh pizza\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\",\\\"Ph\\\\u00f4 mai Mozzarella\\\"],\\\"vegetables\\\":[\\\"\\\\u1edat chu\\\\u00f4ng\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"herbs\\\":[\\\"H\\\\u00fang qu\\\\u1ebf\\\",\\\"Oregano\\\"]}\"', 'Món Pizza Margherita đặc biệt', 'Đây là món Pizza Margherita ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(31, 3, 'GR-eGMbE', 'Gà Rán Giòn', '114747.00', 19, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"B\\\\u1ed9t t\\\\u1ecfi\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Gà Rán Giòn đặc biệt', 'Đây là món Gà Rán Giòn ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(32, 3, 'GR-NUyqF', 'Gà Sốt Cay', '139355.00', 21, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Sốt Cay đặc biệt', 'Đây là món Gà Sốt Cay ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(33, 3, 'GR-uSs2p', 'Gà Sốt BBQ', '143217.00', 11, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Sốt BBQ đặc biệt', 'Đây là món Gà Sốt BBQ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(34, 3, 'GR-x6JnY', 'Gà Không Xương', '161652.00', 13, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Không Xương đặc biệt', 'Đây là món Gà Không Xương ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(35, 3, 'GR-szypo', 'Gà Rán Phô Mai', '134389.00', 14, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"B\\\\u1ed9t t\\\\u1ecfi\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"],\\\"cheese\\\":[\\\"Ph\\\\u00f4 mai Mozzarella\\\",\\\"Ph\\\\u00f4 mai Cheddar\\\"]}\"', 'Món Gà Rán Phô Mai đặc biệt', 'Đây là món Gà Rán Phô Mai ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(36, 3, 'GR-ya1Nz', 'Gà Sốt Teriyaki', '198206.00', 26, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Sốt Teriyaki đặc biệt', 'Đây là món Gà Sốt Teriyaki ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(37, 3, 'GR-ylnXK', 'Gà Rán Mật Ong', '100819.00', 11, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"B\\\\u1ed9t t\\\\u1ecfi\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Gà Rán Mật Ong đặc biệt', 'Đây là món Gà Rán Mật Ong ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(38, 3, 'GR-xmKwN', 'Gà Sốt Tỏi', '122857.00', 18, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Sốt Tỏi đặc biệt', 'Đây là món Gà Sốt Tỏi ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(39, 3, 'GR-R6jTC', 'Gà Rán Original', '171994.00', 12, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"B\\\\u1ed9t t\\\\u1ecfi\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Gà Rán Original đặc biệt', 'Đây là món Gà Rán Original ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(40, 3, 'GR-TvIuI', 'Gà Sốt Cay Ngọt', '78072.00', 17, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Sốt Cay Ngọt đặc biệt', 'Đây là món Gà Sốt Cay Ngọt ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(41, 3, 'GR-sKGvN', 'Gà Rán Giòn Cay', '94996.00', 12, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"\\\\u1edat t\\\\u01b0\\\\u01a1i\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\",\\\"S\\\\u1ed1t cay\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Gà Rán Giòn Cay đặc biệt', 'Đây là món Gà Rán Giòn Cay ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(42, 3, 'GR-KRVws', 'Gà Nướng BBQ', '65865.00', 21, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Nướng BBQ đặc biệt', 'Đây là món Gà Nướng BBQ ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(43, 3, 'GR-Lvohu', 'Gà Sốt Phô Mai', '188601.00', 14, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Gà Sốt Phô Mai đặc biệt', 'Đây là món Gà Sốt Phô Mai ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(44, 3, 'GR-C8ApQ', 'Gà Rán Không Cay', '86958.00', 19, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"\\\\u1edat t\\\\u01b0\\\\u01a1i\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\",\\\"S\\\\u1ed1t cay\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Gà Rán Không Cay đặc biệt', 'Đây là món Gà Rán Không Cay ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(45, 3, 'GR-ckZQB', 'Gà Rán Sốt Đặc Biệt', '107692.00', 10, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"B\\\\u1ed9t t\\\\u1ecfi\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Gà Rán Sốt Đặc Biệt đặc biệt', 'Đây là món Gà Rán Sốt Đặc Biệt ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(46, 4, 'CM-bYTEf', 'Cơm Gà Rán', '55379.00', 13, '\"{\\\"main\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 t\\\\u01b0\\\\u01a1i\\\"],\\\"coating\\\":[\\\"B\\\\u1ed9t chi\\\\u00ean x\\\\u00f9\\\",\\\"B\\\\u1ed9t gia v\\\\u1ecb\\\",\\\"D\\\\u1ea7u chi\\\\u00ean\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"B\\\\u1ed9t t\\\\u1ecfi\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Gà Rán đặc biệt', 'Đây là món Cơm Gà Rán ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(47, 4, 'CM-Yk9NV', 'Cơm Bò Lúc Lắc', '52461.00', 13, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Cơm Bò Lúc Lắc đặc biệt', 'Đây là món Cơm Bò Lúc Lắc ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(48, 4, 'CM-XXX2f', 'Cơm Sườn BBQ', '37150.00', 17, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"sauce\\\":[\\\"S\\\\u1ed1t BBQ\\\",\\\"S\\\\u1ed1t ti\\\\u00eau \\\\u0111en\\\"],\\\"meat\\\":[\\\"S\\\\u01b0\\\\u1eddn heo\\\",\\\"S\\\\u01b0\\\\u1eddn non\\\"]}\"', 'Món Cơm Sườn BBQ đặc biệt', 'Đây là món Cơm Sườn BBQ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(49, 4, 'CM-RWCSj', 'Cơm Gà Teriyaki', '175882.00', 16, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Gà Teriyaki đặc biệt', 'Đây là món Cơm Gà Teriyaki ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(50, 4, 'CM-OdRZi', 'Cơm Bò Xào', '128552.00', 21, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Cơm Bò Xào đặc biệt', 'Đây là món Cơm Bò Xào ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(51, 4, 'CM-wyx9K', 'Cơm Gà Xối Mỡ', '139572.00', 11, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Gà Xối Mỡ đặc biệt', 'Đây là món Cơm Gà Xối Mỡ ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(52, 4, 'CM-Hxt1D', 'Cơm Bò BBQ', '111613.00', 24, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"],\\\"sauce\\\":[\\\"S\\\\u1ed1t BBQ\\\",\\\"S\\\\u1ed1t ti\\\\u00eau \\\\u0111en\\\"]}\"', 'Món Cơm Bò BBQ đặc biệt', 'Đây là món Cơm Bò BBQ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(53, 4, 'CM-hQwe8', 'Cơm Sườn Cay', '192968.00', 21, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"\\\\u1edat t\\\\u01b0\\\\u01a1i\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\",\\\"S\\\\u1ed1t cay\\\"],\\\"meat\\\":[\\\"S\\\\u01b0\\\\u1eddn heo\\\",\\\"S\\\\u01b0\\\\u1eddn non\\\"]}\"', 'Món Cơm Sườn Cay đặc biệt', 'Đây là món Cơm Sườn Cay ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(54, 4, 'CM-uzzCF', 'Cơm Gà Nướng', '85107.00', 11, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Gà Nướng đặc biệt', 'Đây là món Cơm Gà Nướng ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(55, 4, 'CM-8EZ7C', 'Cơm Bò Trứng', '84030.00', 25, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Cơm Bò Trứng đặc biệt', 'Đây là món Cơm Bò Trứng ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(56, 4, 'CM-FLLBb', 'Cơm Gà Sốt Cay', '73505.00', 22, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"\\\\u1edat t\\\\u01b0\\\\u01a1i\\\",\\\"B\\\\u1ed9t \\\\u1edbt\\\",\\\"S\\\\u1ed1t cay\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Gà Sốt Cay đặc biệt', 'Đây là món Cơm Gà Sốt Cay ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(57, 4, 'CM-vDQUg', 'Cơm Sườn Nướng', '36154.00', 25, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"S\\\\u01b0\\\\u1eddn heo\\\",\\\"S\\\\u01b0\\\\u1eddn non\\\"]}\"', 'Món Cơm Sườn Nướng đặc biệt', 'Đây là món Cơm Sườn Nướng ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(58, 4, 'CM-50wK8', 'Cơm Bò Nướng', '125377.00', 14, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Cơm Bò Nướng đặc biệt', 'Đây là món Cơm Bò Nướng ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(59, 4, 'CM-2N2EP', 'Cơm Gà Chiên', '35081.00', 22, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Gà Chiên đặc biệt', 'Đây là món Cơm Gà Chiên ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(60, 4, 'CM-Npsf1', 'Cơm Đùi Gà Chiên', '140539.00', 19, '\"{\\\"base\\\":[\\\"C\\\\u01a1m tr\\\\u1eafng\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"\\\\u0110\\\\u1eadu que\\\",\\\"B\\\\u1eafp c\\\\u1ea3i\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"D\\\\u1ea7u h\\\\u00e0o\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Cơm Đùi Gà Chiên đặc biệt', 'Đây là món Cơm Đùi Gà Chiên ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(61, 5, 'MI-AEXRc', 'Mì Ý Sốt Bò', '93187.00', 27, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Mì Ý Sốt Bò đặc biệt', 'Đây là món Mì Ý Sốt Bò ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(62, 5, 'MI-XGo1I', 'Mì Ý Hải Sản', '109211.00', 12, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"],\\\"seafood\\\":[\\\"T\\\\u00f4m\\\",\\\"M\\\\u1ef1c\\\",\\\"C\\\\u00e1 h\\\\u1ed3i\\\"]}\"', 'Món Mì Ý Hải Sản đặc biệt', 'Đây là món Mì Ý Hải Sản ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(63, 5, 'MI-LPVJh', 'Mì Ý Gà', '114903.00', 25, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Mì Ý Gà đặc biệt', 'Đây là món Mì Ý Gà ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(64, 5, 'MI-5bqyb', 'Mì Ý Carbonara', '36962.00', 25, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Ý Carbonara đặc biệt', 'Đây là món Mì Ý Carbonara ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(65, 5, 'MI-18GTN', 'Mì Xào Hải Sản', '166819.00', 28, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"],\\\"seafood\\\":[\\\"T\\\\u00f4m\\\",\\\"M\\\\u1ef1c\\\",\\\"C\\\\u00e1 h\\\\u1ed3i\\\"]}\"', 'Món Mì Xào Hải Sản đặc biệt', 'Đây là món Mì Xào Hải Sản ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(66, 5, 'MI-RkSMM', 'Mì Ý Sốt Kem', '181787.00', 30, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Ý Sốt Kem đặc biệt', 'Đây là món Mì Ý Sốt Kem ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(67, 5, 'MI-shJul', 'Mì Xào Bò', '99237.00', 23, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt b\\\\u00f2 \\\\u00dac\\\",\\\"Th\\\\u1ecbt b\\\\u00f2 xay\\\"]}\"', 'Món Mì Xào Bò đặc biệt', 'Đây là món Mì Xào Bò ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(68, 5, 'MI-67UGQ', 'Mì Ý Sốt Cà Chua', '135235.00', 18, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Ý Sốt Cà Chua đặc biệt', 'Đây là món Mì Ý Sốt Cà Chua ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(69, 5, 'MI-kaXXA', 'Mì Xào Gà', '63446.00', 12, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"],\\\"meat\\\":[\\\"Th\\\\u1ecbt g\\\\u00e0 phi l\\\\u00ea\\\",\\\"\\\\u1ee8c g\\\\u00e0\\\"]}\"', 'Món Mì Xào Gà đặc biệt', 'Đây là món Mì Xào Gà ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(70, 5, 'MI-X4Nmg', 'Mì Ý Sốt Nấm', '128289.00', 16, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Ý Sốt Nấm đặc biệt', 'Đây là món Mì Ý Sốt Nấm ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(71, 5, 'MI-oOTaH', 'Mì Hoàng Kim', '179927.00', 25, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Hoàng Kim đặc biệt', 'Đây là món Mì Hoàng Kim ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(72, 5, 'MI-npAiP', 'Mì Ý Thịt Viên', '188231.00', 10, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Ý Thịt Viên đặc biệt', 'Đây là món Mì Ý Thịt Viên ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(73, 5, 'MI-bWpQw', 'Mì Xào Thập Cẩm', '73974.00', 22, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Xào Thập Cẩm đặc biệt', 'Đây là món Mì Xào Thập Cẩm ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(74, 5, 'MI-eMyrs', 'Mì Ý Chay', '87658.00', 17, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Ý Chay đặc biệt', 'Đây là món Mì Ý Chay ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(75, 5, 'MI-3MLxs', 'Mì Đặc Biệt', '129924.00', 19, '\"{\\\"base\\\":[\\\"M\\\\u00ec \\\\u00dd\\\",\\\"S\\\\u1ed1t c\\\\u00e0 chua\\\"],\\\"vegetables\\\":[\\\"C\\\\u00e0 r\\\\u1ed1t\\\",\\\"N\\\\u1ea5m\\\",\\\"H\\\\u00e0nh t\\\\u00e2y\\\"],\\\"spices\\\":[\\\"Mu\\\\u1ed1i\\\",\\\"Ti\\\\u00eau\\\",\\\"Oregano\\\"]}\"', 'Món Mì Đặc Biệt đặc biệt', 'Đây là món Mì Đặc Biệt ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(76, 6, 'DU-ikmX8', 'Coca Cola', '108001.00', 15, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Coca Cola đặc biệt', 'Đây là món Coca Cola ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(77, 6, 'DU-w11Ut', 'Pepsi', '71242.00', 11, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Pepsi đặc biệt', 'Đây là món Pepsi ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(78, 6, 'DU-4Gt6a', '7 Up', '71310.00', 22, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món 7 Up đặc biệt', 'Đây là món 7 Up ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(79, 6, 'DU-CdQN1', 'Fanta', '196821.00', 27, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Fanta đặc biệt', 'Đây là món Fanta ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(80, 6, 'DU-FLAiX', 'Trà Đào', '43991.00', 10, '\"{\\\"base\\\":[\\\"Tr\\\\u00e0\\\",\\\"N\\\\u01b0\\\\u1edbc\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1\\\"],\\\"extras\\\":[\\\"\\\\u0110\\\\u00e0o mi\\\\u1ebfng\\\"]}\"', 'Món Trà Đào đặc biệt', 'Đây là món Trà Đào ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(81, 6, 'DU-LiTJq', 'Trà Vải', '187206.00', 15, '\"{\\\"base\\\":[\\\"Tr\\\\u00e0\\\",\\\"N\\\\u01b0\\\\u1edbc\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1\\\"],\\\"extras\\\":[\\\"V\\\\u1ea3i thi\\\\u1ec1u\\\"]}\"', 'Món Trà Vải đặc biệt', 'Đây là món Trà Vải ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(82, 6, 'DU-DZgLG', 'Trà Chanh', '33487.00', 14, '\"{\\\"base\\\":[\\\"Tr\\\\u00e0\\\",\\\"N\\\\u01b0\\\\u1edbc\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1\\\"],\\\"extras\\\":[\\\"Chanh t\\\\u01b0\\\\u01a1i\\\"]}\"', 'Món Trà Chanh đặc biệt', 'Đây là món Trà Chanh ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(83, 6, 'DU-ea45f', 'Cà Phê Đen', '54381.00', 30, '\"{\\\"base\\\":[\\\"C\\\\u00e0 ph\\\\u00ea nguy\\\\u00ean ch\\\\u1ea5t\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1\\\"],\\\"extras\\\":[]}\"', 'Món Cà Phê Đen đặc biệt', 'Đây là món Cà Phê Đen ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(84, 6, 'DU-4sRmL', 'Cà Phê Sữa', '85274.00', 13, '\"{\\\"base\\\":[\\\"C\\\\u00e0 ph\\\\u00ea nguy\\\\u00ean ch\\\\u1ea5t\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1\\\"],\\\"extras\\\":[\\\"S\\\\u1eefa \\\\u0111\\\\u1eb7c\\\"]}\"', 'Món Cà Phê Sữa đặc biệt', 'Đây là món Cà Phê Sữa ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(85, 6, 'DU-iByee', 'Sinh Tố Dâu', '35660.00', 28, '\"{\\\"base\\\":[\\\"D\\\\u00e2u t\\\\u00e2y\\\",\\\"S\\\\u1eefa t\\\\u01b0\\\\u01a1i\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"],\\\"extras\\\":[\\\"S\\\\u1eefa \\\\u0111\\\\u1eb7c\\\"]}\"', 'Món Sinh Tố Dâu đặc biệt', 'Đây là món Sinh Tố Dâu ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(86, 6, 'DU-ZxGZ9', 'Sinh Tố Bơ', '161887.00', 26, '\"{\\\"base\\\":[\\\"B\\\\u01a1\\\",\\\"S\\\\u1eefa t\\\\u01b0\\\\u01a1i\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"],\\\"extras\\\":[\\\"S\\\\u1eefa \\\\u0111\\\\u1eb7c\\\"]}\"', 'Món Sinh Tố Bơ đặc biệt', 'Đây là món Sinh Tố Bơ ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(87, 6, 'DU-B3daZ', 'Nước Cam', '101558.00', 19, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Nước Cam đặc biệt', 'Đây là món Nước Cam ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(88, 6, 'DU-MxSgO', 'Nước Ép Táo', '96513.00', 25, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Nước Ép Táo đặc biệt', 'Đây là món Nước Ép Táo ngon tuyệt', 'selling', NULL, 1, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(89, 6, 'DU-v9cdc', 'Trà Sữa', '193671.00', 26, '\"{\\\"base\\\":[\\\"Tr\\\\u00e0\\\",\\\"N\\\\u01b0\\\\u1edbc\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1\\\"],\\\"extras\\\":[\\\"Chanh t\\\\u01b0\\\\u01a1i\\\"]}\"', 'Món Trà Sữa đặc biệt', 'Đây là món Trà Sữa ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(90, 6, 'DU-DKC9F', 'Matcha Đá Xay', '180400.00', 18, '\"{\\\"base\\\":[\\\"N\\\\u01b0\\\\u1edbc c\\\\u00f3 ga\\\"],\\\"additives\\\":[\\\"\\\\u0110\\\\u01b0\\\\u1eddng\\\",\\\"\\\\u0110\\\\u00e1 vi\\\\u00ean\\\"]}\"', 'Món Matcha Đá Xay đặc biệt', 'Đây là món Matcha Đá Xay ngon tuyệt', 'selling', NULL, 0, NULL, NULL, '2025-05-30 21:27:17', '2025-05-30 21:27:17');

-- --------------------------------------------------------

--
-- Table structure for table `product_imgs`
--

CREATE TABLE `product_imgs` (
  `id` bigint UNSIGNED NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_imgs`
--

INSERT INTO `product_imgs` (`id`, `img`, `is_primary`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 'products/BG-2DmXs_image_0.jpg', 1, 1, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(2, 'products/BG-2DmXs_image_1.jpg', 0, 1, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(3, 'products/BG-2DmXs_image_2.jpg', 0, 1, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(4, 'products/BG-x2Kqy_image_0.jpg', 1, 2, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(5, 'products/BG-x2Kqy_image_1.jpg', 0, 2, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(6, 'products/BG-x2Kqy_image_2.jpg', 0, 2, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(7, 'products/BG-z9Yoi_image_0.jpg', 1, 3, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(8, 'products/BG-kJMib_image_0.jpg', 1, 4, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(9, 'products/BG-kJMib_image_1.jpg', 0, 4, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(10, 'products/BG-n0gnJ_image_0.jpg', 1, 5, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(11, 'products/BG-n0gnJ_image_1.jpg', 0, 5, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(12, 'products/BG-n0gnJ_image_2.jpg', 0, 5, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(13, 'products/BG-MOM3l_image_0.jpg', 1, 6, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(14, 'products/BG-a7ojg_image_0.jpg', 1, 7, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(15, 'products/BG-a7ojg_image_1.jpg', 0, 7, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(16, 'products/BG-xp4Xk_image_0.jpg', 1, 8, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(17, 'products/BG-xp4Xk_image_1.jpg', 0, 8, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(18, 'products/BG-xp4Xk_image_2.jpg', 0, 8, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(19, 'products/BG-oKwrN_image_0.jpg', 1, 9, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(20, 'products/BG-oKwrN_image_1.jpg', 0, 9, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(21, 'products/BG-oKwrN_image_2.jpg', 0, 9, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(22, 'products/BG-NhaIU_image_0.jpg', 1, 10, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(23, 'products/BG-NhaIU_image_1.jpg', 0, 10, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(24, 'products/BG-OUhiL_image_0.jpg', 1, 11, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(25, 'products/BG-OUhiL_image_1.jpg', 0, 11, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(26, 'products/BG-OUhiL_image_2.jpg', 0, 11, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(27, 'products/BG-vffSj_image_0.jpg', 1, 12, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(28, 'products/BG-vffSj_image_1.jpg', 0, 12, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(29, 'products/BG-vffSj_image_2.jpg', 0, 12, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(30, 'products/BG-vffSj_image_3.jpg', 0, 12, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(31, 'products/BG-BJd6Z_image_0.jpg', 1, 13, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(32, 'products/BG-BJd6Z_image_1.jpg', 0, 13, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(33, 'products/BG-BJd6Z_image_2.jpg', 0, 13, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(34, 'products/BG-BJd6Z_image_3.jpg', 0, 13, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(35, 'products/BG-fEOl2_image_0.jpg', 1, 14, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(36, 'products/BG-XXVAl_image_0.jpg', 1, 15, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(37, 'products/BG-XXVAl_image_1.jpg', 0, 15, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(38, 'products/PZ-pc0SP_image_0.jpg', 1, 16, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(39, 'products/PZ-foFZC_image_0.jpg', 1, 17, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(40, 'products/PZ-foFZC_image_1.jpg', 0, 17, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(41, 'products/PZ-foFZC_image_2.jpg', 0, 17, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(42, 'products/PZ-9zPTG_image_0.jpg', 1, 18, '2025-05-30 21:27:16', '2025-05-30 21:27:16'),
(43, 'products/PZ-9zPTG_image_1.jpg', 0, 18, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(44, 'products/PZ-9zPTG_image_2.jpg', 0, 18, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(45, 'products/PZ-9zPTG_image_3.jpg', 0, 18, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(46, 'products/PZ-VjKd2_image_0.jpg', 1, 19, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(47, 'products/PZ-VjKd2_image_1.jpg', 0, 19, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(48, 'products/PZ-VjKd2_image_2.jpg', 0, 19, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(49, 'products/PZ-78GM2_image_0.jpg', 1, 20, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(50, 'products/PZ-78GM2_image_1.jpg', 0, 20, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(51, 'products/PZ-rp1Zf_image_0.jpg', 1, 21, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(52, 'products/PZ-rp1Zf_image_1.jpg', 0, 21, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(53, 'products/PZ-kU1MS_image_0.jpg', 1, 22, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(54, 'products/PZ-kU1MS_image_1.jpg', 0, 22, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(55, 'products/PZ-kU1MS_image_2.jpg', 0, 22, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(56, 'products/PZ-kU1MS_image_3.jpg', 0, 22, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(57, 'products/PZ-SXUhg_image_0.jpg', 1, 23, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(58, 'products/PZ-SXUhg_image_1.jpg', 0, 23, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(59, 'products/PZ-SXUhg_image_2.jpg', 0, 23, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(60, 'products/PZ-a2B2X_image_0.jpg', 1, 24, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(61, 'products/PZ-a2B2X_image_1.jpg', 0, 24, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(62, 'products/PZ-rUAai_image_0.jpg', 1, 25, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(63, 'products/PZ-rUAai_image_1.jpg', 0, 25, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(64, 'products/PZ-YseGP_image_0.jpg', 1, 26, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(65, 'products/PZ-neEhM_image_0.jpg', 1, 27, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(66, 'products/PZ-neEhM_image_1.jpg', 0, 27, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(67, 'products/PZ-eeItp_image_0.jpg', 1, 28, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(68, 'products/PZ-eeItp_image_1.jpg', 0, 28, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(69, 'products/PZ-eeItp_image_2.jpg', 0, 28, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(70, 'products/PZ-Bp2ng_image_0.jpg', 1, 29, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(71, 'products/PZ-Bp2ng_image_1.jpg', 0, 29, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(72, 'products/PZ-UMcZL_image_0.jpg', 1, 30, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(73, 'products/PZ-UMcZL_image_1.jpg', 0, 30, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(74, 'products/PZ-UMcZL_image_2.jpg', 0, 30, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(75, 'products/GR-eGMbE_image_0.jpg', 1, 31, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(76, 'products/GR-NUyqF_image_0.jpg', 1, 32, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(77, 'products/GR-NUyqF_image_1.jpg', 0, 32, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(78, 'products/GR-uSs2p_image_0.jpg', 1, 33, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(79, 'products/GR-uSs2p_image_1.jpg', 0, 33, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(80, 'products/GR-uSs2p_image_2.jpg', 0, 33, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(81, 'products/GR-x6JnY_image_0.jpg', 1, 34, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(82, 'products/GR-x6JnY_image_1.jpg', 0, 34, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(83, 'products/GR-szypo_image_0.jpg', 1, 35, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(84, 'products/GR-szypo_image_1.jpg', 0, 35, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(85, 'products/GR-szypo_image_2.jpg', 0, 35, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(86, 'products/GR-ya1Nz_image_0.jpg', 1, 36, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(87, 'products/GR-ya1Nz_image_1.jpg', 0, 36, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(88, 'products/GR-ylnXK_image_0.jpg', 1, 37, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(89, 'products/GR-xmKwN_image_0.jpg', 1, 38, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(90, 'products/GR-xmKwN_image_1.jpg', 0, 38, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(91, 'products/GR-xmKwN_image_2.jpg', 0, 38, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(92, 'products/GR-R6jTC_image_0.jpg', 1, 39, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(93, 'products/GR-R6jTC_image_1.jpg', 0, 39, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(94, 'products/GR-R6jTC_image_2.jpg', 0, 39, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(95, 'products/GR-TvIuI_image_0.jpg', 1, 40, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(96, 'products/GR-TvIuI_image_1.jpg', 0, 40, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(97, 'products/GR-sKGvN_image_0.jpg', 1, 41, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(98, 'products/GR-sKGvN_image_1.jpg', 0, 41, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(99, 'products/GR-sKGvN_image_2.jpg', 0, 41, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(100, 'products/GR-sKGvN_image_3.jpg', 0, 41, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(101, 'products/GR-KRVws_image_0.jpg', 1, 42, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(102, 'products/GR-Lvohu_image_0.jpg', 1, 43, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(103, 'products/GR-Lvohu_image_1.jpg', 0, 43, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(104, 'products/GR-Lvohu_image_2.jpg', 0, 43, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(105, 'products/GR-C8ApQ_image_0.jpg', 1, 44, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(106, 'products/GR-C8ApQ_image_1.jpg', 0, 44, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(107, 'products/GR-C8ApQ_image_2.jpg', 0, 44, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(108, 'products/GR-ckZQB_image_0.jpg', 1, 45, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(109, 'products/CM-bYTEf_image_0.jpg', 1, 46, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(110, 'products/CM-bYTEf_image_1.jpg', 0, 46, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(111, 'products/CM-Yk9NV_image_0.jpg', 1, 47, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(112, 'products/CM-Yk9NV_image_1.jpg', 0, 47, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(113, 'products/CM-XXX2f_image_0.jpg', 1, 48, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(114, 'products/CM-XXX2f_image_1.jpg', 0, 48, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(115, 'products/CM-XXX2f_image_2.jpg', 0, 48, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(116, 'products/CM-XXX2f_image_3.jpg', 0, 48, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(117, 'products/CM-RWCSj_image_0.jpg', 1, 49, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(118, 'products/CM-OdRZi_image_0.jpg', 1, 50, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(119, 'products/CM-wyx9K_image_0.jpg', 1, 51, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(120, 'products/CM-wyx9K_image_1.jpg', 0, 51, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(121, 'products/CM-wyx9K_image_2.jpg', 0, 51, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(122, 'products/CM-Hxt1D_image_0.jpg', 1, 52, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(123, 'products/CM-Hxt1D_image_1.jpg', 0, 52, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(124, 'products/CM-Hxt1D_image_2.jpg', 0, 52, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(125, 'products/CM-Hxt1D_image_3.jpg', 0, 52, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(126, 'products/CM-hQwe8_image_0.jpg', 1, 53, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(127, 'products/CM-hQwe8_image_1.jpg', 0, 53, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(128, 'products/CM-uzzCF_image_0.jpg', 1, 54, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(129, 'products/CM-uzzCF_image_1.jpg', 0, 54, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(130, 'products/CM-8EZ7C_image_0.jpg', 1, 55, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(131, 'products/CM-8EZ7C_image_1.jpg', 0, 55, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(132, 'products/CM-FLLBb_image_0.jpg', 1, 56, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(133, 'products/CM-vDQUg_image_0.jpg', 1, 57, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(134, 'products/CM-vDQUg_image_1.jpg', 0, 57, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(135, 'products/CM-50wK8_image_0.jpg', 1, 58, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(136, 'products/CM-2N2EP_image_0.jpg', 1, 59, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(137, 'products/CM-Npsf1_image_0.jpg', 1, 60, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(138, 'products/MI-AEXRc_image_0.jpg', 1, 61, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(139, 'products/MI-AEXRc_image_1.jpg', 0, 61, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(140, 'products/MI-XGo1I_image_0.jpg', 1, 62, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(141, 'products/MI-XGo1I_image_1.jpg', 0, 62, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(142, 'products/MI-LPVJh_image_0.jpg', 1, 63, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(143, 'products/MI-5bqyb_image_0.jpg', 1, 64, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(144, 'products/MI-18GTN_image_0.jpg', 1, 65, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(145, 'products/MI-RkSMM_image_0.jpg', 1, 66, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(146, 'products/MI-RkSMM_image_1.jpg', 0, 66, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(147, 'products/MI-RkSMM_image_2.jpg', 0, 66, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(148, 'products/MI-shJul_image_0.jpg', 1, 67, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(149, 'products/MI-67UGQ_image_0.jpg', 1, 68, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(150, 'products/MI-kaXXA_image_0.jpg', 1, 69, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(151, 'products/MI-kaXXA_image_1.jpg', 0, 69, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(152, 'products/MI-kaXXA_image_2.jpg', 0, 69, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(153, 'products/MI-kaXXA_image_3.jpg', 0, 69, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(154, 'products/MI-X4Nmg_image_0.jpg', 1, 70, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(155, 'products/MI-oOTaH_image_0.jpg', 1, 71, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(156, 'products/MI-oOTaH_image_1.jpg', 0, 71, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(157, 'products/MI-oOTaH_image_2.jpg', 0, 71, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(158, 'products/MI-oOTaH_image_3.jpg', 0, 71, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(159, 'products/MI-npAiP_image_0.jpg', 1, 72, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(160, 'products/MI-npAiP_image_1.jpg', 0, 72, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(161, 'products/MI-npAiP_image_2.jpg', 0, 72, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(162, 'products/MI-bWpQw_image_0.jpg', 1, 73, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(163, 'products/MI-bWpQw_image_1.jpg', 0, 73, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(164, 'products/MI-eMyrs_image_0.jpg', 1, 74, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(165, 'products/MI-eMyrs_image_1.jpg', 0, 74, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(166, 'products/MI-eMyrs_image_2.jpg', 0, 74, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(167, 'products/MI-eMyrs_image_3.jpg', 0, 74, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(168, 'products/MI-3MLxs_image_0.jpg', 1, 75, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(169, 'products/MI-3MLxs_image_1.jpg', 0, 75, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(170, 'products/MI-3MLxs_image_2.jpg', 0, 75, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(171, 'products/DU-ikmX8_image_0.jpg', 1, 76, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(172, 'products/DU-ikmX8_image_1.jpg', 0, 76, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(173, 'products/DU-w11Ut_image_0.jpg', 1, 77, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(174, 'products/DU-4Gt6a_image_0.jpg', 1, 78, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(175, 'products/DU-4Gt6a_image_1.jpg', 0, 78, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(176, 'products/DU-CdQN1_image_0.jpg', 1, 79, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(177, 'products/DU-CdQN1_image_1.jpg', 0, 79, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(178, 'products/DU-CdQN1_image_2.jpg', 0, 79, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(179, 'products/DU-CdQN1_image_3.jpg', 0, 79, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(180, 'products/DU-FLAiX_image_0.jpg', 1, 80, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(181, 'products/DU-FLAiX_image_1.jpg', 0, 80, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(182, 'products/DU-FLAiX_image_2.jpg', 0, 80, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(183, 'products/DU-LiTJq_image_0.jpg', 1, 81, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(184, 'products/DU-DZgLG_image_0.jpg', 1, 82, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(185, 'products/DU-DZgLG_image_1.jpg', 0, 82, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(186, 'products/DU-ea45f_image_0.jpg', 1, 83, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(187, 'products/DU-ea45f_image_1.jpg', 0, 83, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(188, 'products/DU-ea45f_image_2.jpg', 0, 83, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(189, 'products/DU-4sRmL_image_0.jpg', 1, 84, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(190, 'products/DU-iByee_image_0.jpg', 1, 85, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(191, 'products/DU-iByee_image_1.jpg', 0, 85, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(192, 'products/DU-iByee_image_2.jpg', 0, 85, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(193, 'products/DU-iByee_image_3.jpg', 0, 85, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(194, 'products/DU-ZxGZ9_image_0.jpg', 1, 86, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(195, 'products/DU-ZxGZ9_image_1.jpg', 0, 86, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(196, 'products/DU-B3daZ_image_0.jpg', 1, 87, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(197, 'products/DU-B3daZ_image_1.jpg', 0, 87, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(198, 'products/DU-MxSgO_image_0.jpg', 1, 88, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(199, 'products/DU-MxSgO_image_1.jpg', 0, 88, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(200, 'products/DU-MxSgO_image_2.jpg', 0, 88, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(201, 'products/DU-v9cdc_image_0.jpg', 1, 89, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(202, 'products/DU-v9cdc_image_1.jpg', 0, 89, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(203, 'products/DU-DKC9F_image_0.jpg', 1, 90, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(204, 'products/DU-DKC9F_image_1.jpg', 0, 90, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(205, 'products/DU-DKC9F_image_2.jpg', 0, 90, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(206, 'products/DU-DKC9F_image_3.jpg', 0, 90, '2025-05-30 21:27:17', '2025-05-30 21:27:17');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
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
-- Table structure for table `product_toppings`
--

CREATE TABLE `product_toppings` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `topping_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_toppings`
--

INSERT INTO `product_toppings` (`id`, `product_id`, `topping_id`, `created_at`, `updated_at`) VALUES
(1, 1, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(2, 1, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(3, 1, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(4, 1, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(5, 1, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(6, 1, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(7, 1, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(8, 2, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(9, 2, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(10, 2, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(11, 2, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(12, 2, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(13, 3, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(14, 3, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(15, 3, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(16, 3, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(17, 3, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(18, 4, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(19, 4, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(20, 4, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(21, 4, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(22, 4, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(23, 4, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(24, 4, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(25, 4, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(26, 5, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(27, 5, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(28, 5, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(29, 6, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(30, 6, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(31, 6, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(32, 6, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(33, 6, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(34, 6, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(35, 7, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(36, 7, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(37, 7, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(38, 7, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(39, 7, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(40, 7, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(41, 8, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(42, 8, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(43, 8, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(44, 8, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(45, 8, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(46, 8, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(47, 9, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(48, 9, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(49, 9, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(50, 9, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(51, 9, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(52, 10, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(53, 10, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(54, 10, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(55, 10, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(56, 11, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(57, 11, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(58, 11, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(59, 11, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(60, 11, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(61, 11, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(62, 11, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(63, 11, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(64, 12, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(65, 12, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(66, 12, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(67, 12, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(68, 12, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(69, 12, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(70, 12, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(71, 12, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(72, 13, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(73, 13, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(74, 13, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(75, 13, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(76, 13, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(77, 13, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(78, 14, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(79, 14, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(80, 14, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(81, 14, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(82, 15, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(83, 15, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(84, 15, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(85, 15, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(86, 15, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(87, 15, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(88, 15, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(89, 15, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(90, 16, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(91, 16, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(92, 16, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(93, 16, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(94, 16, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(95, 16, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(96, 17, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(97, 17, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(98, 17, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(99, 17, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(100, 17, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(101, 18, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(102, 18, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(103, 18, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(104, 18, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(105, 18, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(106, 19, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(107, 19, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(108, 19, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(109, 19, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(110, 19, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(111, 20, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(112, 20, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(113, 20, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(114, 20, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(115, 20, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(116, 20, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(117, 21, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(118, 21, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(119, 21, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(120, 21, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(121, 21, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(122, 22, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(123, 22, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(124, 22, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(125, 22, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(126, 22, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(127, 22, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(128, 23, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(129, 23, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(130, 23, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(131, 24, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(132, 24, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(133, 24, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(134, 24, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(135, 24, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(136, 24, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(137, 24, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(138, 25, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(139, 25, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(140, 25, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(141, 25, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(142, 25, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(143, 26, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(144, 26, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(145, 26, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(146, 26, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(147, 26, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(148, 26, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(149, 27, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(150, 27, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(151, 27, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(152, 27, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(153, 27, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(154, 27, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(155, 27, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(156, 28, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(157, 28, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(158, 28, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(159, 28, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(160, 28, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(161, 28, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(162, 28, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(163, 29, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(164, 29, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(165, 29, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(166, 30, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(167, 30, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(168, 30, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(169, 31, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(170, 31, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(171, 31, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(172, 31, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(173, 31, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(174, 31, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(175, 31, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(176, 32, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(177, 32, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(178, 32, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(179, 32, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(180, 32, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(181, 32, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(182, 33, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(183, 33, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(184, 33, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(185, 33, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(186, 33, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(187, 33, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(188, 34, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(189, 34, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(190, 34, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(191, 34, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(192, 34, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(193, 34, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(194, 35, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(195, 35, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(196, 35, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(197, 35, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(198, 36, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(199, 36, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(200, 36, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(201, 37, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(202, 37, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(203, 37, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(204, 37, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(205, 37, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(206, 37, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(207, 37, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(208, 38, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(209, 38, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(210, 38, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(211, 38, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(212, 38, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(213, 38, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(214, 38, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(215, 38, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(216, 39, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(217, 39, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(218, 39, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(219, 39, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(220, 39, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(221, 39, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(222, 39, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(223, 39, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(224, 40, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(225, 40, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(226, 40, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(227, 40, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(228, 40, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(229, 40, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(230, 40, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(231, 41, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(232, 41, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(233, 41, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(234, 41, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(235, 41, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(236, 41, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(237, 41, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(238, 41, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(239, 42, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(240, 42, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(241, 42, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(242, 42, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(243, 42, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(244, 42, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(245, 42, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(246, 43, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(247, 43, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(248, 43, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(249, 43, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(250, 43, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(251, 43, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(252, 43, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(253, 44, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(254, 44, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(255, 44, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(256, 44, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(257, 44, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(258, 44, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(259, 44, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(260, 44, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(261, 45, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(262, 45, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(263, 45, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(264, 45, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(265, 45, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(266, 45, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(267, 46, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(268, 46, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(269, 46, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(270, 47, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(271, 47, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(272, 47, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(273, 47, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(274, 48, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(275, 48, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(276, 48, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(277, 48, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(278, 48, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(279, 48, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(280, 49, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(281, 49, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(282, 49, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(283, 49, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(284, 49, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(285, 49, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(286, 49, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(287, 49, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(288, 50, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(289, 50, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(290, 50, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(291, 51, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(292, 51, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(293, 51, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(294, 51, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(295, 51, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(296, 51, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(297, 51, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(298, 51, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(299, 52, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(300, 52, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(301, 52, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(302, 52, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(303, 52, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(304, 52, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(305, 52, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(306, 53, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(307, 53, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(308, 53, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(309, 54, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(310, 54, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(311, 54, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(312, 54, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(313, 54, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(314, 55, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(315, 55, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(316, 55, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(317, 56, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(318, 56, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(319, 56, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(320, 56, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(321, 57, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(322, 57, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(323, 57, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(324, 57, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(325, 58, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(326, 58, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(327, 58, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(328, 58, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(329, 58, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(330, 59, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(331, 59, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(332, 59, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(333, 59, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(334, 59, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(335, 59, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(336, 59, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(337, 60, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(338, 60, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(339, 60, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(340, 60, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(341, 60, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(342, 60, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(343, 60, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(344, 60, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(345, 61, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(346, 61, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(347, 61, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(348, 61, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(349, 61, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(350, 61, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(351, 61, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(352, 62, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(353, 62, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(354, 62, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(355, 62, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(356, 62, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(357, 62, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(358, 63, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(359, 63, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(360, 63, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(361, 63, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(362, 64, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(363, 64, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(364, 64, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(365, 64, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(366, 65, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(367, 65, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(368, 65, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(369, 65, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(370, 65, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(371, 66, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(372, 66, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(373, 66, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(374, 66, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(375, 66, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(376, 67, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(377, 67, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(378, 67, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(379, 67, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(380, 67, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(381, 67, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(382, 68, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(383, 68, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(384, 68, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(385, 68, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(386, 68, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(387, 68, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(388, 69, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(389, 69, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(390, 69, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(391, 69, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(392, 69, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(393, 69, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(394, 69, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(395, 69, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(396, 70, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(397, 70, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(398, 70, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(399, 70, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(400, 70, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(401, 70, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(402, 70, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(403, 70, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(404, 71, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(405, 71, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(406, 71, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(407, 71, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(408, 71, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(409, 72, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(410, 72, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(411, 72, 9, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(412, 72, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(413, 72, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(414, 72, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(415, 72, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(416, 73, 14, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(417, 73, 17, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(418, 73, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(419, 73, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(420, 74, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(421, 74, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(422, 74, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(423, 74, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(424, 74, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(425, 74, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(426, 74, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(427, 74, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(428, 75, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(429, 75, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(430, 75, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(431, 75, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(432, 75, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(433, 75, 19, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(434, 75, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(435, 76, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(436, 76, 2, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(437, 76, 3, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(438, 76, 5, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(439, 76, 8, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(440, 76, 10, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(441, 76, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(442, 76, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(443, 77, 4, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(444, 77, 6, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(445, 77, 12, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(446, 77, 20, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(447, 78, 1, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(448, 78, 7, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(449, 78, 11, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(450, 78, 13, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(451, 78, 15, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(452, 78, 16, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(453, 78, 18, '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(454, 78, 19, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(455, 79, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(456, 79, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(457, 79, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(458, 79, 13, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(459, 79, 14, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(460, 79, 15, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(461, 80, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(462, 80, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(463, 80, 4, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(464, 80, 5, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(465, 80, 6, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(466, 80, 11, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(467, 80, 13, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(468, 80, 16, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(469, 81, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(470, 81, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(471, 81, 7, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(472, 81, 9, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(473, 81, 10, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(474, 81, 16, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(475, 81, 18, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(476, 81, 20, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(477, 82, 6, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(478, 82, 10, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(479, 82, 16, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(480, 82, 19, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(481, 83, 1, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(482, 83, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(483, 83, 4, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(484, 83, 5, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(485, 83, 9, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(486, 83, 13, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(487, 84, 3, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(488, 84, 18, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(489, 84, 19, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(490, 84, 20, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(491, 85, 2, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(492, 85, 11, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(493, 85, 18, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(494, 86, 6, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(495, 86, 9, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(496, 86, 10, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(497, 86, 13, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(498, 86, 15, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(499, 86, 16, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(500, 87, 5, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(501, 87, 6, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(502, 87, 7, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(503, 87, 13, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(504, 87, 14, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(505, 87, 17, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(506, 87, 19, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(507, 88, 6, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(508, 88, 11, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(509, 88, 18, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(510, 88, 19, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(511, 88, 20, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(512, 89, 10, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(513, 89, 15, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(514, 89, 17, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(515, 89, 18, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(516, 89, 20, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(517, 90, 6, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(518, 90, 16, '2025-05-30 21:27:26', '2025-05-30 21:27:26'),
(519, 90, 20, '2025-05-30 21:27:26', '2025-05-30 21:27:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `image`, `active`, `created_at`, `updated_at`) VALUES
(1, 1, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(2, 1, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(3, 1, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(4, 1, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(5, 1, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(6, 1, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(7, 1, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(8, 1, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(9, 1, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(10, 2, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(11, 2, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(12, 2, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(13, 2, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(14, 2, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(15, 2, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(16, 2, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(17, 2, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(18, 2, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(19, 3, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(20, 3, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(21, 3, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(22, 3, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(23, 3, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(24, 3, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(25, 3, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(26, 3, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(27, 3, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(28, 4, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(29, 4, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(30, 4, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(31, 4, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(32, 4, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(33, 4, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(34, 4, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(35, 4, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(36, 4, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(37, 5, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(38, 5, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(39, 5, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(40, 5, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(41, 5, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(42, 5, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(43, 5, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(44, 5, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(45, 5, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(46, 6, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(47, 6, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(48, 6, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(49, 6, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(50, 6, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(51, 6, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(52, 6, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(53, 6, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(54, 6, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(55, 7, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(56, 7, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(57, 7, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(58, 7, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(59, 7, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(60, 7, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(61, 7, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(62, 7, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(63, 7, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(64, 8, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(65, 8, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(66, 8, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(67, 8, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(68, 8, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(69, 8, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(70, 8, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(71, 8, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(72, 8, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(73, 9, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(74, 9, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(75, 9, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(76, 9, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(77, 9, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(78, 9, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(79, 9, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(80, 9, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(81, 9, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(82, 10, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(83, 10, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(84, 10, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(85, 10, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(86, 10, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(87, 10, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(88, 10, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(89, 10, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(90, 10, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(91, 11, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(92, 11, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(93, 11, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(94, 11, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(95, 11, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(96, 11, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(97, 11, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(98, 11, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(99, 11, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(100, 12, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(101, 12, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(102, 12, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(103, 12, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(104, 12, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(105, 12, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(106, 12, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(107, 12, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(108, 12, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(109, 13, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(110, 13, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(111, 13, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(112, 13, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(113, 13, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(114, 13, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(115, 13, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(116, 13, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(117, 13, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(118, 14, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(119, 14, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(120, 14, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(121, 14, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(122, 14, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(123, 14, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(124, 14, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(125, 14, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(126, 14, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(127, 15, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(128, 15, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(129, 15, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(130, 15, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(131, 15, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(132, 15, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(133, 15, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(134, 15, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(135, 15, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(136, 16, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(137, 16, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(138, 16, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(139, 16, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(140, 16, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(141, 16, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(142, 16, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(143, 16, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(144, 16, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(145, 17, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(146, 17, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(147, 17, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(148, 17, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(149, 17, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(150, 17, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(151, 17, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(152, 17, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(153, 17, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(154, 18, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(155, 18, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(156, 18, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(157, 18, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(158, 18, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(159, 18, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(160, 18, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(161, 18, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(162, 18, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(163, 19, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(164, 19, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(165, 19, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(166, 19, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(167, 19, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(168, 19, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(169, 19, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(170, 19, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(171, 19, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(172, 20, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(173, 20, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(174, 20, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(175, 20, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(176, 20, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(177, 20, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(178, 20, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(179, 20, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(180, 20, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(181, 21, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(182, 21, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(183, 21, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(184, 21, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(185, 21, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(186, 21, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(187, 21, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(188, 21, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(189, 21, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(190, 22, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(191, 22, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(192, 22, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(193, 22, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(194, 22, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(195, 22, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(196, 22, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(197, 22, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(198, 22, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(199, 23, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(200, 23, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(201, 23, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(202, 23, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(203, 23, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(204, 23, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(205, 23, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(206, 23, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(207, 23, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(208, 24, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(209, 24, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(210, 24, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(211, 24, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(212, 24, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(213, 24, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(214, 24, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(215, 24, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(216, 24, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(217, 25, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(218, 25, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(219, 25, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(220, 25, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(221, 25, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(222, 25, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(223, 25, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(224, 25, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(225, 25, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(226, 26, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(227, 26, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(228, 26, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(229, 26, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(230, 26, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(231, 26, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(232, 26, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(233, 26, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(234, 26, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(235, 27, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(236, 27, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(237, 27, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(238, 27, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(239, 27, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(240, 27, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(241, 27, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(242, 27, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(243, 27, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(244, 28, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(245, 28, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(246, 28, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(247, 28, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(248, 28, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(249, 28, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(250, 28, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(251, 28, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(252, 28, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(253, 29, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(254, 29, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(255, 29, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(256, 29, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(257, 29, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(258, 29, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(259, 29, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(260, 29, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(261, 29, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(262, 30, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(263, 30, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(264, 30, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(265, 30, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(266, 30, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(267, 30, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(268, 30, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(269, 30, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(270, 30, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(271, 31, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(272, 31, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(273, 31, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(274, 31, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(275, 31, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(276, 31, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(277, 31, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(278, 31, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(279, 31, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(280, 32, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(281, 32, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(282, 32, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(283, 32, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(284, 32, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(285, 32, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(286, 32, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(287, 32, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(288, 32, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(289, 33, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(290, 33, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(291, 33, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(292, 33, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(293, 33, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(294, 33, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(295, 33, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(296, 33, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(297, 33, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(298, 34, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(299, 34, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(300, 34, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(301, 34, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(302, 34, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(303, 34, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(304, 34, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(305, 34, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(306, 34, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(307, 35, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(308, 35, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(309, 35, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(310, 35, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(311, 35, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(312, 35, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(313, 35, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(314, 35, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(315, 35, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(316, 36, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(317, 36, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(318, 36, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(319, 36, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(320, 36, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(321, 36, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(322, 36, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(323, 36, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(324, 36, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(325, 37, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(326, 37, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(327, 37, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(328, 37, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(329, 37, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(330, 37, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(331, 37, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(332, 37, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(333, 37, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(334, 38, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(335, 38, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(336, 38, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(337, 38, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(338, 38, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(339, 38, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(340, 38, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(341, 38, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(342, 38, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(343, 39, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(344, 39, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(345, 39, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(346, 39, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(347, 39, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(348, 39, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(349, 39, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(350, 39, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(351, 39, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(352, 40, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(353, 40, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(354, 40, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(355, 40, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(356, 40, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(357, 40, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(358, 40, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(359, 40, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(360, 40, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(361, 41, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(362, 41, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(363, 41, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(364, 41, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(365, 41, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(366, 41, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(367, 41, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(368, 41, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(369, 41, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(370, 42, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(371, 42, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(372, 42, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(373, 42, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(374, 42, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(375, 42, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(376, 42, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(377, 42, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(378, 42, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(379, 43, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(380, 43, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(381, 43, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(382, 43, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(383, 43, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(384, 43, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(385, 43, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(386, 43, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(387, 43, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(388, 44, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(389, 44, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(390, 44, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(391, 44, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(392, 44, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(393, 44, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(394, 44, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(395, 44, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(396, 44, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(397, 45, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(398, 45, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(399, 45, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(400, 45, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(401, 45, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(402, 45, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(403, 45, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(404, 45, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(405, 45, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(406, 46, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(407, 46, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(408, 46, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(409, 46, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(410, 46, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(411, 46, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(412, 46, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(413, 46, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(414, 46, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(415, 47, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(416, 47, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(417, 47, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(418, 47, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(419, 47, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(420, 47, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(421, 47, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(422, 47, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(423, 47, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(424, 48, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(425, 48, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(426, 48, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(427, 48, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(428, 48, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(429, 48, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(430, 48, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(431, 48, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(432, 48, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(433, 49, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(434, 49, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(435, 49, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(436, 49, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(437, 49, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(438, 49, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(439, 49, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(440, 49, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(441, 49, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(442, 50, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(443, 50, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(444, 50, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(445, 50, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(446, 50, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(447, 50, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(448, 50, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(449, 50, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(450, 50, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(451, 51, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(452, 51, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(453, 51, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(454, 51, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(455, 51, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(456, 51, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(457, 51, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(458, 51, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(459, 51, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(460, 52, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(461, 52, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(462, 52, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(463, 52, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(464, 52, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(465, 52, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(466, 52, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(467, 52, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(468, 52, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(469, 53, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(470, 53, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(471, 53, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(472, 53, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(473, 53, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(474, 53, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(475, 53, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(476, 53, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(477, 53, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(478, 54, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(479, 54, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(480, 54, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(481, 54, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(482, 54, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(483, 54, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(484, 54, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(485, 54, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(486, 54, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(487, 55, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(488, 55, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(489, 55, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(490, 55, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(491, 55, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(492, 55, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(493, 55, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(494, 55, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(495, 55, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(496, 56, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(497, 56, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(498, 56, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(499, 56, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(500, 56, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(501, 56, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(502, 56, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(503, 56, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(504, 56, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(505, 57, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(506, 57, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(507, 57, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(508, 57, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(509, 57, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(510, 57, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(511, 57, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(512, 57, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(513, 57, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(514, 58, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(515, 58, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(516, 58, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(517, 58, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(518, 58, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(519, 58, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(520, 58, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(521, 58, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(522, 58, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(523, 59, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(524, 59, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(525, 59, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(526, 59, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(527, 59, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(528, 59, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(529, 59, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(530, 59, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(531, 59, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(532, 60, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(533, 60, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(534, 60, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(535, 60, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(536, 60, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(537, 60, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(538, 60, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(539, 60, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(540, 60, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(541, 61, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(542, 61, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(543, 61, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(544, 61, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(545, 61, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(546, 61, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(547, 61, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(548, 61, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(549, 61, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(550, 62, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(551, 62, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(552, 62, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(553, 62, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(554, 62, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(555, 62, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(556, 62, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(557, 62, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(558, 62, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(559, 63, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(560, 63, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(561, 63, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(562, 63, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(563, 63, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(564, 63, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(565, 63, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(566, 63, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(567, 63, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(568, 64, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(569, 64, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(570, 64, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(571, 64, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(572, 64, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(573, 64, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(574, 64, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(575, 64, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(576, 64, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(577, 65, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(578, 65, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(579, 65, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(580, 65, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(581, 65, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(582, 65, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(583, 65, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(584, 65, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(585, 65, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(586, 66, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(587, 66, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(588, 66, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(589, 66, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(590, 66, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(591, 66, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(592, 66, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(593, 66, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(594, 66, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(595, 67, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(596, 67, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19');
INSERT INTO `product_variants` (`id`, `product_id`, `image`, `active`, `created_at`, `updated_at`) VALUES
(597, 67, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(598, 67, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(599, 67, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(600, 67, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(601, 67, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(602, 67, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(603, 67, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(604, 68, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(605, 68, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(606, 68, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(607, 68, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(608, 68, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(609, 68, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(610, 68, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(611, 68, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(612, 68, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(613, 69, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(614, 69, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(615, 69, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(616, 69, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(617, 69, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(618, 69, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(619, 69, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(620, 69, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(621, 69, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(622, 70, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(623, 70, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(624, 70, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(625, 70, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(626, 70, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(627, 70, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(628, 70, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(629, 70, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(630, 70, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(631, 71, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(632, 71, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(633, 71, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(634, 71, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(635, 71, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(636, 71, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(637, 71, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(638, 71, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(639, 71, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(640, 72, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(641, 72, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(642, 72, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(643, 72, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(644, 72, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(645, 72, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(646, 72, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(647, 72, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(648, 72, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(649, 73, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(650, 73, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(651, 73, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(652, 73, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(653, 73, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(654, 73, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(655, 73, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(656, 73, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(657, 73, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(658, 74, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(659, 74, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(660, 74, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(661, 74, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(662, 74, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(663, 74, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(664, 74, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(665, 74, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(666, 74, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(667, 75, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(668, 75, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(669, 75, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(670, 75, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(671, 75, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(672, 75, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(673, 75, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(674, 75, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(675, 75, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(676, 76, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(677, 76, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(678, 76, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(679, 76, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(680, 76, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(681, 76, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(682, 76, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(683, 76, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(684, 76, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(685, 77, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(686, 77, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(687, 77, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(688, 77, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(689, 77, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(690, 77, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(691, 77, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(692, 77, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(693, 77, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(694, 78, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(695, 78, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(696, 78, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(697, 78, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(698, 78, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(699, 78, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(700, 78, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(701, 78, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(702, 78, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(703, 79, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(704, 79, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(705, 79, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(706, 79, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(707, 79, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(708, 79, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(709, 79, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(710, 79, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(711, 79, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(712, 80, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(713, 80, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(714, 80, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(715, 80, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(716, 80, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(717, 80, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(718, 80, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(719, 80, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(720, 80, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(721, 81, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(722, 81, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(723, 81, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(724, 81, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(725, 81, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(726, 81, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(727, 81, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(728, 81, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(729, 81, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(730, 82, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(731, 82, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(732, 82, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(733, 82, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(734, 82, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(735, 82, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(736, 82, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(737, 82, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(738, 82, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(739, 83, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(740, 83, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(741, 83, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(742, 83, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(743, 83, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(744, 83, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(745, 83, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(746, 83, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(747, 83, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(748, 84, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(749, 84, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(750, 84, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(751, 84, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(752, 84, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(753, 84, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(754, 84, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(755, 84, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(756, 84, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(757, 85, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(758, 85, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(759, 85, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(760, 85, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(761, 85, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(762, 85, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(763, 85, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(764, 85, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(765, 85, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(766, 86, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(767, 86, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(768, 86, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(769, 86, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(770, 86, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(771, 86, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(772, 86, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(773, 86, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(774, 86, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(775, 87, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(776, 87, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(777, 87, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(778, 87, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(779, 87, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(780, 87, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(781, 87, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(782, 87, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(783, 87, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(784, 88, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(785, 88, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(786, 88, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(787, 88, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(788, 88, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(789, 88, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(790, 88, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(791, 88, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(792, 88, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(793, 89, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(794, 89, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(795, 89, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(796, 89, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(797, 89, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(798, 89, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(799, 89, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(800, 89, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(801, 89, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(802, 90, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(803, 90, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(804, 90, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(805, 90, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(806, 90, 'variants/variant_5.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(807, 90, 'variants/variant_2.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(808, 90, 'variants/variant_1.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(809, 90, 'variants/variant_3.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(810, 90, 'variants/variant_4.jpg', 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_details`
--

CREATE TABLE `product_variant_details` (
  `id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED NOT NULL,
  `variant_value_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variant_details`
--

INSERT INTO `product_variant_details` (`id`, `product_variant_id`, `variant_value_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(2, 1, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(3, 2, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(4, 2, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(5, 3, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(6, 3, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(7, 4, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(8, 4, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(9, 5, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(10, 5, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(11, 6, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(12, 6, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(13, 7, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(14, 7, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(15, 8, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(16, 8, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(17, 9, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(18, 9, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(19, 10, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(20, 10, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(21, 11, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(22, 11, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(23, 12, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(24, 12, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(25, 13, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(26, 13, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(27, 14, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(28, 14, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(29, 15, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(30, 15, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(31, 16, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(32, 16, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(33, 17, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(34, 17, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(35, 18, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(36, 18, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(37, 19, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(38, 19, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(39, 20, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(40, 20, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(41, 21, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(42, 21, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(43, 22, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(44, 22, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(45, 23, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(46, 23, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(47, 24, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(48, 24, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(49, 25, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(50, 25, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(51, 26, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(52, 26, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(53, 27, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(54, 27, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(55, 28, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(56, 28, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(57, 29, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(58, 29, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(59, 30, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(60, 30, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(61, 31, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(62, 31, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(63, 32, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(64, 32, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(65, 33, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(66, 33, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(67, 34, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(68, 34, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(69, 35, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(70, 35, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(71, 36, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(72, 36, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(73, 37, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(74, 37, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(75, 38, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(76, 38, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(77, 39, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(78, 39, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(79, 40, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(80, 40, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(81, 41, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(82, 41, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(83, 42, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(84, 42, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(85, 43, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(86, 43, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(87, 44, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(88, 44, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(89, 45, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(90, 45, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(91, 46, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(92, 46, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(93, 47, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(94, 47, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(95, 48, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(96, 48, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(97, 49, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(98, 49, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(99, 50, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(100, 50, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(101, 51, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(102, 51, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(103, 52, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(104, 52, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(105, 53, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(106, 53, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(107, 54, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(108, 54, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(109, 55, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(110, 55, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(111, 56, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(112, 56, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(113, 57, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(114, 57, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(115, 58, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(116, 58, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(117, 59, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(118, 59, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(119, 60, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(120, 60, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(121, 61, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(122, 61, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(123, 62, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(124, 62, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(125, 63, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(126, 63, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(127, 64, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(128, 64, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(129, 65, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(130, 65, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(131, 66, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(132, 66, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(133, 67, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(134, 67, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(135, 68, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(136, 68, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(137, 69, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(138, 69, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(139, 70, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(140, 70, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(141, 71, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(142, 71, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(143, 72, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(144, 72, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(145, 73, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(146, 73, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(147, 74, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(148, 74, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(149, 75, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(150, 75, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(151, 76, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(152, 76, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(153, 77, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(154, 77, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(155, 78, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(156, 78, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(157, 79, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(158, 79, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(159, 80, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(160, 80, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(161, 81, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(162, 81, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(163, 82, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(164, 82, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(165, 83, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(166, 83, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(167, 84, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(168, 84, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(169, 85, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(170, 85, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(171, 86, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(172, 86, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(173, 87, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(174, 87, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(175, 88, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(176, 88, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(177, 89, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(178, 89, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(179, 90, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(180, 90, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(181, 91, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(182, 91, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(183, 92, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(184, 92, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(185, 93, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(186, 93, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(187, 94, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(188, 94, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(189, 95, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(190, 95, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(191, 96, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(192, 96, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(193, 97, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(194, 97, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(195, 98, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(196, 98, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(197, 99, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(198, 99, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(199, 100, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(200, 100, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(201, 101, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(202, 101, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(203, 102, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(204, 102, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(205, 103, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(206, 103, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(207, 104, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(208, 104, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(209, 105, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(210, 105, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(211, 106, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(212, 106, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(213, 107, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(214, 107, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(215, 108, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(216, 108, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(217, 109, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(218, 109, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(219, 110, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(220, 110, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(221, 111, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(222, 111, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(223, 112, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(224, 112, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(225, 113, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(226, 113, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(227, 114, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(228, 114, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(229, 115, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(230, 115, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(231, 116, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(232, 116, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(233, 117, 3, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(234, 117, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(235, 118, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(236, 118, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(237, 119, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(238, 119, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(239, 120, 1, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(240, 120, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(241, 121, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(242, 121, 4, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(243, 122, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(244, 122, 5, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(245, 123, 2, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(246, 123, 6, '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(247, 124, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(248, 124, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(249, 125, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(250, 125, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(251, 126, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(252, 126, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(253, 127, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(254, 127, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(255, 128, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(256, 128, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(257, 129, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(258, 129, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(259, 130, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(260, 130, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(261, 131, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(262, 131, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(263, 132, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(264, 132, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(265, 133, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(266, 133, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(267, 134, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(268, 134, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(269, 135, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(270, 135, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(271, 136, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(272, 136, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(273, 137, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(274, 137, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(275, 138, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(276, 138, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(277, 139, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(278, 139, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(279, 140, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(280, 140, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(281, 141, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(282, 141, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(283, 142, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(284, 142, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(285, 143, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(286, 143, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(287, 144, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(288, 144, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(289, 145, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(290, 145, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(291, 146, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(292, 146, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(293, 147, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(294, 147, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(295, 148, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(296, 148, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(297, 149, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(298, 149, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(299, 150, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(300, 150, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(301, 151, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(302, 151, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(303, 152, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(304, 152, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(305, 153, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(306, 153, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(307, 154, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(308, 154, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(309, 155, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(310, 155, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(311, 156, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(312, 156, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(313, 157, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(314, 157, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(315, 158, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(316, 158, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(317, 159, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(318, 159, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(319, 160, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(320, 160, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(321, 161, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(322, 161, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(323, 162, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(324, 162, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(325, 163, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(326, 163, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(327, 164, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(328, 164, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(329, 165, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(330, 165, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(331, 166, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(332, 166, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(333, 167, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(334, 167, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(335, 168, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(336, 168, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(337, 169, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(338, 169, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(339, 170, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(340, 170, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(341, 171, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(342, 171, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(343, 172, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(344, 172, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(345, 173, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(346, 173, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(347, 174, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(348, 174, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(349, 175, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(350, 175, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(351, 176, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(352, 176, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(353, 177, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(354, 177, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(355, 178, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(356, 178, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(357, 179, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(358, 179, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(359, 180, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(360, 180, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(361, 181, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(362, 181, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(363, 182, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(364, 182, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(365, 183, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(366, 183, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(367, 184, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(368, 184, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(369, 185, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(370, 185, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(371, 186, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(372, 186, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(373, 187, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(374, 187, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(375, 188, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(376, 188, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(377, 189, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(378, 189, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(379, 190, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(380, 190, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(381, 191, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(382, 191, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(383, 192, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(384, 192, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(385, 193, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(386, 193, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(387, 194, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(388, 194, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(389, 195, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(390, 195, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(391, 196, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(392, 196, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(393, 197, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(394, 197, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(395, 198, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(396, 198, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(397, 199, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(398, 199, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(399, 200, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(400, 200, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(401, 201, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(402, 201, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(403, 202, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(404, 202, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(405, 203, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(406, 203, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(407, 204, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(408, 204, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(409, 205, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(410, 205, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(411, 206, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(412, 206, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(413, 207, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(414, 207, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(415, 208, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(416, 208, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(417, 209, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(418, 209, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(419, 210, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(420, 210, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(421, 211, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(422, 211, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(423, 212, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(424, 212, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(425, 213, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(426, 213, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(427, 214, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(428, 214, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(429, 215, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(430, 215, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(431, 216, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(432, 216, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(433, 217, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(434, 217, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(435, 218, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(436, 218, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(437, 219, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(438, 219, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(439, 220, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(440, 220, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(441, 221, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(442, 221, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(443, 222, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(444, 222, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(445, 223, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(446, 223, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(447, 224, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(448, 224, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(449, 225, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(450, 225, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(451, 226, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(452, 226, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(453, 227, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(454, 227, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(455, 228, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(456, 228, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(457, 229, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(458, 229, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(459, 230, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(460, 230, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(461, 231, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(462, 231, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(463, 232, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(464, 232, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(465, 233, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(466, 233, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(467, 234, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(468, 234, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(469, 235, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(470, 235, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(471, 236, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(472, 236, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(473, 237, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(474, 237, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(475, 238, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(476, 238, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(477, 239, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(478, 239, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(479, 240, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(480, 240, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(481, 241, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(482, 241, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(483, 242, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(484, 242, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(485, 243, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(486, 243, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(487, 244, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(488, 244, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(489, 245, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(490, 245, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(491, 246, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(492, 246, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(493, 247, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(494, 247, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(495, 248, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(496, 248, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(497, 249, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(498, 249, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(499, 250, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(500, 250, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(501, 251, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(502, 251, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(503, 252, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(504, 252, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(505, 253, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(506, 253, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(507, 254, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(508, 254, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(509, 255, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(510, 255, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(511, 256, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(512, 256, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(513, 257, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(514, 257, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(515, 258, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(516, 258, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(517, 259, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(518, 259, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(519, 260, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(520, 260, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(521, 261, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(522, 261, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(523, 262, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(524, 262, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(525, 263, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(526, 263, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(527, 264, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(528, 264, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(529, 265, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(530, 265, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(531, 266, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(532, 266, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(533, 267, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(534, 267, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(535, 268, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(536, 268, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(537, 269, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(538, 269, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(539, 270, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(540, 270, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(541, 271, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(542, 271, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(543, 272, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(544, 272, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(545, 273, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(546, 273, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(547, 274, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(548, 274, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(549, 275, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(550, 275, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(551, 276, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(552, 276, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(553, 277, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(554, 277, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(555, 278, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(556, 278, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(557, 279, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(558, 279, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(559, 280, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(560, 280, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(561, 281, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(562, 281, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(563, 282, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(564, 282, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(565, 283, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(566, 283, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(567, 284, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(568, 284, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(569, 285, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(570, 285, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(571, 286, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(572, 286, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(573, 287, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(574, 287, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(575, 288, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(576, 288, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(577, 289, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(578, 289, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(579, 290, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(580, 290, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(581, 291, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(582, 291, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(583, 292, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(584, 292, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(585, 293, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(586, 293, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(587, 294, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(588, 294, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(589, 295, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(590, 295, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(591, 296, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(592, 296, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(593, 297, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(594, 297, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(595, 298, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(596, 298, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(597, 299, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(598, 299, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(599, 300, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(600, 300, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(601, 301, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(602, 301, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(603, 302, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(604, 302, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(605, 303, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(606, 303, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(607, 304, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(608, 304, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(609, 305, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(610, 305, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(611, 306, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(612, 306, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(613, 307, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(614, 307, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(615, 308, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(616, 308, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(617, 309, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(618, 309, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(619, 310, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(620, 310, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(621, 311, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(622, 311, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(623, 312, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(624, 312, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(625, 313, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(626, 313, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(627, 314, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(628, 314, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(629, 315, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(630, 315, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(631, 316, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(632, 316, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(633, 317, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(634, 317, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(635, 318, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(636, 318, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(637, 319, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(638, 319, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(639, 320, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(640, 320, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(641, 321, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(642, 321, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(643, 322, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(644, 322, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(645, 323, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(646, 323, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(647, 324, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(648, 324, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(649, 325, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(650, 325, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(651, 326, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(652, 326, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(653, 327, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(654, 327, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(655, 328, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(656, 328, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(657, 329, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(658, 329, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(659, 330, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(660, 330, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(661, 331, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(662, 331, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(663, 332, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(664, 332, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(665, 333, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(666, 333, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(667, 334, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(668, 334, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(669, 335, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(670, 335, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(671, 336, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(672, 336, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(673, 337, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(674, 337, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(675, 338, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(676, 338, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(677, 339, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(678, 339, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(679, 340, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(680, 340, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(681, 341, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(682, 341, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(683, 342, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(684, 342, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(685, 343, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(686, 343, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(687, 344, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(688, 344, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(689, 345, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(690, 345, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(691, 346, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(692, 346, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(693, 347, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(694, 347, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(695, 348, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(696, 348, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(697, 349, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(698, 349, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(699, 350, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(700, 350, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(701, 351, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(702, 351, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(703, 352, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(704, 352, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(705, 353, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(706, 353, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(707, 354, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(708, 354, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(709, 355, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(710, 355, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(711, 356, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(712, 356, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(713, 357, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(714, 357, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(715, 358, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(716, 358, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(717, 359, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(718, 359, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(719, 360, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(720, 360, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(721, 361, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(722, 361, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(723, 362, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(724, 362, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(725, 363, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(726, 363, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(727, 364, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(728, 364, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(729, 365, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(730, 365, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(731, 366, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(732, 366, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(733, 367, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(734, 367, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(735, 368, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(736, 368, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(737, 369, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(738, 369, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(739, 370, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(740, 370, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(741, 371, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(742, 371, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(743, 372, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(744, 372, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(745, 373, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(746, 373, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(747, 374, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(748, 374, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(749, 375, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(750, 375, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(751, 376, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(752, 376, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(753, 377, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(754, 377, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(755, 378, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(756, 378, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(757, 379, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(758, 379, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(759, 380, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(760, 380, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(761, 381, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(762, 381, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(763, 382, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(764, 382, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(765, 383, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(766, 383, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(767, 384, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(768, 384, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(769, 385, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(770, 385, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(771, 386, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(772, 386, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(773, 387, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(774, 387, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(775, 388, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(776, 388, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(777, 389, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(778, 389, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(779, 390, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(780, 390, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(781, 391, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(782, 391, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(783, 392, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(784, 392, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(785, 393, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(786, 393, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(787, 394, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(788, 394, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(789, 395, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(790, 395, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(791, 396, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(792, 396, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(793, 397, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(794, 397, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(795, 398, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(796, 398, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(797, 399, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(798, 399, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(799, 400, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(800, 400, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(801, 401, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(802, 401, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(803, 402, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(804, 402, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(805, 403, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(806, 403, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(807, 404, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(808, 404, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(809, 405, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(810, 405, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(811, 406, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(812, 406, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(813, 407, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(814, 407, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(815, 408, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(816, 408, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(817, 409, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(818, 409, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(819, 410, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(820, 410, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(821, 411, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(822, 411, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(823, 412, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(824, 412, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(825, 413, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(826, 413, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(827, 414, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(828, 414, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(829, 415, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(830, 415, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(831, 416, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(832, 416, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(833, 417, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(834, 417, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(835, 418, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(836, 418, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(837, 419, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(838, 419, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(839, 420, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(840, 420, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(841, 421, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(842, 421, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(843, 422, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(844, 422, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(845, 423, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(846, 423, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(847, 424, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(848, 424, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(849, 425, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(850, 425, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18');
INSERT INTO `product_variant_details` (`id`, `product_variant_id`, `variant_value_id`, `created_at`, `updated_at`) VALUES
(851, 426, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(852, 426, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(853, 427, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(854, 427, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(855, 428, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(856, 428, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(857, 429, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(858, 429, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(859, 430, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(860, 430, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(861, 431, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(862, 431, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(863, 432, 3, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(864, 432, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(865, 433, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(866, 433, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(867, 434, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(868, 434, 5, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(869, 435, 1, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(870, 435, 6, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(871, 436, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(872, 436, 4, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(873, 437, 2, '2025-05-30 21:27:18', '2025-05-30 21:27:18'),
(874, 437, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(875, 438, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(876, 438, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(877, 439, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(878, 439, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(879, 440, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(880, 440, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(881, 441, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(882, 441, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(883, 442, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(884, 442, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(885, 443, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(886, 443, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(887, 444, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(888, 444, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(889, 445, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(890, 445, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(891, 446, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(892, 446, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(893, 447, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(894, 447, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(895, 448, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(896, 448, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(897, 449, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(898, 449, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(899, 450, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(900, 450, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(901, 451, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(902, 451, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(903, 452, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(904, 452, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(905, 453, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(906, 453, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(907, 454, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(908, 454, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(909, 455, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(910, 455, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(911, 456, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(912, 456, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(913, 457, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(914, 457, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(915, 458, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(916, 458, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(917, 459, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(918, 459, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(919, 460, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(920, 460, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(921, 461, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(922, 461, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(923, 462, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(924, 462, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(925, 463, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(926, 463, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(927, 464, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(928, 464, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(929, 465, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(930, 465, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(931, 466, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(932, 466, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(933, 467, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(934, 467, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(935, 468, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(936, 468, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(937, 469, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(938, 469, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(939, 470, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(940, 470, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(941, 471, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(942, 471, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(943, 472, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(944, 472, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(945, 473, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(946, 473, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(947, 474, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(948, 474, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(949, 475, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(950, 475, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(951, 476, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(952, 476, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(953, 477, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(954, 477, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(955, 478, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(956, 478, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(957, 479, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(958, 479, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(959, 480, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(960, 480, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(961, 481, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(962, 481, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(963, 482, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(964, 482, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(965, 483, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(966, 483, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(967, 484, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(968, 484, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(969, 485, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(970, 485, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(971, 486, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(972, 486, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(973, 487, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(974, 487, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(975, 488, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(976, 488, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(977, 489, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(978, 489, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(979, 490, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(980, 490, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(981, 491, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(982, 491, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(983, 492, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(984, 492, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(985, 493, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(986, 493, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(987, 494, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(988, 494, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(989, 495, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(990, 495, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(991, 496, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(992, 496, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(993, 497, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(994, 497, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(995, 498, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(996, 498, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(997, 499, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(998, 499, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(999, 500, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1000, 500, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1001, 501, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1002, 501, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1003, 502, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1004, 502, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1005, 503, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1006, 503, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1007, 504, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1008, 504, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1009, 505, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1010, 505, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1011, 506, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1012, 506, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1013, 507, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1014, 507, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1015, 508, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1016, 508, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1017, 509, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1018, 509, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1019, 510, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1020, 510, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1021, 511, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1022, 511, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1023, 512, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1024, 512, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1025, 513, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1026, 513, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1027, 514, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1028, 514, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1029, 515, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1030, 515, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1031, 516, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1032, 516, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1033, 517, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1034, 517, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1035, 518, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1036, 518, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1037, 519, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1038, 519, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1039, 520, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1040, 520, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1041, 521, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1042, 521, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1043, 522, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1044, 522, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1045, 523, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1046, 523, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1047, 524, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1048, 524, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1049, 525, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1050, 525, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1051, 526, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1052, 526, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1053, 527, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1054, 527, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1055, 528, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1056, 528, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1057, 529, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1058, 529, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1059, 530, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1060, 530, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1061, 531, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1062, 531, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1063, 532, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1064, 532, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1065, 533, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1066, 533, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1067, 534, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1068, 534, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1069, 535, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1070, 535, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1071, 536, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1072, 536, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1073, 537, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1074, 537, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1075, 538, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1076, 538, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1077, 539, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1078, 539, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1079, 540, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1080, 540, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1081, 541, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1082, 541, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1083, 542, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1084, 542, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1085, 543, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1086, 543, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1087, 544, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1088, 544, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1089, 545, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1090, 545, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1091, 546, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1092, 546, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1093, 547, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1094, 547, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1095, 548, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1096, 548, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1097, 549, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1098, 549, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1099, 550, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1100, 550, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1101, 551, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1102, 551, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1103, 552, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1104, 552, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1105, 553, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1106, 553, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1107, 554, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1108, 554, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1109, 555, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1110, 555, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1111, 556, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1112, 556, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1113, 557, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1114, 557, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1115, 558, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1116, 558, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1117, 559, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1118, 559, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1119, 560, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1120, 560, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1121, 561, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1122, 561, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1123, 562, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1124, 562, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1125, 563, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1126, 563, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1127, 564, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1128, 564, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1129, 565, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1130, 565, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1131, 566, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1132, 566, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1133, 567, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1134, 567, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1135, 568, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1136, 568, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1137, 569, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1138, 569, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1139, 570, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1140, 570, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1141, 571, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1142, 571, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1143, 572, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1144, 572, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1145, 573, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1146, 573, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1147, 574, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1148, 574, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1149, 575, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1150, 575, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1151, 576, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1152, 576, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1153, 577, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1154, 577, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1155, 578, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1156, 578, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1157, 579, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1158, 579, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1159, 580, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1160, 580, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1161, 581, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1162, 581, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1163, 582, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1164, 582, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1165, 583, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1166, 583, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1167, 584, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1168, 584, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1169, 585, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1170, 585, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1171, 586, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1172, 586, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1173, 587, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1174, 587, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1175, 588, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1176, 588, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1177, 589, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1178, 589, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1179, 590, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1180, 590, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1181, 591, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1182, 591, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1183, 592, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1184, 592, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1185, 593, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1186, 593, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1187, 594, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1188, 594, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1189, 595, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1190, 595, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1191, 596, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1192, 596, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1193, 597, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1194, 597, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1195, 598, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1196, 598, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1197, 599, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1198, 599, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1199, 600, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1200, 600, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1201, 601, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1202, 601, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1203, 602, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1204, 602, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1205, 603, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1206, 603, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1207, 604, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1208, 604, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1209, 605, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1210, 605, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1211, 606, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1212, 606, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1213, 607, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1214, 607, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1215, 608, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1216, 608, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1217, 609, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1218, 609, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1219, 610, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1220, 610, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1221, 611, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1222, 611, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1223, 612, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1224, 612, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1225, 613, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1226, 613, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1227, 614, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1228, 614, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1229, 615, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1230, 615, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1231, 616, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1232, 616, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1233, 617, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1234, 617, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1235, 618, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1236, 618, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1237, 619, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1238, 619, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1239, 620, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1240, 620, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1241, 621, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1242, 621, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1243, 622, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1244, 622, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1245, 623, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1246, 623, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1247, 624, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1248, 624, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1249, 625, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1250, 625, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1251, 626, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1252, 626, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1253, 627, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1254, 627, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1255, 628, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1256, 628, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1257, 629, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1258, 629, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1259, 630, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1260, 630, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1261, 631, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1262, 631, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1263, 632, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1264, 632, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1265, 633, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1266, 633, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1267, 634, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1268, 634, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1269, 635, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1270, 635, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1271, 636, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1272, 636, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1273, 637, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1274, 637, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1275, 638, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1276, 638, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1277, 639, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1278, 639, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1279, 640, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1280, 640, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1281, 641, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1282, 641, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1283, 642, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1284, 642, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1285, 643, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1286, 643, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1287, 644, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1288, 644, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1289, 645, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1290, 645, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1291, 646, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1292, 646, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1293, 647, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1294, 647, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1295, 648, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1296, 648, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1297, 649, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1298, 649, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1299, 650, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1300, 650, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1301, 651, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1302, 651, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1303, 652, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1304, 652, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1305, 653, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1306, 653, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1307, 654, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1308, 654, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1309, 655, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1310, 655, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1311, 656, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1312, 656, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1313, 657, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1314, 657, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1315, 658, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1316, 658, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1317, 659, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1318, 659, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1319, 660, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1320, 660, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1321, 661, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1322, 661, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1323, 662, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1324, 662, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1325, 663, 2, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1326, 663, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1327, 664, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1328, 664, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1329, 665, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1330, 665, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1331, 666, 3, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1332, 666, 6, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1333, 667, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1334, 667, 4, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1335, 668, 1, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1336, 668, 5, '2025-05-30 21:27:19', '2025-05-30 21:27:19'),
(1337, 669, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1338, 669, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1339, 670, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1340, 670, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1341, 671, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1342, 671, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1343, 672, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1344, 672, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1345, 673, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1346, 673, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1347, 674, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1348, 674, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1349, 675, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1350, 675, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1351, 676, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1352, 676, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1353, 677, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1354, 677, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1355, 678, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1356, 678, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1357, 679, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1358, 679, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1359, 680, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1360, 680, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1361, 681, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1362, 681, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1363, 682, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1364, 682, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1365, 683, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1366, 683, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1367, 684, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1368, 684, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1369, 685, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1370, 685, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1371, 686, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1372, 686, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1373, 687, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1374, 687, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1375, 688, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1376, 688, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1377, 689, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1378, 689, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1379, 690, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1380, 690, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1381, 691, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1382, 691, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1383, 692, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1384, 692, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1385, 693, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1386, 693, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1387, 694, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1388, 694, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1389, 695, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1390, 695, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1391, 696, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1392, 696, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1393, 697, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1394, 697, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1395, 698, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1396, 698, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1397, 699, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1398, 699, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1399, 700, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1400, 700, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1401, 701, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1402, 701, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1403, 702, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1404, 702, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1405, 703, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1406, 703, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1407, 704, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1408, 704, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1409, 705, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1410, 705, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1411, 706, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1412, 706, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1413, 707, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1414, 707, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1415, 708, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1416, 708, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1417, 709, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1418, 709, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1419, 710, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1420, 710, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1421, 711, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1422, 711, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1423, 712, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1424, 712, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1425, 713, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1426, 713, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1427, 714, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1428, 714, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1429, 715, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1430, 715, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1431, 716, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1432, 716, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1433, 717, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1434, 717, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1435, 718, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1436, 718, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1437, 719, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1438, 719, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1439, 720, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1440, 720, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1441, 721, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1442, 721, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1443, 722, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1444, 722, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1445, 723, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1446, 723, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1447, 724, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1448, 724, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1449, 725, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1450, 725, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1451, 726, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1452, 726, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1453, 727, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1454, 727, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1455, 728, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1456, 728, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1457, 729, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1458, 729, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1459, 730, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1460, 730, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1461, 731, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1462, 731, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1463, 732, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1464, 732, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1465, 733, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1466, 733, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1467, 734, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1468, 734, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1469, 735, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1470, 735, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1471, 736, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1472, 736, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1473, 737, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1474, 737, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1475, 738, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1476, 738, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1477, 739, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1478, 739, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1479, 740, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1480, 740, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1481, 741, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1482, 741, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1483, 742, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1484, 742, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1485, 743, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1486, 743, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1487, 744, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1488, 744, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1489, 745, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1490, 745, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1491, 746, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1492, 746, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1493, 747, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1494, 747, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1495, 748, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1496, 748, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1497, 749, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1498, 749, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1499, 750, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1500, 750, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1501, 751, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1502, 751, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1503, 752, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1504, 752, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1505, 753, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1506, 753, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1507, 754, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1508, 754, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1509, 755, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1510, 755, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1511, 756, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1512, 756, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1513, 757, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1514, 757, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1515, 758, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1516, 758, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1517, 759, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1518, 759, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1519, 760, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1520, 760, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1521, 761, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1522, 761, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1523, 762, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1524, 762, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1525, 763, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1526, 763, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1527, 764, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1528, 764, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1529, 765, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1530, 765, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1531, 766, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1532, 766, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1533, 767, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1534, 767, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1535, 768, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1536, 768, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1537, 769, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1538, 769, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1539, 770, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1540, 770, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1541, 771, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1542, 771, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1543, 772, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1544, 772, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1545, 773, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1546, 773, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1547, 774, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1548, 774, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1549, 775, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1550, 775, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1551, 776, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1552, 776, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1553, 777, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1554, 777, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1555, 778, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1556, 778, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1557, 779, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1558, 779, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1559, 780, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1560, 780, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1561, 781, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1562, 781, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1563, 782, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1564, 782, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1565, 783, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1566, 783, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1567, 784, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1568, 784, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1569, 785, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1570, 785, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1571, 786, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1572, 786, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1573, 787, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1574, 787, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1575, 788, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1576, 788, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1577, 789, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1578, 789, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1579, 790, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1580, 790, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1581, 791, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1582, 791, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1583, 792, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1584, 792, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1585, 793, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1586, 793, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1587, 794, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1588, 794, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1589, 795, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1590, 795, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1591, 796, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1592, 796, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1593, 797, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1594, 797, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1595, 798, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1596, 798, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1597, 799, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1598, 799, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1599, 800, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1600, 800, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1601, 801, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1602, 801, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1603, 802, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1604, 802, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1605, 803, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1606, 803, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1607, 804, 1, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1608, 804, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1609, 805, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1610, 805, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1611, 806, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1612, 806, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1613, 807, 2, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1614, 807, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1615, 808, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1616, 808, 4, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1617, 809, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1618, 809, 5, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1619, 810, 3, '2025-05-30 21:27:20', '2025-05-30 21:27:20'),
(1620, 810, 6, '2025-05-30 21:27:20', '2025-05-30 21:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_branches`
--

CREATE TABLE `promotion_branches` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_program_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion_discount_codes`
--

CREATE TABLE `promotion_discount_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_program_id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion_programs`
--

CREATE TABLE `promotion_programs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `banner_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applicable_scope` enum('all_branches','specific_branches') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all_branches',
  `start_date` timestamp NOT NULL,
  `end_date` timestamp NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_orders`
--

CREATE TABLE `return_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `requested_at` datetime NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('requested','approved','rejected','processing','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requested',
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `responsible_party` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
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
(1, 'admin', '[\"*\"]', '2025-05-30 21:27:13', '2025-05-30 21:27:13'),
(2, 'manager', '[\"create\", \"edit\", \"view\"]', '2025-05-30 21:27:13', '2025-05-30 21:27:13'),
(3, 'staff', '[\"view\"]', '2025-05-30 21:27:13', '2025-05-30 21:27:13'),
(4, 'customer', '[\"view\"]', '2025-05-30 21:27:13', '2025-05-30 21:27:13'),
(5, 'driver', '[\"view\", \"driver_actions\"]', '2025-05-30 21:27:13', '2025-05-30 21:27:13');

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
('N5b7X7jru6DQ2BxlYemQK0WgBdO0DbLxtR0tI6z0', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiejBHZDE5S0FWdk9LcTZqRlNodkJxajdrZlRiemJjS1JLMVBXTHd0aCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi91c2VyX3JhbmtzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1748708426);

-- --------------------------------------------------------

--
-- Table structure for table `toppings`
--

CREATE TABLE `toppings` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `toppings`
--

INSERT INTO `toppings` (`id`, `name`, `price`, `active`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Phô Mai Thêm', '15000.00', 1, 'toppings/pho-mai-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(2, 'Thịt Bò Thêm', '25000.00', 1, 'toppings/thit-bo-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(3, 'Thịt Gà Thêm', '20000.00', 1, 'toppings/thit-ga-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(4, 'Bacon', '18000.00', 1, 'toppings/bacon.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(5, 'Trứng Ốp La', '12000.00', 1, 'toppings/trung-op-la.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(6, 'Xà Lách Thêm', '5000.00', 1, 'toppings/xa-lach-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(7, 'Cà Chua Thêm', '5000.00', 1, 'toppings/ca-chua-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(8, 'Hành Tây Thêm', '5000.00', 1, 'toppings/hanh-tay-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(9, 'Dưa Chuột Thêm', '5000.00', 1, 'toppings/dua-chuot-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(10, 'Sốt BBQ', '8000.00', 1, 'toppings/sot-bbq.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(11, 'Sốt Cay', '8000.00', 1, 'toppings/sot-cay.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(12, 'Sốt Mayonnaise', '8000.00', 1, 'toppings/sot-mayonnaise.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(13, 'Sốt Tỏi', '8000.00', 1, 'toppings/sot-toi.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(14, 'Nấm Thêm', '10000.00', 1, 'toppings/nam-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(15, 'Ớt Jalapeño', '7000.00', 1, 'toppings/ot-jalapeno.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(16, 'Tôm Thêm', '30000.00', 1, 'toppings/tom-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(17, 'Mực Thêm', '25000.00', 1, 'toppings/muc-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(18, 'Xúc Xích', '15000.00', 1, 'toppings/xuc-xich.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(19, 'Pepperoni', '20000.00', 1, 'toppings/pepperoni.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25'),
(20, 'Dứa Thêm', '8000.00', 1, 'toppings/dua-them.jpg', '2025-05-30 21:27:25', '2025-05-30 21:27:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `user_rank_id` bigint UNSIGNED DEFAULT NULL,
  `total_spending` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_orders` int NOT NULL DEFAULT '0',
  `rank_updated_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `full_name`, `email`, `phone`, `avatar`, `google_id`, `balance`, `user_rank_id`, `total_spending`, `total_orders`, `rank_updated_at`, `active`, `email_verified_at`, `password`, `deleted_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'spinka.sandy', 'Celia Ratke', 'daren32@example.net', '323-763-7047', 'avatars/default.jpg', NULL, '380.35', 1, '0.00', 0, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:14', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, '50VZGb12fM', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(2, 'marcos64', 'Mrs. Kacie Schuster IV', 'hdurgan@example.com', '425-681-0181', 'avatars/default.jpg', NULL, '471.77', 1, '0.00', 0, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'ZhiS9tMQCc', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(3, 'therese.batz', 'Gene Shanahan Sr.', 'kris.estelle@example.com', '+1.848.993.9492', 'avatars/default.jpg', NULL, '837.69', 1, '0.00', 0, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'Oi9gyLar13', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(4, 'elangworth', 'Prof. Dylan Gerhold', 'emory.klocko@example.com', '+13519331456', 'avatars/default.jpg', NULL, '431.70', 1, '0.00', 0, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'D1lSeXLomI', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(5, 'moen.beverly', 'Maria Kessler', 'ikutch@example.org', '(559) 422-1365', 'avatars/default.jpg', NULL, '569.75', 1, '0.00', 0, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'GgFr5b4hzW', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(6, 'yryan', 'Dariana Cruickshank', 'chelsey.mcclure@example.com', '341.859.3488', 'avatars/default.jpg', NULL, '332.87', 2, '1500000.00', 6, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'wUDRM9JGWV', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(7, 'mollie33', 'Lucas Champlin', 'madilyn.lemke@example.com', '+1.224.301.7503', 'avatars/default.jpg', NULL, '479.06', 2, '1500000.00', 6, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, '503qYJLhd7', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(8, 'jailyn.borer', 'Willis Gislason', 'wisozk.nicolette@example.org', '+1 (754) 458-1533', 'avatars/default.jpg', NULL, '596.70', 2, '1500000.00', 6, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'JMf4GCv3c5', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(9, 'emile06', 'Prof. Benjamin Murray', 'pascale93@example.net', '603-755-9473', 'avatars/default.jpg', NULL, '808.88', 2, '1500000.00', 6, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'O9TSymmpyq', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(10, 'meredith94', 'Makayla Little Sr.', 'lynch.okey@example.net', '1-754-871-3981', 'avatars/default.jpg', NULL, '467.01', 2, '1500000.00', 6, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'SEADL6BLAQ', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(11, 'spadmin', 'Administrator', 'admin@gmail.com', '848-368-5803', 'avatars/default.jpg', NULL, '384.24', 3, '4000000.00', 16, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$6Vl/VCAxZROa846PFWg3c.BAWf8cHHjCphNcFUNbG55SOT0.ND3Ha', NULL, 'tox7ja5f0s', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(12, 'customer', 'Test Customer', 'customer@example.com', '+1-734-968-3301', 'avatars/default.jpg', NULL, '851.94', 3, '4000000.00', 16, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:15', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, '7rRvV0vbjI', '2025-05-30 14:27:15', '2025-05-30 14:27:15'),
(13, 'manager1', 'Nguyễn Văn Quản Lý', 'manager1@devfoods.com', '629-222-7442', 'avatars/default.jpg', NULL, '595.64', 3, '4000000.00', 16, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$escwi/nM3eBuqGOOjEfK9O.m2hntNVFbhAKewDgaJO7B8wtGMtxoG', NULL, '46VdKYle02', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(14, 'manager2', 'Trần Thị Quản Lý', 'manager2@devfoods.com', '+1-484-622-2400', 'avatars/default.jpg', NULL, '492.54', 3, '4000000.00', 16, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$ghzoT7vf0UKqwHUYVY1Qlee.MDkPwdrsV6SZZkCEL60hxr8kivYLy', NULL, 'M4QcrLzvsB', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(15, 'manager3', 'Lê Minh Quản Lý', 'manager3@devfoods.com', '(951) 394-0229', 'avatars/default.jpg', NULL, '189.86', 3, '4000000.00', 16, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$LGn43kK7wkCk5DFi3pHSUe/UnVbq0QhvYvN1Oe.NgrRBVEheTUqjm', NULL, 'I33Lo22JYt', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(16, 'manager1_vn', 'Nguyễn Văn Quản Lý', 'manager1@example.com', '0901234567', NULL, NULL, '0.00', 4, '8000000.00', 31, '2025-05-31 10:11:00', 1, NULL, '$2y$12$Mk03ATeKpimzE7FCeWXp5O8uRw0jf1OargsvDhs7FBcgvc4luMDqq', NULL, NULL, '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(17, 'manager2_vn', 'Trần Thị Quản Lý', 'manager2@example.com', '0912345678', NULL, NULL, '0.00', 4, '8000000.00', 31, '2025-05-31 10:11:00', 1, NULL, '$2y$12$7iNyuw2QoySmydrTbtkIUuz2elJ42i66pj.7QU6YqtBdCVBqlNPzi', NULL, NULL, '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(18, 'manager3_vn', 'Lê Văn Quản Lý', 'manager3@example.com', '0923456789', NULL, NULL, '0.00', 4, '8000000.00', 31, '2025-05-31 10:11:00', 1, NULL, '$2y$12$TbgrNXyvJEAgqPwkZwuJa.jad8Z7JLE2hzTXW3o6N0GPGeOzy.SQC', NULL, NULL, '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(19, 'yvolkman', 'Tavares Schumm', 'wiza.consuelo@example.org', '458-364-5365', 'avatars/default.jpg', NULL, '30.38', 4, '8000000.00', 31, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'rJegHKoW0a', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(20, 'xweber', 'Mr. Cory Wiegand DDS', 'fmarquardt@example.org', '1-351-933-7538', 'avatars/default.jpg', NULL, '979.46', 4, '8000000.00', 31, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'AxtA1PqJYU', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(21, 'tdietrich', 'Amber Frami', 'jpowlowski@example.com', '+1.816.566.4387', 'avatars/default.jpg', NULL, '586.89', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'kZSjr1S3Fp', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(22, 'allen06', 'Aleen Kiehn', 'koch.kimberly@example.net', '689.579.8657', 'avatars/default.jpg', NULL, '144.57', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'Wvv1CKeSqy', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(23, 'gracie18', 'Anjali Trantow', 'holly05@example.org', '+1 (463) 480-0852', 'avatars/default.jpg', NULL, '522.91', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'dCWmqw5XuG', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(24, 'marisa.emard', 'Yvonne Wuckert', 'bailey08@example.net', '928.349.2813', 'avatars/default.jpg', NULL, '444.83', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'tFnJIhZ8bL', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(25, 'timmy.paucek', 'Haleigh Lowe', 'makenzie99@example.net', '(918) 693-9743', 'avatars/default.jpg', NULL, '566.70', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, 'D3DqpQ6FyG', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(26, 'yheathcote', 'Prof. Darrion Strosin II', 'lacey.toy@example.com', '+18657864725', 'avatars/default.jpg', NULL, '914.39', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, '3GFR0b5rNf', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(27, 'eziemann', 'Dakota Wehner', 'wbreitenberg@example.org', '1-878-745-0444', 'avatars/default.jpg', NULL, '947.34', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, '6xYFUV8xyB', '2025-05-30 14:27:16', '2025-05-30 14:27:16'),
(28, 'jlowe', 'Meagan Mohr II', 'stehr.wilma@example.org', '+1-385-570-6914', 'avatars/default.jpg', NULL, '173.00', 5, '16000000.00', 61, '2025-05-31 10:11:00', 1, '2025-05-30 14:27:16', '$2y$12$T2SM7tqHLUgoH7vletICy.BxgXnYxb5OfOyt2nTzB.7Nntpi1mMa.', NULL, '9LOZ7NkjjR', '2025-05-30 14:27:16', '2025-05-30 14:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_discount_codes`
--

CREATE TABLE `user_discount_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `status` enum('available','used_up','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_used_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_ranks`
--

CREATE TABLE `user_ranks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#CD7F32',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_spending` decimal(12,2) NOT NULL DEFAULT '0.00',
  `min_orders` int NOT NULL DEFAULT '0',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `benefits` json DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_ranks`
--

INSERT INTO `user_ranks` (`id`, `name`, `slug`, `color`, `icon`, `min_spending`, `min_orders`, `discount_percentage`, `benefits`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Đồng', 'dong', '#cd7f32', NULL, '0.00', 0, '0.00', '\"[\\\"Tích điểm cơ bản\\\"]\"', 1, 1, '2025-05-30 21:31:13', '2025-05-31 16:20:25'),
(2, 'Bạc', 'bac', '#c0c0c0', NULL, '1000000.00', 5, '2.00', '\"[\\\"Tích điểm cơ bản\\\",\\\"rtjryjyjyj\\\"]\"', 2, 1, '2025-05-30 21:31:13', '2025-05-31 16:17:28'),
(3, 'Vàng', 'vang', '#ffd700', NULL, '3000000.00', 15, '5.00', '\"[\\\"Tích điểm cơ bản\\\"]\"', 3, 1, '2025-05-30 21:31:13', '2025-05-31 16:20:11'),
(4, 'Bạch Kim', 'bach-kim', '#e5e4e2', NULL, '7000000.00', 30, '8.00', '\"[\\\"Tích điểm cơ bản\\\"]\"', 4, 1, '2025-05-30 21:31:13', '2025-05-31 16:19:48'),
(5, 'Kim Cương', 'kim-cuong', '#b9f2ff', NULL, '15000000.00', 60, '12.00', '\"[\\\"Tích điểm cơ bản\\\"]\"', 5, 1, '2025-05-30 21:31:13', '2025-05-31 16:20:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_rank_history`
--

CREATE TABLE `user_rank_history` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `old_rank_id` bigint UNSIGNED DEFAULT NULL,
  `new_rank_id` bigint UNSIGNED NOT NULL,
  `total_spending` decimal(12,2) NOT NULL,
  `total_orders` int NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(2, 2, 2, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(3, 3, 3, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(4, 4, 4, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(5, 5, 5, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(6, 6, 1, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(7, 7, 2, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(8, 8, 3, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(9, 9, 4, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(10, 10, 5, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(11, 11, 1, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(12, 12, 2, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(13, 13, 3, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(14, 14, 4, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(15, 15, 5, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(16, 16, 1, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(17, 17, 2, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(18, 18, 3, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(19, 19, 4, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(20, 20, 5, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(21, 21, 1, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(22, 22, 2, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(23, 23, 3, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(24, 24, 4, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(25, 25, 5, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(26, 26, 1, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(27, 27, 2, '2025-05-31 10:53:42', '2025-05-31 10:53:42'),
(28, 28, 3, '2025-05-31 10:53:42', '2025-05-31 10:53:42');

-- --------------------------------------------------------

--
-- Table structure for table `variant_attributes`
--

CREATE TABLE `variant_attributes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_attributes`
--

INSERT INTO `variant_attributes` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Size', '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(2, 'Spice Level', '2025-05-30 21:27:17', '2025-05-30 21:27:17');

-- --------------------------------------------------------

--
-- Table structure for table `variant_values`
--

CREATE TABLE `variant_values` (
  `id` bigint UNSIGNED NOT NULL,
  `variant_attribute_id` bigint UNSIGNED NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_adjustment` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_values`
--

INSERT INTO `variant_values` (`id`, `variant_attribute_id`, `value`, `price_adjustment`, `created_at`, `updated_at`) VALUES
(1, 1, 'Small', '17027.00', '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(2, 1, 'Medium', '16865.00', '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(3, 1, 'Large', '10826.00', '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(4, 2, 'Mild', '13869.00', '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(5, 2, 'Medium', '5337.00', '2025-05-30 21:27:17', '2025-05-30 21:27:17'),
(6, 2, 'Hot', '6457.00', '2025-05-30 21:27:17', '2025-05-30 21:27:17');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

CREATE TABLE `wishlist_items` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_branch_code_unique` (`branch_code`),
  ADD UNIQUE KEY `branches_name_unique` (`name`),
  ADD UNIQUE KEY `branches_address_unique` (`address`),
  ADD UNIQUE KEY `branches_phone_unique` (`phone`),
  ADD UNIQUE KEY `branches_email_unique` (`email`);

--
-- Indexes for table `branch_images`
--
ALTER TABLE `branch_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_images_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `branch_stocks`
--
ALTER TABLE `branch_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_stocks_branch_id_product_variant_id_unique` (`branch_id`,`product_variant_id`),
  ADD KEY `branch_stocks_product_variant_id_foreign` (`product_variant_id`);

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
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_cart_id_product_variant_id_combo_id_unique` (`cart_id`,`product_variant_id`,`combo_id`),
  ADD KEY `cart_items_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `cart_items_combo_id_foreign` (`combo_id`);

--
-- Indexes for table `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_item_toppings_cart_item_id_foreign` (`cart_item_id`),
  ADD KEY `cart_item_toppings_topping_id_foreign` (`topping_id`);

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
-- Indexes for table `combos`
--
ALTER TABLE `combos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combo_items`
--
ALTER TABLE `combo_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `combo_items_combo_id_product_variant_id_unique` (`combo_id`,`product_variant_id`),
  ADD KEY `combo_items_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contacts_email_unique` (`email`),
  ADD UNIQUE KEY `contacts_phone_unique` (`phone`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_codes_code_unique` (`code`),
  ADD KEY `discount_codes_created_by_foreign` (`created_by`),
  ADD KEY `discount_codes_is_active_start_date_end_date_index` (`is_active`,`start_date`,`end_date`),
  ADD KEY `discount_codes_discount_type_is_active_index` (`discount_type`,`is_active`);

--
-- Indexes for table `discount_code_branches`
--
ALTER TABLE `discount_code_branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_code_branches_discount_code_id_branch_id_unique` (`discount_code_id`,`branch_id`),
  ADD KEY `discount_code_branches_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `discount_code_products`
--
ALTER TABLE `discount_code_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_code_products_discount_code_id_foreign` (`discount_code_id`),
  ADD KEY `discount_code_products_product_id_foreign` (`product_id`),
  ADD KEY `discount_code_products_category_id_foreign` (`category_id`),
  ADD KEY `discount_code_products_combo_id_foreign` (`combo_id`);

--
-- Indexes for table `discount_usage_history`
--
ALTER TABLE `discount_usage_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_usage_history_order_id_foreign` (`order_id`),
  ADD KEY `discount_usage_history_branch_id_foreign` (`branch_id`),
  ADD KEY `discount_usage_history_discount_code_id_used_at_index` (`discount_code_id`,`used_at`),
  ADD KEY `discount_usage_history_user_id_used_at_index` (`user_id`,`used_at`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drivers_email_unique` (`email`),
  ADD UNIQUE KEY `drivers_license_number_unique` (`license_number`),
  ADD KEY `drivers_application_id_foreign` (`application_id`);

--
-- Indexes for table `driver_applications`
--
ALTER TABLE `driver_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_applications_email_unique` (`email`),
  ADD UNIQUE KEY `driver_applications_phone_number_unique` (`phone_number`),
  ADD UNIQUE KEY `driver_applications_id_card_number_unique` (`id_card_number`),
  ADD UNIQUE KEY `driver_applications_license_plate_unique` (`license_plate`),
  ADD UNIQUE KEY `driver_applications_driver_license_number_unique` (`driver_license_number`);

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
  ADD KEY `orders_branch_id_foreign` (`branch_id`),
  ADD KEY `orders_driver_id_foreign` (`driver_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`),
  ADD KEY `orders_discount_code_id_foreign` (`discount_code_id`),
  ADD KEY `orders_payment_id_foreign` (`payment_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_cancellations_order_id_foreign` (`order_id`),
  ADD KEY `order_cancellations_cancelled_by_foreign` (`cancelled_by`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `order_items_combo_id_foreign` (`combo_id`);

--
-- Indexes for table `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_toppings_order_item_id_foreign` (`order_item_id`),
  ADD KEY `order_item_toppings_topping_id_foreign` (`topping_id`);

--
-- Indexes for table `order_status_histories`
--
ALTER TABLE `order_status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_status_histories_order_id_foreign` (`order_id`),
  ADD KEY `order_status_histories_changed_by_foreign` (`changed_by`);

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
  ADD UNIQUE KEY `payments_txn_ref_unique` (`txn_ref`),
  ADD KEY `payments_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `point_rules`
--
ALTER TABLE `point_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_created_by_foreign` (`created_by`),
  ADD KEY `products_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `product_imgs`
--
ALTER TABLE `product_imgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_imgs_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_reviews_product_id_foreign` (`product_id`),
  ADD KEY `product_reviews_order_id_foreign` (`order_id`),
  ADD KEY `product_reviews_branch_id_foreign` (`branch_id`),
  ADD KEY `product_reviews_user_id_foreign` (`user_id`);

--
-- Indexes for table `product_toppings`
--
ALTER TABLE `product_toppings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_toppings_product_id_topping_id_unique` (`product_id`,`topping_id`),
  ADD KEY `product_toppings_topping_id_foreign` (`topping_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_variant_details`
--
ALTER TABLE `product_variant_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pvd_product_variant_value_unique` (`product_variant_id`,`variant_value_id`),
  ADD KEY `product_variant_details_variant_value_id_foreign` (`variant_value_id`);

--
-- Indexes for table `promotion_branches`
--
ALTER TABLE `promotion_branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promotion_branches_promotion_program_id_branch_id_unique` (`promotion_program_id`,`branch_id`),
  ADD KEY `promotion_branches_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `promotion_discount_codes`
--
ALTER TABLE `promotion_discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promotion_discount_unique` (`promotion_program_id`,`discount_code_id`),
  ADD KEY `promotion_discount_codes_discount_code_id_foreign` (`discount_code_id`);

--
-- Indexes for table `promotion_programs`
--
ALTER TABLE `promotion_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_programs_created_by_foreign` (`created_by`);

--
-- Indexes for table `return_orders`
--
ALTER TABLE `return_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_orders_order_id_foreign` (`order_id`),
  ADD KEY `return_orders_customer_id_foreign` (`customer_id`);

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
-- Indexes for table `toppings`
--
ALTER TABLE `toppings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_user_rank_id_foreign` (`user_rank_id`);

--
-- Indexes for table `user_discount_codes`
--
ALTER TABLE `user_discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_discount_codes_user_id_discount_code_id_unique` (`user_id`,`discount_code_id`),
  ADD KEY `user_discount_codes_discount_code_id_foreign` (`discount_code_id`);

--
-- Indexes for table `user_ranks`
--
ALTER TABLE `user_ranks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_ranks_slug_unique` (`slug`);

--
-- Indexes for table `user_rank_history`
--
ALTER TABLE `user_rank_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_rank_history_user_id_foreign` (`user_id`),
  ADD KEY `user_rank_history_old_rank_id_foreign` (`old_rank_id`),
  ADD KEY `user_rank_history_new_rank_id_foreign` (`new_rank_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_attributes_name_unique` (`name`);

--
-- Indexes for table `variant_values`
--
ALTER TABLE `variant_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variant_values_variant_attribute_id_foreign` (`variant_attribute_id`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlist_items_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `wishlist_items_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `branch_images`
--
ALTER TABLE `branch_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_stocks`
--
ALTER TABLE `branch_stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2431;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combos`
--
ALTER TABLE `combos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `combo_items`
--
ALTER TABLE `combo_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_code_branches`
--
ALTER TABLE `discount_code_branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_code_products`
--
ALTER TABLE `discount_code_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_usage_history`
--
ALTER TABLE `discount_usage_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `driver_applications`
--
ALTER TABLE `driver_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT for table `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_status_histories`
--
ALTER TABLE `order_status_histories`
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
-- AUTO_INCREMENT for table `point_rules`
--
ALTER TABLE `point_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `product_imgs`
--
ALTER TABLE `product_imgs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=901;

--
-- AUTO_INCREMENT for table `product_toppings`
--
ALTER TABLE `product_toppings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=520;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=811;

--
-- AUTO_INCREMENT for table `product_variant_details`
--
ALTER TABLE `product_variant_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1621;

--
-- AUTO_INCREMENT for table `promotion_branches`
--
ALTER TABLE `promotion_branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion_discount_codes`
--
ALTER TABLE `promotion_discount_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion_programs`
--
ALTER TABLE `promotion_programs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_orders`
--
ALTER TABLE `return_orders`
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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `toppings`
--
ALTER TABLE `toppings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_discount_codes`
--
ALTER TABLE `user_discount_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_ranks`
--
ALTER TABLE `user_ranks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_rank_history`
--
ALTER TABLE `user_rank_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `variant_values`
--
ALTER TABLE `variant_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `branch_images`
--
ALTER TABLE `branch_images`
  ADD CONSTRAINT `branch_images_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_stocks`
--
ALTER TABLE `branch_stocks`
  ADD CONSTRAINT `branch_stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_stocks_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_combo_id_foreign` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  ADD CONSTRAINT `cart_item_toppings_cart_item_id_foreign` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_toppings_topping_id_foreign` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `chat_messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chat_messages_related_order_id_foreign` FOREIGN KEY (`related_order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `combo_items`
--
ALTER TABLE `combo_items`
  ADD CONSTRAINT `combo_items_combo_id_foreign` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `combo_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD CONSTRAINT `discount_codes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `discount_code_branches`
--
ALTER TABLE `discount_code_branches`
  ADD CONSTRAINT `discount_code_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_code_branches_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_code_products`
--
ALTER TABLE `discount_code_products`
  ADD CONSTRAINT `discount_code_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_code_products_combo_id_foreign` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_code_products_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_code_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_usage_history`
--
ALTER TABLE `discount_usage_history`
  ADD CONSTRAINT `discount_usage_history_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `discount_usage_history_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`),
  ADD CONSTRAINT `discount_usage_history_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_usage_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `driver_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
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
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_combo_id_foreign` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  ADD CONSTRAINT `order_item_toppings_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_toppings_topping_id_foreign` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_histories`
--
ALTER TABLE `order_status_histories`
  ADD CONSTRAINT `order_status_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_status_histories_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `product_imgs`
--
ALTER TABLE `product_imgs`
  ADD CONSTRAINT `product_imgs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `product_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_toppings`
--
ALTER TABLE `product_toppings`
  ADD CONSTRAINT `product_toppings_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_toppings_topping_id_foreign` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variant_details`
--
ALTER TABLE `product_variant_details`
  ADD CONSTRAINT `product_variant_details_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variant_details_variant_value_id_foreign` FOREIGN KEY (`variant_value_id`) REFERENCES `variant_values` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion_branches`
--
ALTER TABLE `promotion_branches`
  ADD CONSTRAINT `promotion_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_branches_promotion_program_id_foreign` FOREIGN KEY (`promotion_program_id`) REFERENCES `promotion_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion_discount_codes`
--
ALTER TABLE `promotion_discount_codes`
  ADD CONSTRAINT `promotion_discount_codes_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_discount_codes_promotion_program_id_foreign` FOREIGN KEY (`promotion_program_id`) REFERENCES `promotion_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion_programs`
--
ALTER TABLE `promotion_programs`
  ADD CONSTRAINT `promotion_programs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `return_orders`
--
ALTER TABLE `return_orders`
  ADD CONSTRAINT `return_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `return_orders_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `users_user_rank_id_foreign` FOREIGN KEY (`user_rank_id`) REFERENCES `user_ranks` (`id`);

--
-- Constraints for table `user_discount_codes`
--
ALTER TABLE `user_discount_codes`
  ADD CONSTRAINT `user_discount_codes_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_discount_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_rank_history`
--
ALTER TABLE `user_rank_history`
  ADD CONSTRAINT `user_rank_history_new_rank_id_foreign` FOREIGN KEY (`new_rank_id`) REFERENCES `user_ranks` (`id`),
  ADD CONSTRAINT `user_rank_history_old_rank_id_foreign` FOREIGN KEY (`old_rank_id`) REFERENCES `user_ranks` (`id`),
  ADD CONSTRAINT `user_rank_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `variant_values`
--
ALTER TABLE `variant_values`
  ADD CONSTRAINT `variant_values_variant_attribute_id_foreign` FOREIGN KEY (`variant_attribute_id`) REFERENCES `variant_attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `wishlist_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
