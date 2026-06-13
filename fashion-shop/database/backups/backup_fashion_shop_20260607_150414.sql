-- Backup of fashion_shop created at 2026-06-07T15:04:14+00:00

DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `collection_id` bigint unsigned DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_category_id_foreign` (`category_id`),
  KEY `banners_collection_id_foreign` (`collection_id`),
  CONSTRAINT `banners_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `banners_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `banners` (`id`, `title`, `banner_type`, `category_id`, `collection_id`, `image_url`, `is_active`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
('1', 'Summer2026', 'all', NULL, NULL, 'banners/banner-ba47ff84-b92f-4985-8db5-0582abfd250b.png', '1', '2026-04-01', '2026-06-30', '2026-04-13 11:26:34', '2026-05-21 16:28:46');

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('fashionshop-cache-boost:mcp:database-schema:mysql::1:0:0:0', 'a:2:{s:6:\"engine\";s:5:\"mysql\";s:6:\"tables\";a:31:{s:7:\"banners\";a:11:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"title\";s:12:\"varchar(255)\";s:11:\"banner_type\";s:12:\"varchar(255)\";s:11:\"category_id\";s:15:\"bigint unsigned\";s:13:\"collection_id\";s:15:\"bigint unsigned\";s:9:\"image_url\";s:12:\"varchar(255)\";s:9:\"is_active\";s:10:\"tinyint(1)\";s:10:\"start_date\";s:4:\"date\";s:8:\"end_date\";s:4:\"date\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:5:\"cache\";a:3:{s:3:\"key\";s:12:\"varchar(255)\";s:5:\"value\";s:10:\"mediumtext\";s:10:\"expiration\";s:3:\"int\";}s:11:\"cache_locks\";a:3:{s:3:\"key\";s:12:\"varchar(255)\";s:5:\"owner\";s:12:\"varchar(255)\";s:10:\"expiration\";s:3:\"int\";}s:5:\"carts\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:10:\"session_id\";s:12:\"varchar(100)\";s:14:\"product_sku_id\";s:15:\"bigint unsigned\";s:8:\"quantity\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:10:\"categories\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:9:\"parent_id\";s:3:\"int\";s:4:\"name\";s:12:\"varchar(255)\";s:4:\"slug\";s:12:\"varchar(255)\";s:9:\"is_active\";s:3:\"int\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:11:\"collections\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(255)\";s:4:\"slug\";s:12:\"varchar(255)\";s:13:\"thumbnail_url\";s:12:\"varchar(255)\";s:11:\"description\";s:4:\"text\";s:9:\"is_active\";s:3:\"int\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:26:\"customer_membership_levels\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:12:\"int unsigned\";s:13:\"customer_code\";s:12:\"varchar(255)\";s:19:\"membership_level_id\";s:12:\"int unsigned\";s:6:\"points\";s:6:\"bigint\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:9:\"employees\";a:5:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:13:\"employee_code\";s:12:\"varchar(255)\";s:6:\"salary\";s:12:\"varchar(255)\";s:9:\"hire_date\";s:4:\"date\";}s:11:\"failed_jobs\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"uuid\";s:12:\"varchar(255)\";s:10:\"connection\";s:4:\"text\";s:5:\"queue\";s:4:\"text\";s:7:\"payload\";s:8:\"longtext\";s:9:\"exception\";s:8:\"longtext\";s:9:\"failed_at\";s:9:\"timestamp\";}s:11:\"flash_sales\";a:15:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(255)\";s:13:\"discount_type\";s:12:\"varchar(255)\";s:14:\"discount_value\";s:13:\"decimal(12,2)\";s:5:\"scope\";s:12:\"varchar(255)\";s:11:\"category_id\";s:3:\"int\";s:13:\"collection_id\";s:3:\"int\";s:10:\"product_id\";s:3:\"int\";s:11:\"usage_limit\";s:3:\"int\";s:10:\"used_count\";s:3:\"int\";s:10:\"start_date\";s:8:\"datetime\";s:8:\"end_date\";s:8:\"datetime\";s:9:\"is_active\";s:10:\"tinyint(1)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:11:\"job_batches\";a:10:{s:2:\"id\";s:12:\"varchar(255)\";s:4:\"name\";s:12:\"varchar(255)\";s:10:\"total_jobs\";s:3:\"int\";s:12:\"pending_jobs\";s:3:\"int\";s:11:\"failed_jobs\";s:3:\"int\";s:14:\"failed_job_ids\";s:8:\"longtext\";s:7:\"options\";s:10:\"mediumtext\";s:12:\"cancelled_at\";s:3:\"int\";s:10:\"created_at\";s:3:\"int\";s:11:\"finished_at\";s:3:\"int\";}s:4:\"jobs\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:5:\"queue\";s:12:\"varchar(255)\";s:7:\"payload\";s:8:\"longtext\";s:8:\"attempts\";s:16:\"tinyint unsigned\";s:11:\"reserved_at\";s:12:\"int unsigned\";s:12:\"available_at\";s:12:\"int unsigned\";s:10:\"created_at\";s:12:\"int unsigned\";}s:17:\"membership_levels\";a:7:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"name\";s:12:\"varchar(255)\";s:10:\"min_points\";s:12:\"int unsigned\";s:21:\"point_conversion_rate\";s:12:\"int unsigned\";s:13:\"discount_rate\";s:12:\"decimal(5,2)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:10:\"migrations\";a:3:{s:2:\"id\";s:12:\"int unsigned\";s:9:\"migration\";s:12:\"varchar(255)\";s:5:\"batch\";s:3:\"int\";}s:15:\"order_feedbacks\";a:11:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"order_id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:6:\"rating\";s:16:\"tinyint unsigned\";s:7:\"content\";s:4:\"text\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";s:10:\"product_id\";s:15:\"bigint unsigned\";s:14:\"admin_reply_by\";s:15:\"bigint unsigned\";s:11:\"admin_reply\";s:4:\"text\";s:16:\"admin_replied_at\";s:9:\"timestamp\";}s:11:\"order_items\";a:11:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"order_id\";s:12:\"int unsigned\";s:14:\"product_sku_id\";s:12:\"int unsigned\";s:12:\"product_name\";s:12:\"varchar(255)\";s:11:\"product_sku\";s:12:\"varchar(255)\";s:12:\"product_size\";s:12:\"varchar(255)\";s:13:\"product_color\";s:12:\"varchar(255)\";s:8:\"quantity\";s:12:\"int unsigned\";s:5:\"price\";s:13:\"decimal(12,2)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:21:\"order_return_requests\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"order_id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:12:\"request_type\";s:12:\"varchar(255)\";s:6:\"reason\";s:4:\"text\";s:7:\"details\";s:4:\"text\";s:15:\"evidence_images\";s:4:\"json\";s:6:\"status\";s:12:\"varchar(255)\";s:10:\"admin_note\";s:4:\"text\";s:8:\"admin_id\";s:15:\"bigint unsigned\";s:11:\"resolved_at\";s:9:\"timestamp\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:14:\"order_vouchers\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"order_id\";s:15:\"bigint unsigned\";s:10:\"voucher_id\";s:15:\"bigint unsigned\";s:15:\"discount_amount\";s:13:\"decimal(12,2)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:6:\"orders\";a:21:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:12:\"int unsigned\";s:15:\"guest_full_name\";s:12:\"varchar(120)\";s:11:\"guest_email\";s:12:\"varchar(255)\";s:11:\"guest_phone\";s:11:\"varchar(20)\";s:13:\"guest_address\";s:12:\"varchar(255)\";s:10:\"guest_note\";s:4:\"text\";s:10:\"order_code\";s:12:\"varchar(255)\";s:12:\"total_amount\";s:13:\"decimal(12,2)\";s:15:\"discount_amount\";s:13:\"decimal(12,2)\";s:12:\"final_amount\";s:13:\"decimal(12,2)\";s:6:\"status\";s:69:\"enum(\'pending\',\'processing\',\'completed\',\'cancelled\',\'payment_failed\')\";s:15:\"shipping_status\";s:50:\"enum(\'pending\',\'shipping\',\'delivered\',\'cancelled\')\";s:14:\"payment_method\";s:12:\"varchar(255)\";s:13:\"customer_name\";s:12:\"varchar(255)\";s:14:\"customer_email\";s:12:\"varchar(255)\";s:14:\"customer_phone\";s:11:\"varchar(20)\";s:16:\"shipping_address\";s:12:\"varchar(500)\";s:8:\"staff_id\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:21:\"password_reset_tokens\";a:3:{s:5:\"email\";s:12:\"varchar(255)\";s:5:\"token\";s:12:\"varchar(255)\";s:10:\"created_at\";s:9:\"timestamp\";}s:8:\"payments\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"order_id\";s:12:\"int unsigned\";s:6:\"amount\";s:13:\"decimal(12,2)\";s:14:\"payment_method\";s:12:\"varchar(255)\";s:14:\"transaction_id\";s:12:\"varchar(255)\";s:6:\"status\";s:31:\"enum(\'pending\',\'paid\',\'failed\')\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:14:\"product_skuses\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:10:\"product_id\";s:3:\"int\";s:3:\"sku\";s:12:\"varchar(255)\";s:4:\"size\";s:12:\"varchar(255)\";s:5:\"color\";s:12:\"varchar(255)\";s:5:\"stock\";s:3:\"int\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:8:\"products\";a:13:{s:2:\"id\";s:15:\"bigint unsigned\";s:12:\"product_code\";s:12:\"varchar(255)\";s:11:\"category_id\";s:3:\"int\";s:13:\"collection_id\";s:3:\"int\";s:4:\"name\";s:12:\"varchar(255)\";s:4:\"slug\";s:12:\"varchar(255)\";s:11:\"description\";s:4:\"text\";s:10:\"base_price\";s:13:\"decimal(10,2)\";s:14:\"main_image_url\";s:12:\"varchar(255)\";s:18:\"gallery_image_urls\";s:4:\"text\";s:9:\"is_active\";s:3:\"int\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:8:\"sessions\";a:6:{s:2:\"id\";s:12:\"varchar(255)\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:10:\"ip_address\";s:11:\"varchar(45)\";s:10:\"user_agent\";s:4:\"text\";s:7:\"payload\";s:8:\"longtext\";s:13:\"last_activity\";s:3:\"int\";}s:21:\"support_conversations\";a:11:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:8:\"admin_id\";s:15:\"bigint unsigned\";s:7:\"subject\";s:12:\"varchar(255)\";s:6:\"status\";s:12:\"varchar(255)\";s:12:\"contact_name\";s:12:\"varchar(255)\";s:13:\"contact_email\";s:12:\"varchar(255)\";s:15:\"last_message_at\";s:9:\"timestamp\";s:11:\"resolved_at\";s:9:\"timestamp\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:16:\"support_messages\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:23:\"support_conversation_id\";s:15:\"bigint unsigned\";s:9:\"sender_id\";s:15:\"bigint unsigned\";s:11:\"sender_role\";s:12:\"varchar(255)\";s:7:\"message\";s:8:\"longtext\";s:7:\"read_at\";s:9:\"timestamp\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:15:\"user_activities\";a:10:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:12:\"int unsigned\";s:10:\"section_id\";s:12:\"varchar(255)\";s:6:\"action\";s:73:\"enum(\'view\',\'click\',\'add_to_cart\',\'remove_from_cart\',\'checkout\',\'search\')\";s:9:\"target_id\";s:12:\"varchar(255)\";s:11:\"target_type\";s:12:\"varchar(255)\";s:8:\"metadata\";s:4:\"json\";s:10:\"ip_address\";s:11:\"varchar(45)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:13:\"user_vouchers\";a:8:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:10:\"voucher_id\";s:15:\"bigint unsigned\";s:6:\"status\";s:31:\"enum(\'unused\',\'used\',\'expired\')\";s:12:\"collected_at\";s:8:\"datetime\";s:7:\"used_at\";s:8:\"datetime\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:5:\"users\";a:15:{s:2:\"id\";s:15:\"bigint unsigned\";s:8:\"username\";s:12:\"varchar(255)\";s:5:\"email\";s:12:\"varchar(255)\";s:17:\"email_verified_at\";s:9:\"timestamp\";s:8:\"password\";s:12:\"varchar(255)\";s:9:\"full_name\";s:12:\"varchar(255)\";s:12:\"phone_number\";s:12:\"varchar(255)\";s:7:\"address\";s:12:\"varchar(255)\";s:6:\"gender\";s:12:\"varchar(255)\";s:8:\"birthday\";s:4:\"date\";s:6:\"avatar\";s:12:\"varchar(255)\";s:4:\"role\";s:12:\"varchar(255)\";s:14:\"remember_token\";s:12:\"varchar(100)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:8:\"vouchers\";a:17:{s:2:\"id\";s:15:\"bigint unsigned\";s:4:\"code\";s:12:\"varchar(255)\";s:8:\"category\";s:12:\"varchar(255)\";s:11:\"category_id\";s:3:\"int\";s:13:\"collection_id\";s:3:\"int\";s:10:\"product_id\";s:3:\"int\";s:13:\"discount_type\";s:12:\"varchar(255)\";s:14:\"discount_value\";s:13:\"decimal(12,2)\";s:15:\"min_order_value\";s:13:\"decimal(12,2)\";s:12:\"max_discount\";s:13:\"decimal(12,2)\";s:11:\"usage_limit\";s:3:\"int\";s:10:\"used_count\";s:3:\"int\";s:10:\"start_date\";s:8:\"datetime\";s:8:\"end_date\";s:8:\"datetime\";s:9:\"is_active\";s:10:\"tinyint(1)\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}s:10:\"whistlists\";a:6:{s:2:\"id\";s:15:\"bigint unsigned\";s:7:\"user_id\";s:15:\"bigint unsigned\";s:10:\"session_id\";s:12:\"varchar(100)\";s:10:\"product_id\";s:12:\"int unsigned\";s:10:\"created_at\";s:9:\"timestamp\";s:10:\"updated_at\";s:9:\"timestamp\";}}}', '1780842679');

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_sku_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_user_id_product_sku_id_unique` (`user_id`,`product_sku_id`),
  UNIQUE KEY `carts_session_id_product_sku_id_unique` (`session_id`,`product_sku_id`),
  KEY `carts_product_sku_id_foreign` (`product_sku_id`),
  KEY `carts_session_id_index` (`session_id`),
  CONSTRAINT `carts_product_sku_id_foreign` FOREIGN KEY (`product_sku_id`) REFERENCES `product_skuses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `carts` (`id`, `user_id`, `session_id`, `product_sku_id`, `quantity`, `created_at`, `updated_at`) VALUES
('16', NULL, 'GJJSvs8mIvdGLChQI4MkREYqkILNHxT6usrqOSHC', '8', '1', '2026-04-19 15:31:05', '2026-04-19 15:31:05'),
('22', NULL, 'RAoiXT4VLPEc2GhVZRTV3wvKeWUuPkiJ2Y3s3Rp1', '11', '1', '2026-05-09 00:37:59', '2026-05-09 00:37:59'),
('25', NULL, 'mWCtuCPoPxh09LysvvhtvZKdRsCR6ikk0WZ85RTu', '12', '1', '2026-05-09 13:25:27', '2026-05-09 13:25:27'),
('26', NULL, 'mWCtuCPoPxh09LysvvhtvZKdRsCR6ikk0WZ85RTu', '8', '1', '2026-05-09 13:28:18', '2026-05-09 13:28:18'),
('36', '7', NULL, '12', '3', '2026-06-05 02:09:31', '2026-06-05 02:24:07'),
('45', '6', NULL, '12', '1', '2026-06-06 20:35:23', '2026-06-06 20:35:23');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
('1', NULL, 'Quần áo nữ', 'quan-ao-nu', '1', '2026-03-31 02:59:26', '2026-03-31 02:59:26'),
('2', '1', 'Áo Polo', 'ao-polo', '1', '2026-03-31 03:02:06', '2026-03-31 03:02:06'),
('4', NULL, 'Thời trang nam', 'thoi-trang-nam', '1', '2026-04-19 10:15:27', '2026-04-19 10:15:27'),
('5', NULL, 'Unisex', 'unisex', '1', '2026-04-19 10:18:57', '2026-04-19 10:18:57');

DROP TABLE IF EXISTS `collections`;
CREATE TABLE `collections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `collections` (`id`, `name`, `slug`, `thumbnail_url`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Thu đông', 'thu-dong', 'collections/x5sV1dWpLWPBUoeCqy3oS0MCsR63KWb2c5TJwwFb.png', 'bộ sưu tập thu đông', '1', '2026-03-31 16:13:09', '2026-04-11 15:41:52');

DROP TABLE IF EXISTS `customer_membership_levels`;
CREATE TABLE `customer_membership_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `customer_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `membership_level_id` bigint unsigned NOT NULL,
  `points` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_membership_levels_customer_code_unique` (`customer_code`),
  KEY `customer_membership_levels_user_id_foreign` (`user_id`),
  KEY `customer_membership_levels_membership_level_id_foreign` (`membership_level_id`),
  CONSTRAINT `customer_membership_levels_membership_level_id_foreign` FOREIGN KEY (`membership_level_id`) REFERENCES `membership_levels` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customer_membership_levels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customer_membership_levels` (`id`, `user_id`, `customer_code`, `membership_level_id`, `points`, `created_at`, `updated_at`) VALUES
('1', '6', 'KH260419XNQY', '1', '191', '2026-04-19 09:59:41', '2026-06-05 02:28:27'),
('2', '7', 'KH2604191WNJ', '1', '18', '2026-04-19 10:03:51', '2026-04-19 11:23:17'),
('3', '9', 'KH260514INVO', '1', '0', '2026-05-14 15:42:15', '2026-05-14 15:42:15'),
('4', '10', 'KH260604CKFK', '1', '0', '2026-06-04 23:55:28', '2026-06-04 23:55:28'),
('5', '11', 'KH260605UA1Z', '1', '0', '2026-06-05 02:25:13', '2026-06-05 02:25:13');

DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `employee_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  KEY `employees_user_id_foreign` (`user_id`),
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employees` (`id`, `user_id`, `employee_code`, `salary`, `hire_date`) VALUES
('2', '4', 'PM260409IZ2Z', '17000000', '2026-04-14'),
('3', '8', 'SC260419PRNV', '7000000', '2026-04-19');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `flash_sales`;
CREATE TABLE `flash_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `collection_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_sales_category_id_foreign` (`category_id`),
  KEY `flash_sales_collection_id_foreign` (`collection_id`),
  KEY `flash_sales_product_id_foreign` (`product_id`),
  CONSTRAINT `flash_sales_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `flash_sales_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `flash_sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `flash_sales` (`id`, `name`, `discount_type`, `discount_value`, `scope`, `category_id`, `collection_id`, `product_id`, `usage_limit`, `used_count`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
('3', 'Sale hè 2026', 'percent', '10.00', 'all', NULL, NULL, NULL, NULL, '0', '2026-05-01 00:00:00', '2026-07-03 00:00:00', '1', '2026-04-19 10:22:15', '2026-06-05 03:09:47');

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `membership_levels`;
CREATE TABLE `membership_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_points` int unsigned NOT NULL,
  `point_conversion_rate` int unsigned NOT NULL,
  `discount_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `membership_levels` (`id`, `name`, `min_points`, `point_conversion_rate`, `discount_rate`, `created_at`, `updated_at`) VALUES
('1', 'Thành viên mới', '0', '0', '0.00', '2026-04-19 09:59:41', '2026-06-05 02:28:27'),
('2', 'Bạc', '500', '2', '2.00', '2026-04-19 11:23:17', '2026-06-05 02:28:27'),
('3', 'Vàng', '1500', '5', '5.00', '2026-04-19 11:23:17', '2026-06-05 02:28:27'),
('4', 'Bạch kim', '3000', '8', '8.00', '2026-04-19 11:23:17', '2026-06-05 02:28:27'),
('5', 'Kim cương', '6000', '10', '10.00', '2026-04-19 11:23:17', '2026-06-05 02:28:27');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
('1', '0001_01_01_000000_create_users_table', '1'),
('2', '0001_01_01_000001_create_cache_table', '1'),
('3', '0001_01_01_000002_create_jobs_table', '1'),
('4', '2026_02_06_164032_create_categories_table', '1'),
('5', '2026_02_06_164053_create_collections_table', '1'),
('6', '2026_02_06_165044_create_products_table', '1'),
('7', '2026_03_23_132027_create_employees_table', '1'),
('8', '2026_03_31_011932_create_product_skuses_table', '1'),
('9', '2026_03_31_090000_alter_products_gallery_image_urls_to_text', '1'),
('10', '2026_03_31_215825_create_vouchers_table', '2'),
('11', '2026_04_09_221759_create_user_vouchers_table', '2'),
('13', '2026_04_12_000000_create_flash_sales_table', '3'),
('14', '2026_04_12_124543_create_customer_membership_levels_table', '4'),
('15', '2026_04_12_124559_create_membership_levels_table', '4'),
('16', '2026_04_12_193716_create_orders_table', '5'),
('17', '2026_04_12_193811_create_order_items_table', '5'),
('18', '2026_04_12_193820_create_order_vouchers_table', '5'),
('19', '2026_04_12_193846_create_wishlists_table', '5'),
('20', '2026_04_12_193913_create_user_activities_table', '5'),
('21', '2026_04_12_195126_create_payments_table', '5'),
('24', '2026_04_12_205904_create_banners_table', '6'),
('25', '2026_04_13_185354_create_carts_table', '7'),
('26', '2026_04_13_200000_create_reviews_table', '8'),
('27', '2026_04_13_120000_alter_orders_table_for_guest_checkout', '9'),
('28', '2026_04_13_210500_rename_wishlists_to_whistlists_table', '10'),
('29', '2026_04_13_214000_add_session_id_to_whistlists_table', '11'),
('30', '2026_04_13_225500_make_user_id_nullable_on_whistlists_table', '12'),
('31', '2026_04_14_090000_add_checkout_customer_fields_to_orders_table', '13'),
('32', '2026_04_16_120000_convert_status_columns_to_enum', '14'),
('33', '2026_04_17_000000_create_support_conversations_table', '15'),
('34', '2026_04_17_000001_create_support_messages_table', '15'),
('35', '2026_04_19_140000_add_order_columns_to_order_vouchers_table', '16'),
('36', '2026_05_08_000000_add_customer_fields_to_orders_table', '17'),
('37', '2026_05_08_223418_create_order_feedback_table', '18'),
('38', '2026_05_08_235901_update_order_feedbacks_add_product_id_and_drop_reviews_table', '19'),
('39', '2026_05_08_235902_add_admin_reply_fields_to_order_feedbacks', '20'),
('40', '2026_05_08_235811_create_agent_conversations_table', '21'),
('41', '2026_05_14_000001_create_order_return_requests_table', '22'),
('42', '2026_05_31_214323_add_evidence_images_and_resolution_status_to_order_return_requests_table', '23'),
('43', '2026_06_05_000001_add_product_snapshot_to_order_items_table', '24'),
('44', '2026_06_07_213312_add_missing_foreign_keys_to_catalog_and_order_tables', '25');

DROP TABLE IF EXISTS `order_feedbacks`;
CREATE TABLE `order_feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `admin_reply_by` bigint unsigned DEFAULT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `admin_replied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_feedbacks_order_id_unique` (`order_id`),
  KEY `order_feedbacks_user_id_foreign` (`user_id`),
  KEY `order_feedbacks_product_id_foreign` (`product_id`),
  KEY `order_feedbacks_admin_reply_by_foreign` (`admin_reply_by`),
  CONSTRAINT `order_feedbacks_admin_reply_by_foreign` FOREIGN KEY (`admin_reply_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_feedbacks_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_feedbacks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_feedbacks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_feedbacks` (`id`, `order_id`, `user_id`, `rating`, `content`, `created_at`, `updated_at`, `product_id`, `admin_reply_by`, `admin_reply`, `admin_replied_at`) VALUES
('1', '15', '6', '4', 'Áo đẹp rất tốt', '2026-05-08 22:54:19', '2026-05-08 23:45:36', '3', '1', 'cảm ơn bạn đã ủng hộ!!!', '2026-05-08 23:45:36');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_sku_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_sku_id_foreign` (`product_sku_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_sku_id_foreign` FOREIGN KEY (`product_sku_id`) REFERENCES `product_skuses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_sku_id`, `product_name`, `product_sku`, `product_size`, `product_color`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
('10', '10', '11', NULL, NULL, NULL, NULL, '1', '135000.00', '2026-04-19 15:10:09', '2026-04-19 15:10:09'),
('13', '13', '12', NULL, NULL, NULL, NULL, '1', '150000.00', '2026-05-08 22:00:49', '2026-05-08 22:00:49'),
('14', '14', '11', NULL, NULL, NULL, NULL, '2', '150000.00', '2026-05-08 22:02:08', '2026-05-08 22:02:08'),
('15', '15', '12', NULL, NULL, NULL, NULL, '1', '150000.00', '2026-05-08 22:03:03', '2026-05-08 22:03:03'),
('16', '16', '12', NULL, NULL, NULL, NULL, '1', '150000.00', '2026-05-09 00:39:10', '2026-05-09 00:39:10'),
('19', '19', '12', NULL, NULL, NULL, NULL, '1', '135000.00', '2026-06-01 01:41:44', '2026-06-01 01:41:44'),
('20', '20', '12', NULL, NULL, NULL, NULL, '8', '135000.00', '2026-06-05 01:55:50', '2026-06-05 01:55:50'),
('22', '22', '12', 'Áo phông nam', 'PRD-3-H2OYKH', 'L', 'Trắng', '1', '135000.00', '2026-06-05 03:18:03', '2026-06-05 03:18:03'),
('23', '23', '12', 'Áo phông nam', 'PRD-3-H2OYKH', 'L', 'Trắng', '1', '135000.00', '2026-06-05 03:27:57', '2026-06-05 03:27:57'),
('24', '24', '12', 'Áo phông nam', 'PRD-3-H2OYKH', 'L', 'Trắng', '1', '135000.00', '2026-06-05 03:35:55', '2026-06-05 03:35:55'),
('25', '25', '12', 'Áo phông nam', 'PRD-3-H2OYKH', 'L', 'Trắng', '1', '135000.00', '2026-06-05 03:45:00', '2026-06-05 03:45:00'),
('26', '26', '12', 'Áo phông nam', 'PRD-3-H2OYKH', 'L', 'Trắng', '1', '135000.00', '2026-06-05 03:52:09', '2026-06-05 03:52:09'),
('27', '27', '12', 'Áo phông nam', 'PRD-3-H2OYKH', 'L', 'Trắng', '1', '135000.00', '2026-06-06 20:33:39', '2026-06-06 20:33:39');

DROP TABLE IF EXISTS `order_return_requests`;
CREATE TABLE `order_return_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `request_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `evidence_images` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `admin_id` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_return_requests_order_id_unique` (`order_id`),
  KEY `order_return_requests_user_id_foreign` (`user_id`),
  KEY `order_return_requests_admin_id_foreign` (`admin_id`),
  CONSTRAINT `order_return_requests_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_return_requests_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_return_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_return_requests` (`id`, `order_id`, `user_id`, `request_type`, `reason`, `details`, `evidence_images`, `status`, `admin_note`, `admin_id`, `resolved_at`, `created_at`, `updated_at`) VALUES
('1', '17', '6', 'exchange', 'Màu giao không đúng', NULL, NULL, 'completed', NULL, '1', '2026-05-14 23:38:18', '2026-05-14 23:25:46', '2026-05-14 23:38:18'),
('2', '19', '6', 'exchange', 'khong dung size', 'can doi lai size', '[\"storage/return-requests/FxPSi79JcZWUR6ipzUe3SRzNFu3lWQNMuySLRNYR.jpg\"]', 'pending', NULL, NULL, NULL, '2026-06-01 01:44:08', '2026-06-01 01:44:08');

DROP TABLE IF EXISTS `order_vouchers`;
CREATE TABLE `order_vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `voucher_id` bigint unsigned NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_vouchers_order_id_foreign` (`order_id`),
  KEY `order_vouchers_voucher_id_foreign` (`voucher_id`),
  CONSTRAINT `order_vouchers_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_vouchers_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_vouchers` (`id`, `order_id`, `voucher_id`, `discount_amount`, `created_at`, `updated_at`) VALUES
('1', '9', '6', '21600.00', '2026-04-19 11:06:34', '2026-04-19 11:06:34'),
('2', '20', '6', '129600.00', '2026-06-05 01:55:50', '2026-06-05 01:55:50'),
('3', '27', '6', '16200.00', '2026-06-06 20:33:39', '2026-06-06 20:33:39');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `guest_full_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_note` text COLLATE utf8mb4_unicode_ci,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `final_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled','payment_failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_status` enum('pending','shipping','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_staff_id_foreign` (`staff_id`),
  CONSTRAINT `orders_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `guest_full_name`, `guest_email`, `guest_phone`, `guest_address`, `guest_note`, `order_code`, `total_amount`, `discount_amount`, `final_amount`, `status`, `shipping_status`, `payment_method`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `staff_id`, `created_at`, `updated_at`) VALUES
('5', '7', NULL, NULL, NULL, NULL, NULL, 'OD2026041910574411', '600000.00', '0.00', '600000.00', 'cancelled', 'cancelled', 'cod', 'Nguyễn Duy Chiến', 'duychien@gmail.com', '0338886678', 'Trần Duy Hưng - Hà Nội', NULL, '2026-04-19 10:57:44', '2026-04-19 11:01:43'),
('9', '7', NULL, NULL, NULL, NULL, NULL, 'OD2026041911063464', '210000.00', '21600.00', '188400.00', 'completed', 'delivered', 'cod', 'Nguyễn Duy Chiến', 'duychien@gmail.com', '0338886678', 'Trần Duy Hưng - Hà Nội', NULL, '2026-04-19 11:06:34', '2026-04-19 11:23:17'),
('10', NULL, NULL, NULL, NULL, NULL, NULL, 'OD2026041915100957', '165000.00', '0.00', '165000.00', 'completed', 'delivered', 'cod', 'Nguyễn Duy Chiến', 'Duychien123@gmail.com', '0339861085', 'Hải Dương', NULL, '2026-04-19 15:10:09', '2026-05-08 22:55:34'),
('11', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026050821563436', '230000.00', '0.00', '230000.00', 'cancelled', 'cancelled', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-08 21:56:34', '2026-05-08 21:57:39'),
('12', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026050821580888', '230000.00', '0.00', '230000.00', 'cancelled', 'cancelled', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-08 21:58:08', '2026-05-08 22:00:05'),
('13', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026050822004997', '180000.00', '0.00', '180000.00', 'cancelled', 'cancelled', 'vnpay', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-08 22:00:49', '2026-05-08 22:01:30'),
('14', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026050822020885', '330000.00', '0.00', '330000.00', 'cancelled', 'cancelled', 'stripe', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-08 22:02:08', '2026-05-08 22:02:36'),
('15', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026050822030386', '180000.00', '0.00', '180000.00', 'completed', 'delivered', 'stripe', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-08 22:03:03', '2026-05-08 22:53:50'),
('16', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026050900391024', '180000.00', '0.00', '180000.00', 'completed', 'delivered', 'vnpay', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-09 00:39:10', '2026-05-14 23:32:17'),
('17', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026051015460618', '230000.00', '0.00', '230000.00', 'completed', 'delivered', 'vnpay', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-10 15:46:06', '2026-05-14 23:25:00'),
('18', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026052409060136', '210000.00', '0.00', '210000.00', 'completed', 'delivered', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-05-24 09:06:01', '2026-05-24 09:06:34'),
('19', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060101414449', '165000.00', '0.00', '165000.00', 'completed', 'delivered', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-01 01:41:44', '2026-06-01 01:43:05'),
('20', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060501555064', '1080000.00', '129600.00', '950400.00', 'completed', 'delivered', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-05 01:55:50', '2026-06-05 02:28:27'),
('21', NULL, NULL, NULL, NULL, NULL, NULL, 'OD2026060502382297', '210000.00', '0.00', '210000.00', 'pending', 'pending', 'cod', 'ggwghwrhhw', 'afajfjjh@gmail.com', '0333986211', 'nbnabfabja', NULL, '2026-06-05 02:38:22', '2026-06-05 02:38:22'),
('22', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060503180356', '165000.00', '0.00', '165000.00', 'cancelled', 'cancelled', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-05 03:18:03', '2026-06-05 03:27:19'),
('23', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060503275755', '165000.00', '0.00', '165000.00', 'cancelled', 'cancelled', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-05 03:27:57', '2026-06-05 03:34:51'),
('24', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060503355527', '165000.00', '0.00', '165000.00', 'pending', 'pending', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-05 03:35:55', '2026-06-05 03:35:55'),
('25', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060503450081', '165000.00', '0.00', '165000.00', 'pending', 'pending', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-05 03:45:00', '2026-06-05 03:45:00'),
('26', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060503520920', '165000.00', '0.00', '165000.00', 'pending', 'pending', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-05 03:52:09', '2026-06-05 03:52:09'),
('27', '6', NULL, NULL, NULL, NULL, NULL, 'OD2026060620333926', '165000.00', '16200.00', '148800.00', 'pending', 'pending', 'cod', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', NULL, '2026-06-06 20:33:39', '2026-06-06 20:33:39');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('duychien123@gmail.com', 'IaSE19EnnysV1Z0wydcAtnKabuBkNpzSXpYBcT25qL40wFNYkq2WufCncjPMwzAV', '2026-06-05 00:46:57');

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_order_id_foreign` (`order_id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_method`, `transaction_id`, `status`, `created_at`, `updated_at`) VALUES
('5', '5', '600000.00', 'cod', 'TXN20260419105744859', 'pending', '2026-04-19 10:57:44', '2026-04-19 10:57:44'),
('6', '9', '188400.00', 'cod', 'TXN20260419110634144', 'paid', '2026-04-19 11:06:34', '2026-04-19 11:23:17'),
('7', '10', '165000.00', 'cod', 'TXN20260419151009255', 'paid', '2026-04-19 15:10:09', '2026-05-08 22:55:34'),
('8', '11', '230000.00', 'cod', 'TXN20260508215634114', 'pending', '2026-05-08 21:56:34', '2026-05-08 21:56:34'),
('9', '12', '230000.00', 'cod', 'TXN20260508215808962', 'pending', '2026-05-08 21:58:08', '2026-05-08 21:58:08'),
('10', '13', '180000.00', 'vnpay', 'TXN20260508220049620', 'failed', '2026-05-08 22:00:49', '2026-05-08 22:01:20'),
('11', '14', '330000.00', 'stripe', 'TXN20260508220208391', 'pending', '2026-05-08 22:02:08', '2026-05-08 22:02:08'),
('12', '15', '180000.00', 'stripe', 'TXN20260508220303103', 'paid', '2026-05-08 22:03:03', '2026-05-08 22:06:20'),
('13', '16', '180000.00', 'vnpay', 'TXN20260509003910284', 'paid', '2026-05-09 00:39:10', '2026-05-14 23:32:17'),
('14', '17', '230000.00', 'vnpay', 'TXN20260510154606378', 'paid', '2026-05-10 15:46:06', '2026-05-10 15:47:36'),
('15', '18', '210000.00', 'cod', 'TXN20260524090601229', 'paid', '2026-05-24 09:06:01', '2026-05-24 09:06:34'),
('16', '19', '165000.00', 'cod', 'TXN20260601014144144', 'paid', '2026-06-01 01:41:44', '2026-06-01 01:43:05'),
('17', '20', '950400.00', 'cod', 'TXN20260605015550676', 'paid', '2026-06-05 01:55:50', '2026-06-05 02:28:27'),
('18', '21', '210000.00', 'cod', 'TXN20260605023822939', 'pending', '2026-06-05 02:38:22', '2026-06-05 02:38:22'),
('19', '22', '165000.00', 'cod', 'TXN20260605031803447', 'pending', '2026-06-05 03:18:03', '2026-06-05 03:18:03'),
('20', '23', '165000.00', 'cod', 'TXN20260605032757437', 'pending', '2026-06-05 03:27:57', '2026-06-05 03:27:57'),
('21', '24', '165000.00', 'cod', 'TXN20260605033555715', 'pending', '2026-06-05 03:35:55', '2026-06-05 03:35:55'),
('22', '25', '165000.00', 'cod', 'TXN20260605034500155', 'pending', '2026-06-05 03:45:00', '2026-06-05 03:45:00'),
('23', '26', '165000.00', 'cod', 'TXN20260605035209650', 'pending', '2026-06-05 03:52:09', '2026-06-05 03:52:09'),
('24', '27', '148800.00', 'cod', 'TXN20260606203339737', 'pending', '2026-06-06 20:33:39', '2026-06-06 20:33:39');

DROP TABLE IF EXISTS `product_skuses`;
CREATE TABLE `product_skuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_skuses_sku_unique` (`sku`),
  KEY `product_skuses_product_id_foreign` (`product_id`),
  CONSTRAINT `product_skuses_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_skuses` (`id`, `product_id`, `sku`, `size`, `color`, `stock`, `created_at`, `updated_at`) VALUES
('8', '2', 'PRD-2-NVEOKV', 'M', 'Xanh', '12', '2026-04-19 10:17:22', '2026-04-19 10:17:22'),
('9', '2', 'PRD-2-8WD3JY', 'S', 'Xanh', '12', '2026-04-19 10:17:22', '2026-04-19 10:17:22'),
('10', '2', 'PRD-2-RPYEIX', 'L', 'Xanh', '10', '2026-04-19 10:17:22', '2026-04-19 10:17:22'),
('11', '3', 'PRD-3-EJLWHW', 'M', 'Trắng', '12', '2026-04-19 10:18:45', '2026-04-19 10:18:45'),
('12', '3', 'PRD-3-H2OYKH', 'L', 'Trắng', '10', '2026-04-19 10:18:45', '2026-04-19 10:18:45'),
('17', '4', 'PRD-4-T4ZR6Z', 'M', 'Đen', '0', '2026-06-05 02:41:14', '2026-06-05 02:41:14'),
('18', '4', 'PRD-4-QFWSCM', 'L', 'Đen', '5', '2026-06-05 02:41:14', '2026-06-05 02:41:14'),
('19', '4', 'PRD-4-6JHKAM', 'M', 'Xanh đậm', '12', '2026-06-05 02:41:14', '2026-06-05 02:41:14'),
('20', '4', 'PRD-4-XQQR7Y', 'L', 'Xanh đậm', '12', '2026-06-05 02:41:14', '2026-06-05 02:41:14');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `collection_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(10,2) NOT NULL,
  `main_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_image_urls` text COLLATE utf8mb4_unicode_ci,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_collection_id_foreign` (`collection_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `product_code`, `category_id`, `collection_id`, `name`, `slug`, `description`, `base_price`, `main_image_url`, `gallery_image_urls`, `is_active`, `created_at`, `updated_at`) VALUES
('2', 'PRD-SOMI', '4', NULL, 'Áo sơ mi nam', 'ao-so-mi-nam', 'Áo sơ mi cho nam', '250000.00', 'storage/products/main/g3nXy9apvMcfKA22YdHngYhmwOJst3LprrpR9SMn.webp', '[\"storage\\/products\\/gallery\\/BfjhhZ0HNvAmPYkdqC9u0sXGv5rxLaJp4pnxMzm5.webp\"]', '1', '2026-04-19 10:17:22', '2026-04-19 10:17:22'),
('3', 'PRD-SHIRT', '4', NULL, 'Áo phông nam', 'ao-phong-nam', 'Áo phông cho nam', '150000.00', 'storage/products/main/FmoW8qiENIWV0gxOSaV6cKq48F3qtljUmRqQ7cJK.webp', '[\"storage\\/products\\/gallery\\/BZN2Ck3jdXb2oBiTD5PssIUmozzA4icfFMuKZn6N.webp\",\"storage\\/products\\/gallery\\/WfzkwALxRmhbhSdslutqdZJGMFU18PSb45raopNi.webp\",\"storage\\/products\\/gallery\\/LWXB1AIOfDvsxXw6zbWxYCdAH7FRz9ud1an7Ycrv.webp\"]', '1', '2026-04-19 10:18:45', '2026-04-19 10:18:45'),
('4', 'PRO-NANG', '5', NULL, 'Áo chống nắng', 'ao-chong-nang', 'Áo chống nắng', '200000.00', 'storage/products/main/AP5xICOVfhvLM6zWyOOMe5iV9rcGJmkcJegYDLt7.webp', '[\"storage\\/products\\/gallery\\/oOGb6Jfjwgtfa2CXM4rvVRy2Lt9mAp52LyT2EO0z.webp\"]', '0', '2026-04-19 10:20:39', '2026-06-05 02:41:14');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('vbtQzp9IfUtWcA3AxGLV21dz2zf2J4Wm5g3Pxo5R', '6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidjMwVjJPckxrYXV6WUJIRklpM1FlMDNSN2xzcDFyVU5sM2FuMENaZSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO31zOjU6InN0YXRlIjtzOjQwOiJ4R3RCNE53azdCZU9RNHlOMDhYRXBpS2tLZW9qY3RSa0N6T3M4UThiIjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O30=', '1780762880');

DROP TABLE IF EXISTS `support_conversations`;
CREATE TABLE `support_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `admin_id` bigint unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hỗ trợ khách hàng',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_conversations_user_id_foreign` (`user_id`),
  KEY `support_conversations_admin_id_foreign` (`admin_id`),
  CONSTRAINT `support_conversations_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `support_conversations` (`id`, `user_id`, `admin_id`, `subject`, `status`, `contact_name`, `contact_email`, `last_message_at`, `resolved_at`, `created_at`, `updated_at`) VALUES
('2', '7', '1', 'Hỗ trợ khách hàng', 'open', NULL, NULL, '2026-04-19 15:24:17', NULL, '2026-04-19 11:12:56', '2026-04-19 15:24:17'),
('3', '6', '1', 'Hỗ trợ khách hàng', 'open', NULL, NULL, '2026-05-14 16:35:29', NULL, '2026-05-08 22:14:46', '2026-05-14 16:35:29');

DROP TABLE IF EXISTS `support_messages`;
CREATE TABLE `support_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_conversation_id` bigint unsigned NOT NULL,
  `sender_id` bigint unsigned DEFAULT NULL,
  `sender_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_messages_support_conversation_id_foreign` (`support_conversation_id`),
  KEY `support_messages_sender_id_foreign` (`sender_id`),
  CONSTRAINT `support_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_messages_support_conversation_id_foreign` FOREIGN KEY (`support_conversation_id`) REFERENCES `support_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `support_messages` (`id`, `support_conversation_id`, `sender_id`, `sender_role`, `message`, `read_at`, `created_at`, `updated_at`) VALUES
('37', '2', '7', 'customer', 'Xin chòa', '2026-04-19 11:21:25', '2026-04-19 11:21:03', '2026-04-19 11:21:25'),
('38', '2', '7', 'customer', 'Tôi muốn được tư vấn 1 số sản phẩm', '2026-04-19 11:21:25', '2026-04-19 11:21:16', '2026-04-19 11:21:25'),
('39', '2', '1', 'admin', 'Tôi có thể giúp gì cho bạn', '2026-04-19 11:21:52', '2026-04-19 11:21:52', '2026-04-19 11:21:52'),
('40', '2', '1', 'admin', 'bạn có thể nói rõ vấn đề được không ạ', '2026-04-19 11:22:22', '2026-04-19 11:22:22', '2026-04-19 11:22:22'),
('41', '2', '7', 'customer', 'Tôi muốn xem đơn hàng của mình thì làm như thế nào?\\', '2026-04-19 15:22:38', '2026-04-19 15:21:36', '2026-04-19 15:22:38'),
('42', '2', '7', 'customer', 'Tôi muốn xem đơn hàng của mình thì làm như thế nào?', '2026-04-19 15:22:38', '2026-04-19 15:21:38', '2026-04-19 15:22:38'),
('43', '2', '1', 'admin', 'Bạn vào phần đơn hàng sau đó tìm kiếm theo mã sản phẩm sẽ thấy được tình trạng của đơn hàng', '2026-04-19 15:22:38', '2026-04-19 15:22:38', '2026-04-19 15:22:38'),
('44', '2', '1', 'admin', 'bạn có câu hỏi gì thêm không?', '2026-04-19 15:24:17', '2026-04-19 15:24:17', '2026-04-19 15:24:17'),
('45', '3', '6', 'customer', 'hello', '2026-05-08 22:15:05', '2026-05-08 22:14:50', '2026-05-08 22:15:05'),
('46', '3', '1', 'admin', 'tooi cos ther giup gif', '2026-05-08 22:15:22', '2026-05-08 22:15:22', '2026-05-08 22:15:22'),
('47', '3', '6', 'customer', 'tôi muốn phong cacvsh trưởng thành hơn', '2026-05-09 15:02:23', '2026-05-09 13:52:58', '2026-05-09 15:02:23'),
('48', '3', '6', 'customer', 'Tooi muoons phong cách trưởng thahf hơn', '2026-05-09 15:02:23', '2026-05-09 13:54:21', '2026-05-09 15:02:23'),
('49', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn: Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo giá, size hoặc phong cách.', '2026-05-09 13:54:21', '2026-05-09 13:54:21', '2026-05-09 13:54:21'),
('50', '3', '6', 'customer', 'Tôi là nam muốn phong cách trưởng thành thì có gọi ý gì không?', '2026-05-09 15:02:23', '2026-05-09 14:52:17', '2026-05-09 15:02:23'),
('51', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn: Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo giá, size hoặc phong cách.', '2026-05-09 14:52:18', '2026-05-09 14:52:17', '2026-05-09 14:52:18'),
('52', '3', '6', 'customer', 'có áo nắng nào không?', '2026-05-09 15:02:23', '2026-05-09 14:53:58', '2026-05-09 15:02:23'),
('53', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn: Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo giá, size hoặc phong cách.', '2026-05-09 14:53:58', '2026-05-09 14:53:58', '2026-05-09 14:53:58'),
('54', '3', '6', 'customer', 'có áo nắng nào không?', '2026-05-09 15:02:23', '2026-05-09 14:59:29', '2026-05-09 15:02:23'),
('55', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn: Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo giá, size hoặc phong cách.', '2026-05-09 14:59:29', '2026-05-09 14:59:29', '2026-05-09 14:59:29'),
('56', '3', '6', 'customer', 'có áo sơ mi nào không?', '2026-05-09 15:02:23', '2026-05-09 14:59:56', '2026-05-09 15:02:23'),
('57', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn: Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo giá, size hoặc phong cách.', '2026-05-09 14:59:56', '2026-05-09 14:59:56', '2026-05-09 14:59:56'),
('58', '3', '6', 'customer', 'có áo sơ mi không?', '2026-05-09 15:22:42', '2026-05-09 15:17:24', '2026-05-09 15:22:42'),
('59', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn về áo sơ mi. Mình thấy hợp nhất là Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo form slim/regular hoặc màu trắng, xanh.', '2026-05-09 15:17:24', '2026-05-09 15:17:24', '2026-05-09 15:17:24'),
('60', '3', '6', 'customer', 'Tôi muốn thay đổi phong cách sang công sở muốn mua áo sơ mi bạn có áo sơ mi phù hợp với mình không', '2026-05-09 15:22:42', '2026-05-09 15:19:02', '2026-05-09 15:22:42'),
('61', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn về áo sơ mi. Mình thấy hợp nhất là Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo form slim/regular hoặc màu trắng, xanh.', '2026-05-09 15:19:03', '2026-05-09 15:19:03', '2026-05-09 15:19:03'),
('62', '3', '6', 'customer', 'ôi muốn thay đổi phong cách sang công sở muốn mua áo sơ mi bạn có áo sơ mi phù hợp với mình không', '2026-05-10 15:53:27', '2026-05-09 15:29:39', '2026-05-10 15:53:27'),
('63', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn về áo sơ mi. Mình thấy hợp nhất là Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo form slim/regular hoặc màu trắng, xanh.', '2026-05-09 15:29:40', '2026-05-09 15:29:40', '2026-05-09 15:29:40'),
('64', '3', '6', 'customer', 'ôi muốn thay đổi phong cách sang công sở muốn mua áo sơ mi bạn có áo sơ mi phù hợp với mình không', '2026-05-10 15:53:27', '2026-05-09 15:32:24', '2026-05-10 15:53:27'),
('65', '3', NULL, 'admin', 'Mình đã lọc nhanh một vài gợi ý phù hợp cho bạn về áo sơ mi. Mình thấy hợp nhất là Áo phông nam. Nếu bạn muốn, mình có thể lọc tiếp theo form slim/regular hoặc màu trắng, xanh.', '2026-05-09 15:32:24', '2026-05-09 15:32:24', '2026-05-09 15:32:24'),
('66', '3', '6', 'customer', 'hello', '2026-05-14 16:02:15', '2026-05-14 16:00:39', '2026-05-14 16:02:15'),
('67', '3', '6', 'customer', 'hello', '2026-05-14 16:02:15', '2026-05-14 16:00:48', '2026-05-14 16:02:15'),
('68', '3', '6', 'customer', 'banj giups tooi dudwojc khoong', '2026-05-14 16:02:15', '2026-05-14 16:01:51', '2026-05-14 16:02:15'),
('69', '3', '1', 'admin', 'toi co the giup gi cho ban', '2026-05-14 16:02:39', '2026-05-14 16:02:39', '2026-05-14 16:02:39'),
('70', '3', '6', 'customer', 'tôi muốn tư vấn', '2026-05-14 16:13:15', '2026-05-14 16:13:08', '2026-05-14 16:13:15'),
('71', '3', '1', 'admin', 'bạn muốn tôi tư vấn cho gì', '2026-05-14 16:13:30', '2026-05-14 16:13:30', '2026-05-14 16:13:30'),
('72', '3', '6', 'customer', 'tôi muốn mua áo', '2026-05-14 16:16:14', '2026-05-14 16:16:08', '2026-05-14 16:16:14'),
('73', '3', '1', 'admin', 'bạn muốn mua như thế nào', '2026-05-14 16:16:29', '2026-05-14 16:16:29', '2026-05-14 16:16:29'),
('74', '3', '6', 'customer', 'heheh', '2026-05-14 16:18:25', '2026-05-14 16:17:41', '2026-05-14 16:18:25'),
('75', '3', '1', 'admin', 'vâng', '2026-05-14 16:19:03', '2026-05-14 16:19:03', '2026-05-14 16:19:03'),
('76', '3', '6', 'customer', '11111', '2026-05-14 16:19:17', '2026-05-14 16:19:10', '2026-05-14 16:19:17'),
('77', '3', '1', 'admin', '1111', '2026-05-14 16:19:17', '2026-05-14 16:19:17', '2026-05-14 16:19:17'),
('78', '3', '6', 'customer', '1111', '2026-05-14 16:23:31', '2026-05-14 16:21:57', '2026-05-14 16:23:31'),
('79', '3', '1', 'admin', 'hey', '2026-05-14 16:29:30', '2026-05-14 16:29:30', '2026-05-14 16:29:30'),
('80', '3', '6', 'customer', 'oke rồi đó', '2026-05-14 16:31:54', '2026-05-14 16:29:36', '2026-05-14 16:31:54'),
('81', '3', '6', 'customer', 'yeah', '2026-05-14 16:33:41', '2026-05-14 16:33:15', '2026-05-14 16:33:41'),
('82', '3', '6', 'customer', 'yeah', '2026-05-14 23:23:36', '2026-05-14 16:35:29', '2026-05-14 23:23:36');

DROP TABLE IF EXISTS `user_activities`;
CREATE TABLE `user_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `section_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` enum('view','click','add_to_cart','remove_from_cart','checkout','search') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_vouchers`;
CREATE TABLE `user_vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `voucher_id` bigint unsigned NOT NULL,
  `status` enum('unused','used','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unused',
  `collected_at` datetime DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_vouchers` (`id`, `user_id`, `voucher_id`, `status`, `collected_at`, `used_at`, `created_at`, `updated_at`) VALUES
('9', '7', '6', 'unused', '2026-06-05 02:21:29', NULL, '2026-06-05 02:21:29', '2026-06-05 02:21:29'),
('10', '6', '6', 'used', '2026-06-05 03:12:26', '2026-06-06 20:33:39', '2026-06-05 03:12:26', '2026-06-06 20:33:39');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `email`, `email_verified_at`, `password`, `full_name`, `phone_number`, `address`, `gender`, `birthday`, `avatar`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
('1', 'admin', 'admin@email.com', NULL, '$2y$12$qZjaG18N5c6J.OfbzMUX4Oq3Zf4eWXtox/nuhodlkvKwesDHcQk5G', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', NULL, '2026-03-31 02:57:15', '2026-03-31 02:57:15'),
('4', 'prodndchien', 'chien@gmail.com', NULL, '$2y$12$RJx18RTtkXCjTnAe0jg9AuQ.lbiEPCAh.yfpeYMUINMxAsiIInpvq', 'Nguyễn Duy Chiến', '0123456789', NULL, 'male', '2026-04-01', 'avatars/48af05bb-373e-46fa-a406-8d8567d11cb7.jpg', 'productmanager', NULL, '2026-04-09 13:35:58', '2026-04-09 14:43:16'),
('6', 'Nguyễn Duy Chiến', 'nguyenduychien2206@gmail.com', NULL, '$2y$12$mDwQd5XhD.AQtXNfNQo.TOAtIOODQ5eh5w7mKRGCmrfxP7WuxLuei', 'Nguyễn Duy Chiến', '0338886678', 'Trần Duy Hưng - Yên Hòa - Hà Nội', 'male', '2004-06-08', NULL, 'customer', NULL, '2026-04-19 09:59:41', '2026-04-19 10:00:57'),
('7', 'duychien', 'duychien@gmail.com', NULL, '$2y$12$w4Cw7tsM/dslb4O2xdazSej4OZx47jppm3Q.vQ0qmup9KCl1E5YBm', 'Nguyễn Duy Chiến', '0338886678', 'Trần Duy Hưng - Hà Nội', 'male', '2005-06-07', NULL, 'customer', NULL, '2026-04-19 10:03:51', '2026-04-19 10:53:44'),
('8', 'servchin', 'ChinChin@gmail.com', NULL, '$2y$12$WSVWT8jXdoLGInrfsJ8bS.prBerF90SjUomv7v5tw3xe5fxFiwrGG', 'Chin', '0940404033', NULL, 'male', '2002-04-26', NULL, 'servicescustomer', NULL, '2026-04-19 11:40:00', '2026-04-19 11:40:00'),
('9', 'duychien2206', 'duychien2206@gmail.com', NULL, '$2y$12$nUgvxtf4xVsNWTINp/vfIe0dKQ4hO0WHABSOsoEQFxKE/wfCJXy8u', NULL, NULL, NULL, NULL, NULL, NULL, 'customer', NULL, '2026-05-14 15:42:15', '2026-05-14 15:42:15'),
('10', 'duychien123', 'duychien123@gmail.com', NULL, '$2y$12$Fybd4It0c8IaeygZUFUfb.kQXl4AYL9Wb0hfbgDiHPz7YNW3y5EH.', 'duychien', NULL, NULL, NULL, NULL, NULL, 'customer', 'P1SrTyQslbbw1iTDbgxuFuJMRWkR034XxpzZoUVD9RBu0Xwik4ES9XJkj4cs', '2026-06-04 23:55:28', '2026-06-05 00:43:36'),
('11', 'duychien220602', 'duychien220602@gmail.com', NULL, '$2y$12$hfDSrjATNbtOTvwpcCRcPei5aj9.6cVXOx509M8CluhTbxkdjutgO', NULL, NULL, NULL, NULL, NULL, NULL, 'customer', NULL, '2026-06-05 02:25:13', '2026-06-05 02:25:13');

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `collection_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_value` decimal(12,2) NOT NULL,
  `max_discount` decimal(12,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  KEY `vouchers_category_id_foreign` (`category_id`),
  KEY `vouchers_collection_id_foreign` (`collection_id`),
  KEY `vouchers_product_id_foreign` (`product_id`),
  CONSTRAINT `vouchers_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vouchers_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vouchers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vouchers` (`id`, `code`, `category`, `category_id`, `collection_id`, `product_id`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
('6', 'SUMMER2026', 'all', NULL, NULL, NULL, 'percent', '12.00', '100000.00', '200000.00', '12', '3', '2026-04-01 00:00:00', '2026-06-30 00:00:00', '1', '2026-04-13 14:12:03', '2026-06-06 20:33:39');

DROP TABLE IF EXISTS `whistlists`;
CREATE TABLE `whistlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whistlists_user_id_product_id_unique` (`user_id`,`product_id`),
  UNIQUE KEY `whistlists_session_id_product_id_unique` (`session_id`,`product_id`),
  KEY `whistlists_product_id_foreign` (`product_id`),
  CONSTRAINT `whistlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `whistlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `whistlists` (`id`, `user_id`, `session_id`, `product_id`, `created_at`, `updated_at`) VALUES
('17', '6', NULL, '3', '2026-05-09 13:22:21', '2026-05-09 13:22:21');

