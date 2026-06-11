-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 11 juin 2026 à 15:36
-- Version du serveur : 11.4.9-MariaDB
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion-hoteliere`
--

-- --------------------------------------------------------

--
-- Structure de la table `accounting_logs`
--

DROP TABLE IF EXISTS `accounting_logs`;
CREATE TABLE IF NOT EXISTS `accounting_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `period_type` varchar(191) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_revenue` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transactions_count` int(11) NOT NULL DEFAULT 0,
  `status` varchar(191) NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounting_logs_period_type_start_date_end_date_unique` (`period_type`,`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `key_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `key_card_assigned_at` timestamp NULL DEFAULT NULL,
  `key_card_expires_at` timestamp NULL DEFAULT NULL,
  `customer_name` varchar(191) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_room_id_foreign` (`room_id`),
  KEY `bookings_key_card_id_foreign` (`key_card_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `key_card_id`, `key_card_assigned_at`, `key_card_expires_at`, `customer_name`, `check_in`, `check_out`, `total_price`, `created_at`, `updated_at`) VALUES
(3, 13, NULL, NULL, NULL, 'KOFFI LEON', '2026-06-09', '2026-06-09', 15000.00, '2026-06-09 17:06:06', '2026-06-09 17:06:06'),
(4, 2, NULL, NULL, NULL, 'ouattara amara', '2026-06-09', '2026-06-12', 45000.00, '2026-06-09 17:06:38', '2026-06-09 17:06:38'),
(5, 15, NULL, NULL, NULL, 'OUATT1', '2026-06-11', '2026-06-11', 15000.00, '2026-06-11 08:11:59', '2026-06-11 08:11:59');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6:timer', 'i:1781164934;', 1781164934),
('laravel-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6', 'i:1;', 1781164934);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `catering_items`
--

DROP TABLE IF EXISTS `catering_items`;
CREATE TABLE IF NOT EXISTS `catering_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `category` varchar(191) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `event_booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catering_items_event_booking_id_foreign` (`event_booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `catering_items`
--

INSERT INTO `catering_items` (`id`, `name`, `category`, `unit_price`, `created_at`, `updated_at`, `event_booking_id`) VALUES
(1, 'Sauce Graine au Riz', 'plat', 2500.00, '2026-06-11 14:29:49', '2026-06-11 14:29:49', NULL),
(2, 'Sauce Arrachide au Riz', 'plat', 2500.00, '2026-06-11 14:30:14', '2026-06-11 14:30:14', NULL),
(3, '1 Personne', 'forfait_buffet', 3500.00, '2026-06-11 14:30:46', '2026-06-11 14:30:46', NULL),
(4, 'Poisson Braisé Attieké', 'plat', 7500.00, '2026-06-11 14:31:29', '2026-06-11 14:31:56', NULL),
(5, 'Vin Valpierre', 'boisson', 3500.00, '2026-06-11 14:32:22', '2026-06-11 14:32:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `event_bookings`
--

DROP TABLE IF EXISTS `event_bookings`;
CREATE TABLE IF NOT EXISTS `event_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_space_id` bigint(20) UNSIGNED NOT NULL,
  `formule_location` varchar(191) NOT NULL DEFAULT 'journee',
  `choix_periode` varchar(191) DEFAULT NULL,
  `nombre_heures` int(11) DEFAULT NULL,
  `client_name` varchar(191) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'confirme',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_bookings_event_space_id_foreign` (`event_space_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `event_space_id`, `formule_location`, `choix_periode`, `nombre_heures`, `client_name`, `start_time`, `end_time`, `status`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'journee', NULL, NULL, 'SIFCA', '2026-06-16 10:00:00', '2026-06-16 16:00:00', 'confirme', 9000000.00, '2026-06-11 14:58:35', '2026-06-11 14:58:35');

-- --------------------------------------------------------

--
-- Structure de la table `event_spaces`
--

DROP TABLE IF EXISTS `event_spaces`;
CREATE TABLE IF NOT EXISTS `event_spaces` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `capacity` int(11) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_spaces`
--

INSERT INTO `event_spaces` (`id`, `name`, `type`, `capacity`, `hourly_rate`, `created_at`, `updated_at`) VALUES
(1, 'Salle Katanna', 'conference', 250, 1500000.00, '2026-06-11 14:47:42', '2026-06-11 14:47:54'),
(2, 'Salle Diogaha', 'conference', 5500, 5000000.00, '2026-06-11 14:49:10', '2026-06-11 14:49:15'),
(3, 'Salle Sabaga', 'esplanade', 9000, 5000000.00, '2026-06-11 14:53:28', '2026-06-11 14:53:28');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` varchar(191) NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `housekeeping_logs`
--

DROP TABLE IF EXISTS `housekeeping_logs`;
CREATE TABLE IF NOT EXISTS `housekeeping_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `housekeeping_logs_room_id_foreign` (`room_id`),
  KEY `housekeeping_logs_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `key_cards`
--

DROP TABLE IF EXISTS `key_cards`;
CREATE TABLE IF NOT EXISTS `key_cards` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` varchar(191) NOT NULL,
  `label` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_cards_uid_unique` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '2026_06_08_100157_create_room_types_table', 2),
(4, '2026_06_08_100222_create_rooms_table', 3),
(5, '2026_06_08_100236_create_bookings_table', 4),
(6, '2026_06_08_133950_create_services_table', 5),
(7, '2026_06_08_133959_create_housekeeping_logs_table', 6),
(8, '2026_06_08_141554_create_event_spaces_table', 7),
(9, '2026_06_08_141606_create_event_bookings_table', 8),
(10, '2026_06_08_141616_create_catering_items_table', 9),
(14, '2026_06_08_153129_create_accounting_logs_table', 11),
(13, '2026_06_08_153121_create_payments_table', 10),
(15, '2026_06_09_114356_create_key_cards_table', 12),
(16, '2026_06_09_114546_add_key_card_fields_to_bookings_table', 13),
(17, '2026_06_09_124320_add_currency_to_room_types_table', 14),
(18, '2026_06_11_133540_add_housekeeping_status_to_rooms_table', 15),
(19, '2026_06_11_134344_add_booking_id_to_catering_orders_table', 16),
(20, '2026_06_11_150319_add_booking_options_to_event_bookings_table', 17);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_booking_id` bigint(20) UNSIGNED NOT NULL,
  `payment_type` varchar(191) NOT NULL DEFAULT 'chambre',
  `receipt_number` varchar(191) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'completed',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_receipt_number_unique` (`receipt_number`),
  KEY `payments_event_booking_id_foreign` (`event_booking_id`),
  KEY `payments_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `payments`
--

INSERT INTO `payments` (`id`, `event_booking_id`, `payment_type`, `receipt_number`, `amount`, `payment_method`, `status`, `paid_at`, `user_id`, `notes`, `created_at`, `updated_at`) VALUES
(6, 2, 'chambre', 'REC-20260609-160942', 8000.00, 'cash', 'completed', '2026-06-09 16:09:42', NULL, NULL, '2026-06-09 16:10:08', '2026-06-09 16:10:08'),
(5, 1, 'chambre', 'REC-20260609-160618', 5000.00, 'cash', 'completed', '2026-06-09 16:06:18', NULL, NULL, '2026-06-09 16:06:47', '2026-06-09 16:06:47'),
(8, 5, 'chambre', 'REC-20260611-091852', 15000.00, 'cash', 'validé / encaissé', '2026-06-11 09:19:20', NULL, NULL, '2026-06-11 09:19:20', '2026-06-11 09:19:20'),
(20, 4, 'chambre', 'REC-20260611-120650', 35000.00, 'cash', 'validé / encaissé', '2026-06-11 12:07:00', NULL, NULL, '2026-06-11 12:07:00', '2026-06-11 12:07:00');

-- --------------------------------------------------------

--
-- Structure de la table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_type_id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'disponible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `housekeeping_status` enum('propre','sale','en_cours','maintenance') NOT NULL DEFAULT 'propre',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_number_unique` (`number`),
  KEY `rooms_room_type_id_foreign` (`room_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `number`, `status`, `created_at`, `updated_at`, `housekeeping_status`) VALUES
(1, 4, '21', 'disponible', '2026-06-08 13:21:15', '2026-06-08 13:21:15', 'propre'),
(2, 4, '22', 'occupee', '2026-06-08 13:21:29', '2026-06-09 17:06:38', 'propre'),
(3, 4, '23', 'menage', '2026-06-08 13:21:41', '2026-06-11 09:18:20', 'propre'),
(4, 4, '24', 'disponible', '2026-06-08 13:21:51', '2026-06-08 13:21:51', 'propre'),
(5, 1, '25', 'disponible', '2026-06-08 13:21:59', '2026-06-08 13:21:59', 'propre'),
(6, 3, '26', 'menage', '2026-06-08 13:22:24', '2026-06-11 09:18:20', 'propre'),
(7, 2, '27', 'disponible', '2026-06-08 13:22:32', '2026-06-08 13:22:32', 'propre'),
(8, 2, '28', 'disponible', '2026-06-08 13:22:40', '2026-06-08 13:22:40', 'propre'),
(9, 2, '29', 'disponible', '2026-06-08 13:22:48', '2026-06-08 13:22:48', 'propre'),
(10, 2, '30', 'disponible', '2026-06-08 13:23:00', '2026-06-08 13:23:00', 'propre'),
(11, 1, '31', 'disponible', '2026-06-08 13:23:09', '2026-06-08 13:23:09', 'propre'),
(12, 5, '32', 'disponible', '2026-06-09 16:38:59', '2026-06-09 16:38:59', 'propre'),
(13, 5, '33', 'occupee', '2026-06-09 16:39:07', '2026-06-09 17:06:06', 'propre'),
(14, 5, '34', 'disponible', '2026-06-09 16:39:17', '2026-06-09 16:39:17', 'propre'),
(15, 5, '35', 'occupee', '2026-06-09 16:39:26', '2026-06-11 08:11:59', 'propre');

-- --------------------------------------------------------

--
-- Structure de la table `room_types`
--

DROP TABLE IF EXISTS `room_types`;
CREATE TABLE IF NOT EXISTS `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `base_price` decimal(8,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'EUR',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `base_price`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'Suite', 35000.00, 'XOF', '2026-06-08 13:18:16', '2026-06-09 12:54:05'),
(2, 'Residence', 20000.00, 'XOF', '2026-06-08 13:18:33', '2026-06-09 12:54:19'),
(3, 'Simple  lit double', 15000.00, 'XOF', '2026-06-08 13:18:59', '2026-06-09 12:54:32'),
(4, 'Lit Simple', 15000.00, 'XOF', '2026-06-08 13:19:25', '2026-06-09 12:54:46'),
(5, 'Passage', 5000.00, 'XOF', '2026-06-09 16:27:29', '2026-06-09 16:27:29');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'coulsie@gmail.com', NULL, '$2y$12$yeTcnk5PNKO.tqWPd4Vikeer/Dxcf3EAQO5ceVVszUB4CXnWqvZCe', NULL, '2026-06-08 11:39:21', '2026-06-08 11:45:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
