-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 26 juin 2026 à 12:51
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
  `check_in` datetime NOT NULL,
  `check_out` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'confirmed',
  PRIMARY KEY (`id`),
  KEY `bookings_room_id_foreign` (`room_id`),
  KEY `bookings_key_card_id_foreign` (`key_card_id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `key_card_id`, `key_card_assigned_at`, `key_card_expires_at`, `customer_name`, `check_in`, `check_out`, `total_price`, `created_at`, `updated_at`, `status`) VALUES
(3, 13, NULL, NULL, NULL, 'KOFFI LEON', '2026-06-09 00:00:00', '2026-06-09 00:00:00', 15000.00, '2026-06-09 17:06:06', '2026-06-09 17:06:06', 'confirmed'),
(4, 2, NULL, NULL, NULL, 'ouattara amara', '2026-06-09 00:00:00', '2026-06-12 00:00:00', 45000.00, '2026-06-09 17:06:38', '2026-06-09 17:06:38', 'confirmed'),
(5, 15, NULL, NULL, NULL, 'OUATT1', '2026-06-11 00:00:00', '2026-06-11 00:00:00', 15000.00, '2026-06-11 08:11:59', '2026-06-11 08:11:59', 'confirmed'),
(6, 15, NULL, NULL, NULL, 'KOUAME Seraphin', '2026-06-15 00:00:00', '2026-06-15 00:00:00', 20000.00, '2026-06-15 10:53:32', '2026-06-15 10:53:32', 'confirmed'),
(7, 15, NULL, NULL, NULL, 'Jean de Bonheur', '2026-06-17 00:00:00', '2026-06-17 00:00:00', 20000.00, '2026-06-17 09:16:47', '2026-06-17 09:16:47', 'confirmed'),
(8, 2, NULL, NULL, NULL, 'Jean de Bonheur', '2026-06-17 00:00:00', '2026-06-20 00:00:00', 45000.00, '2026-06-17 11:52:51', '2026-06-17 11:52:51', 'confirmed'),
(9, 5, NULL, NULL, NULL, 'OUATT2', '2026-06-18 00:00:00', '2026-06-21 00:00:00', 105000.00, '2026-06-18 07:24:49', '2026-06-18 07:24:49', 'confirmed'),
(10, 6, NULL, NULL, NULL, 'FELIX001', '2026-06-18 00:00:00', '2026-06-20 00:00:00', 30000.00, '2026-06-18 07:59:03', '2026-06-18 07:59:03', 'confirmed'),
(11, 9, NULL, NULL, NULL, 'SEYDOU BAH', '2026-06-18 00:00:00', '2026-06-21 00:00:00', 60000.00, '2026-06-18 08:23:32', '2026-06-18 08:23:32', 'confirmed'),
(12, 9, NULL, NULL, NULL, 'Koné Nafata', '2026-06-18 00:00:00', '2026-06-21 00:00:00', 60000.00, '2026-06-18 10:05:40', '2026-06-18 10:05:40', 'confirmed'),
(13, 14, NULL, NULL, NULL, 'Yao Magloire', '2026-06-18 00:00:00', '2026-06-18 00:00:00', 15000.00, '2026-06-18 10:08:13', '2026-06-18 10:08:13', 'confirmed'),
(14, 3, NULL, NULL, NULL, 'BAT', '2026-06-18 00:00:00', '2026-06-23 00:00:00', 75000.00, '2026-06-18 12:52:08', '2026-06-18 12:52:08', 'confirmed'),
(15, 15, NULL, NULL, NULL, 'Cisse Inza', '2026-06-18 00:00:00', '2026-06-18 00:00:00', 20000.00, '2026-06-18 15:10:53', '2026-06-18 15:10:53', 'confirmed'),
(16, 2, NULL, NULL, NULL, 'OUATT1', '2026-06-18 00:00:00', '2026-06-26 00:00:00', 120000.00, '2026-06-18 15:31:21', '2026-06-18 15:31:21', 'confirmed'),
(17, 7, NULL, NULL, NULL, 'BAMBA N\'ZO', '2026-06-19 00:00:00', '2026-06-24 00:00:00', 100000.00, '2026-06-19 10:30:42', '2026-06-19 10:30:42', 'confirmed'),
(18, 5, NULL, NULL, NULL, 'Baldé Jean Ismael', '2026-06-22 00:00:00', '2026-06-28 00:00:00', 210000.00, '2026-06-22 08:15:32', '2026-06-22 08:15:32', 'confirmed'),
(19, 11, NULL, NULL, NULL, 'Irié Bi le roi francis', '2026-06-22 00:00:00', '2026-06-24 00:00:00', 70000.00, '2026-06-22 08:42:36', '2026-06-22 08:42:36', 'confirmed'),
(20, 12, NULL, NULL, NULL, 'Irié Bi le roi francis', '2026-06-22 00:00:00', '2026-06-22 00:00:00', 20000.00, '2026-06-22 08:45:01', '2026-06-22 08:45:01', 'confirmed'),
(21, 12, NULL, NULL, NULL, 'QS', '2026-06-22 00:00:00', '2026-06-22 00:00:00', 10000.00, '2026-06-22 09:48:41', '2026-06-22 09:48:41', 'confirmed'),
(22, 12, NULL, NULL, NULL, 'gb', '2026-06-22 00:00:00', '2026-06-22 00:00:00', 15000.00, '2026-06-22 10:08:14', '2026-06-22 10:08:14', 'confirmed'),
(23, 12, NULL, NULL, NULL, 'fd', '2026-07-22 00:00:00', '2026-07-22 00:00:00', 10000.00, '2026-06-22 10:08:56', '2026-06-22 10:11:22', 'confirmed'),
(24, 12, NULL, NULL, NULL, 'NZI David', '2026-06-22 14:00:00', '2026-06-22 14:00:00', 15000.00, '2026-06-22 11:52:05', '2026-06-22 11:52:05', 'confirmed'),
(25, 12, NULL, NULL, NULL, 'QS', '2026-06-22 14:05:00', '2026-06-22 14:05:00', 15000.00, '2026-06-22 12:06:27', '2026-06-22 12:06:27', 'confirmed'),
(26, 12, NULL, NULL, NULL, 'dfgh', '2026-06-22 14:17:00', '2026-06-22 14:17:00', 10000.00, '2026-06-22 12:18:33', '2026-06-22 12:18:33', 'confirmed'),
(36, 9, NULL, NULL, NULL, 'Sylla Oumar', '2026-06-26 08:22:00', '2026-06-28 08:22:00', 40000.00, '2026-06-26 08:24:23', '2026-06-26 08:24:23', 'confirmed'),
(28, 12, NULL, NULL, NULL, 'nn,k', '2026-06-22 14:31:00', '2026-06-22 14:31:00', 10000.00, '2026-06-22 12:32:45', '2026-06-22 12:32:45', 'confirmed'),
(29, 12, NULL, NULL, NULL, 'hj', '2026-06-22 14:12:00', '2026-06-22 14:12:00', 15000.00, '2026-06-22 14:13:33', '2026-06-22 14:13:33', 'confirmed'),
(30, 12, NULL, NULL, NULL, 'n', '2026-06-22 14:13:00', '2026-06-22 14:13:00', 5000.00, '2026-06-22 14:14:07', '2026-06-22 14:14:07', 'confirmed'),
(31, 12, NULL, NULL, NULL, 'rt', '2026-06-22 14:13:00', '2026-06-22 14:13:00', 15000.00, '2026-06-22 14:28:14', '2026-06-22 14:28:14', 'confirmed'),
(32, 4, NULL, NULL, NULL, 'gb', '2026-06-22 14:28:00', '2026-06-25 14:28:00', 15000.00, '2026-06-22 14:28:46', '2026-06-22 14:28:46', 'confirmed'),
(33, 8, NULL, NULL, NULL, 'bh', '2026-06-22 14:39:00', '2026-06-25 14:39:00', 60000.00, '2026-06-22 14:39:20', '2026-06-22 14:39:20', 'confirmed'),
(34, 12, NULL, NULL, NULL, 'n,n', '2026-06-22 14:39:00', '2026-06-22 14:39:00', 10000.00, '2026-06-22 14:40:24', '2026-06-22 14:40:24', 'confirmed'),
(37, 2, NULL, NULL, NULL, 'Kone Koumantien', '2026-06-26 10:25:00', '2026-07-06 12:00:00', 150000.00, '2026-06-26 10:27:20', '2026-06-26 10:27:20', 'confirmed'),
(38, 7, NULL, NULL, NULL, 'KOFFI LEON', '2026-06-26 11:23:00', '2026-06-28 12:00:00', 40000.00, '2026-06-26 11:25:11', '2026-06-26 11:25:11', 'confirmed'),
(39, 13, NULL, NULL, NULL, 'Sylla Oumar', '2026-06-26 11:44:00', '2026-06-26 11:44:00', 15000.00, '2026-06-26 11:44:46', '2026-06-26 11:44:46', 'confirmed'),
(40, 12, NULL, NULL, NULL, 'KONE Lanzéni', '2026-06-26 11:49:00', '2026-06-26 14:49:00', 15000.00, '2026-06-26 11:51:35', '2026-06-26 11:51:35', 'confirmed'),
(41, 14, NULL, NULL, NULL, 'Jean de Bonheur', '2026-06-26 11:57:00', '2026-06-26 15:57:00', 20000.00, '2026-06-26 11:58:10', '2026-06-26 11:58:10', 'confirmed'),
(42, 15, NULL, NULL, NULL, 'Sylla Oumar', '2026-06-26 12:05:00', '2026-06-26 15:05:00', 15000.00, '2026-06-26 12:05:25', '2026-06-26 12:05:25', 'confirmed'),
(43, 8, NULL, NULL, NULL, 'KOUAME Seraphin', '2026-06-26 12:05:00', '2026-06-29 12:00:00', 60000.00, '2026-06-26 12:06:20', '2026-06-26 12:06:20', 'confirmed');

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
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:138:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"ViewAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:9:\"View:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:11:\"Create:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"Update:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"Delete:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"DeleteAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"Restore:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:16:\"ForceDelete:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:19:\"ForceDeleteAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:15:\"RestoreAny:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:14:\"Replicate:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:12:\"Reorder:Role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"ViewAny:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:12:\"View:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:14:\"Create:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"Update:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:14:\"Delete:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:17:\"DeleteAny:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"Restore:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:19:\"ForceDelete:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:22:\"ForceDeleteAny:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:18:\"RestoreAny:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:17:\"Replicate:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:15:\"Reorder:Booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:20:\"ViewAny:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:17:\"View:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:19:\"Create:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:19:\"Update:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:19:\"Delete:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:22:\"DeleteAny:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:20:\"Restore:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:24:\"ForceDelete:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:27:\"ForceDeleteAny:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:23:\"RestoreAny:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:22:\"Replicate:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:20:\"Reorder:CateringItem\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:21:\"ViewAny:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:18:\"View:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:20:\"Create:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:20:\"Update:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:20:\"Delete:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:23:\"DeleteAny:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:21:\"Restore:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:25:\"ForceDelete:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:28:\"ForceDeleteAny:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:24:\"RestoreAny:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:23:\"Replicate:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:21:\"Reorder:CateringOrder\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:20:\"ViewAny:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:17:\"View:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:19:\"Create:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:19:\"Update:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:19:\"Delete:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:22:\"DeleteAny:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:20:\"Restore:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:24:\"ForceDelete:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:27:\"ForceDeleteAny:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:23:\"RestoreAny:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:22:\"Replicate:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:20:\"Reorder:EventBooking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:18:\"ViewAny:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:15:\"View:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:17:\"Create:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:17:\"Update:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:17:\"Delete:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:20:\"DeleteAny:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:18:\"Restore:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:22:\"ForceDelete:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:25:\"ForceDeleteAny:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:21:\"RestoreAny:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:20:\"Replicate:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:18:\"Reorder:EventSpace\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:15:\"ViewAny:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:12:\"View:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:14:\"Create:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:14:\"Update:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:14:\"Delete:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:17:\"DeleteAny:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:15:\"Restore:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:19:\"ForceDelete:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:22:\"ForceDeleteAny:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:18:\"RestoreAny:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:17:\"Replicate:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:15:\"Reorder:KeyCard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:15:\"ViewAny:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:12:\"View:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:14:\"Create:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:14:\"Update:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:14:\"Delete:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:17:\"DeleteAny:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:15:\"Restore:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:19:\"ForceDelete:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:22:\"ForceDeleteAny:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:18:\"RestoreAny:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:17:\"Replicate:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:15:\"Reorder:Payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:12:\"ViewAny:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:9:\"View:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:11:\"Create:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:11:\"Update:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:11:\"Delete:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:101;a:4:{s:1:\"a\";i:102;s:1:\"b\";s:14:\"DeleteAny:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:102;a:4:{s:1:\"a\";i:103;s:1:\"b\";s:12:\"Restore:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:103;a:4:{s:1:\"a\";i:104;s:1:\"b\";s:16:\"ForceDelete:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:104;a:4:{s:1:\"a\";i:105;s:1:\"b\";s:19:\"ForceDeleteAny:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:105;a:4:{s:1:\"a\";i:106;s:1:\"b\";s:15:\"RestoreAny:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:106;a:4:{s:1:\"a\";i:107;s:1:\"b\";s:14:\"Replicate:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:107;a:4:{s:1:\"a\";i:108;s:1:\"b\";s:12:\"Reorder:Room\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:108;a:4:{s:1:\"a\";i:109;s:1:\"b\";s:16:\"ViewAny:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:109;a:4:{s:1:\"a\";i:110;s:1:\"b\";s:13:\"View:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:110;a:4:{s:1:\"a\";i:111;s:1:\"b\";s:15:\"Create:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:111;a:4:{s:1:\"a\";i:112;s:1:\"b\";s:15:\"Update:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:112;a:4:{s:1:\"a\";i:113;s:1:\"b\";s:15:\"Delete:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:113;a:4:{s:1:\"a\";i:114;s:1:\"b\";s:18:\"DeleteAny:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:114;a:4:{s:1:\"a\";i:115;s:1:\"b\";s:16:\"Restore:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:115;a:4:{s:1:\"a\";i:116;s:1:\"b\";s:20:\"ForceDelete:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:116;a:4:{s:1:\"a\";i:117;s:1:\"b\";s:23:\"ForceDeleteAny:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:117;a:4:{s:1:\"a\";i:118;s:1:\"b\";s:19:\"RestoreAny:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:118;a:4:{s:1:\"a\";i:119;s:1:\"b\";s:18:\"Replicate:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:119;a:4:{s:1:\"a\";i:120;s:1:\"b\";s:16:\"Reorder:RoomType\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:120;a:4:{s:1:\"a\";i:121;s:1:\"b\";s:14:\"View:Dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:121;a:4:{s:1:\"a\";i:122;s:1:\"b\";s:13:\"View:Planning\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:122;a:4:{s:1:\"a\";i:123;s:1:\"b\";s:16:\"View:VueChambres\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:123;a:4:{s:1:\"a\";i:124;s:1:\"b\";s:24:\"View:PaymentMethodsChart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:124;a:4:{s:1:\"a\";i:125;s:1:\"b\";s:17:\"View:RevenueChart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:125;a:4:{s:1:\"a\";i:126;s:1:\"b\";s:18:\"View:StatsOverview\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:126;a:4:{s:1:\"a\";i:127;s:1:\"b\";s:12:\"ViewAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:127;a:4:{s:1:\"a\";i:128;s:1:\"b\";s:9:\"View:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:128;a:4:{s:1:\"a\";i:129;s:1:\"b\";s:11:\"Create:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:129;a:4:{s:1:\"a\";i:130;s:1:\"b\";s:11:\"Update:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:130;a:4:{s:1:\"a\";i:131;s:1:\"b\";s:11:\"Delete:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:131;a:4:{s:1:\"a\";i:132;s:1:\"b\";s:14:\"DeleteAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:132;a:4:{s:1:\"a\";i:133;s:1:\"b\";s:12:\"Restore:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:133;a:4:{s:1:\"a\";i:134;s:1:\"b\";s:16:\"ForceDelete:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:134;a:4:{s:1:\"a\";i:135;s:1:\"b\";s:19:\"ForceDeleteAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:135;a:4:{s:1:\"a\";i:136;s:1:\"b\";s:15:\"RestoreAny:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:136;a:4:{s:1:\"a\";i:137;s:1:\"b\";s:14:\"Replicate:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:137;a:4:{s:1:\"a\";i:138;s:1:\"b\";s:12:\"Reorder:User\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"user1\";s:1:\"c\";s:3:\"web\";}}}', 1782560973);

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
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `alert_threshold` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `event_booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catering_items_event_booking_id_foreign` (`event_booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `catering_items`
--

INSERT INTO `catering_items` (`id`, `name`, `category`, `unit_price`, `stock_quantity`, `alert_threshold`, `created_at`, `updated_at`, `event_booking_id`) VALUES
(1, 'Sauce Graine au Riz', 'plat', 2500.00, 0, 5, '2026-06-11 14:29:49', '2026-06-11 14:29:49', NULL),
(2, 'Sauce Arrachide au Riz', 'plat', 2500.00, 0, 5, '2026-06-11 14:30:14', '2026-06-11 14:30:14', NULL),
(3, 'Buffet par Personne', 'forfait_buffet', 3500.00, 0, 5, '2026-06-11 14:30:46', '2026-06-15 14:53:35', NULL),
(4, 'Attieké Poisson Braisé ', 'plat', 7500.00, 0, 5, '2026-06-11 14:31:29', '2026-06-21 09:44:04', NULL),
(5, 'Vin Valpierre', 'boisson', 3500.00, 25, 5, '2026-06-11 14:32:22', '2026-06-25 14:04:12', NULL),
(6, 'Tchep', 'plat', 2500.00, 0, 5, '2026-06-19 16:14:21', '2026-06-19 16:14:21', NULL),
(7, 'Yassa', 'plat', 2500.00, 0, 5, '2026-06-19 16:14:38', '2026-06-19 16:14:38', NULL),
(8, 'Atiéké Poulet Braisé', 'plat', 7500.00, 0, 5, '2026-06-21 09:44:48', '2026-06-21 09:44:48', NULL),
(9, 'Alloko Poulet Braisé', 'plat', 7500.00, 0, 5, '2026-06-21 09:45:32', '2026-06-21 09:45:32', NULL),
(10, 'Alloco Poisson Braisé', 'plat', 7500.00, 0, 5, '2026-06-21 09:46:27', '2026-06-21 09:46:27', NULL),
(11, 'Placali', 'plat', 5000.00, 0, 5, '2026-06-22 07:59:33', '2026-06-22 07:59:33', NULL),
(12, 'Foutou Banane Gouagouassou', 'plat', 5000.00, 0, 5, '2026-06-22 08:00:23', '2026-06-22 08:00:23', NULL),
(13, 'Foutou Igname Gouagouassou', 'plat', 5000.00, 0, 5, '2026-06-22 08:00:52', '2026-06-22 08:00:52', NULL),
(14, 'Foutou Banane Sauce Graine', 'plat', 5000.00, 0, 5, '2026-06-22 08:01:48', '2026-06-22 08:01:48', NULL),
(15, 'Foutou Igname Sauce Gnangnan', 'plat', 5000.00, 0, 5, '2026-06-22 08:02:29', '2026-06-22 08:02:29', NULL),
(16, 'Foutou Banane Sauce Djoumgblé', 'plat', 5000.00, 0, 5, '2026-06-22 08:03:10', '2026-06-22 08:03:10', NULL),
(17, 'Foutou Igname Sauce Djoumgblé', 'plat', 5000.00, 0, 5, '2026-06-22 08:04:02', '2026-06-22 08:04:02', NULL),
(18, 'Kédjénou de Poulet', 'plat', 7500.00, 0, 5, '2026-06-22 08:04:32', '2026-06-22 08:04:32', NULL),
(19, 'Kédjénou de Pintade', 'plat', 10000.00, 0, 5, '2026-06-22 08:06:08', '2026-06-22 08:06:08', NULL),
(20, 'Assiete de Riz', 'plat', 1000.00, 0, 5, '2026-06-22 08:06:34', '2026-06-22 08:06:34', NULL),
(21, 'Assiete d\'attieké', 'plat', 500.00, 0, 5, '2026-06-22 08:07:00', '2026-06-22 08:07:00', NULL),
(22, 'Assiete de foutou', 'plat', 1000.00, 0, 5, '2026-06-22 08:07:24', '2026-06-22 08:07:24', NULL),
(23, 'Assiete d\'Alloco', 'plat', 1000.00, 0, 5, '2026-06-22 08:08:00', '2026-06-22 08:08:00', NULL),
(24, 'Assiete de frite ', 'plat', 1500.00, 0, 5, '2026-06-22 08:08:22', '2026-06-22 08:08:22', NULL),
(25, 'Poulet Frite', 'plat', 10000.00, 0, 5, '2026-06-22 08:08:45', '2026-06-22 08:08:45', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `catering_orders`
--

DROP TABLE IF EXISTS `catering_orders`;
CREATE TABLE IF NOT EXISTS `catering_orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(191) NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_name` varchar(191) NOT NULL DEFAULT 'Client Comptoir',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('en_attente','paye','annule') NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `catering_orders_order_number_unique` (`order_number`),
  KEY `catering_orders_booking_id_foreign` (`booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `catering_orders`
--

INSERT INTO `catering_orders` (`id`, `order_number`, `booking_id`, `client_name`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CMD-20260614-2025-15', NULL, 'Client Comptoir', 5000.00, 'paye', '2026-06-14 20:25:38', '2026-06-14 20:25:54'),
(2, 'CMD-20260614-2026-84', NULL, 'Client Comptoir', 8500.00, 'paye', '2026-06-14 20:26:59', '2026-06-14 20:43:51'),
(3, 'CMD-20260614-2030-59', NULL, 'Client Comptoir', 12500.00, 'paye', '2026-06-14 20:30:20', '2026-06-14 20:41:21'),
(4, 'CMD-20260614-2041-78', NULL, 'Client Comptoir', 0.00, 'paye', '2026-06-14 20:41:03', '2026-06-15 10:03:32'),
(5, 'CMD-20260615-1003-19', NULL, 'Client Comptoir', 7500.00, 'paye', '2026-06-15 10:03:19', '2026-06-15 10:39:33'),
(6, 'CMD-20260615-1011-87', NULL, 'Client Comptoir', 75000.00, 'paye', '2026-06-15 10:11:44', '2026-06-15 10:12:57'),
(7, 'CMD-20260615-1012-23', NULL, 'Client Comptoir', 12500.00, 'paye', '2026-06-15 10:12:23', '2026-06-15 10:12:36'),
(8, 'CMD-20260615-1020-34', NULL, 'Client Comptoir', 15500.00, 'paye', '2026-06-15 10:20:41', '2026-06-15 10:39:57'),
(9, 'CMD-20260615-1024-61', NULL, 'Client Comptoir', 13000.00, 'paye', '2026-06-15 10:24:06', '2026-06-15 10:24:17'),
(10, 'CMD-20260615-1031-25', NULL, 'Client Comptoir', 83000.00, 'paye', '2026-06-15 10:31:28', '2026-06-15 10:31:36'),
(11, 'CMD-20260615-1040-37', NULL, 'Client Comptoir', 27500.00, 'paye', '2026-06-15 10:40:42', '2026-06-15 10:40:53'),
(12, 'CMD-20260615-1046-13', NULL, 'Client Comptoir', 40000.00, 'paye', '2026-06-15 10:46:55', '2026-06-15 10:47:03'),
(13, 'CMD-20260615-1414-44', NULL, 'Client Comptoir', 14500.00, 'paye', '2026-06-15 14:14:43', '2026-06-15 14:14:53'),
(14, 'CMD-20260615-1610-17', NULL, 'Client Comptoir', 880000.00, 'paye', '2026-06-15 16:10:46', '2026-06-15 16:11:01'),
(15, 'CMD-20260616-1442-20', NULL, 'Client Comptoir', 705000.00, 'paye', '2026-06-16 14:42:21', '2026-06-16 14:42:44'),
(16, 'CMD-20260616-1513-92', NULL, 'Client Comptoir', 37000.00, 'paye', '2026-06-16 15:13:23', '2026-06-16 15:13:47'),
(17, 'CMD-20260617-0858-29', NULL, 'Client Comptoir', 362500.00, 'paye', '2026-06-17 08:58:44', '2026-06-17 08:59:09'),
(18, 'CMD-20260617-0933-64', NULL, 'Client Comptoir', 10000.00, 'paye', '2026-06-17 09:33:28', '2026-06-17 15:10:17'),
(19, 'CMD-20260617-1510-25', NULL, 'Client Comptoir', 77500.00, 'paye', '2026-06-17 15:10:01', '2026-06-17 15:10:32'),
(20, 'CMD-20260617-1527-82', NULL, 'Client Comptoir', 40000.00, 'paye', '2026-06-17 15:27:39', '2026-06-17 15:29:19'),
(21, 'CMD-20260617-1530-50', NULL, 'Client Comptoir', 40000.00, 'paye', '2026-06-17 15:30:51', '2026-06-17 15:31:04'),
(22, 'CMD-20260617-1549-96', NULL, 'Client Comptoir', 92500.00, 'paye', '2026-06-17 15:49:40', '2026-06-17 15:49:55'),
(23, 'CMD-20260618-1421-40', NULL, 'Client Comptoir', 12500.00, 'paye', '2026-06-18 14:21:49', '2026-06-18 14:22:02'),
(24, 'CMD-20260618-1512-63', NULL, 'Client Comptoir', 20000.00, 'paye', '2026-06-18 15:12:11', '2026-06-18 15:12:28'),
(25, 'CMD-20260619-1026-69', 14, 'Client Comptoir', 15500.00, 'paye', '2026-06-19 10:26:16', '2026-06-19 10:26:32'),
(26, 'CMD-20260619-1314-53', NULL, 'Client Comptoir', 25000.00, 'paye', '2026-06-19 13:14:32', '2026-06-19 13:15:01'),
(27, 'CMD-20260619-1509-83', NULL, 'Client Comptoir', 17500.00, 'paye', '2026-06-19 15:09:06', '2026-06-19 15:09:37'),
(28, 'CMD-20260619-1617-99', NULL, 'Client Comptoir', 7500.00, 'paye', '2026-06-19 16:17:28', '2026-06-19 16:21:42'),
(29, 'CMD-20260625-0850-48', NULL, 'Client Comptoir', 50000.00, 'en_attente', '2026-06-25 08:50:36', '2026-06-25 08:50:36'),
(30, 'CMD-20260625-1339-77', NULL, 'Client Comptoir', 35000.00, 'en_attente', '2026-06-25 13:39:28', '2026-06-25 13:39:28'),
(31, 'CMD-20260625-1340-10', NULL, 'Client Comptoir', 24500.00, 'en_attente', '2026-06-25 13:40:01', '2026-06-25 13:40:01'),
(33, 'CMD-20260625-1403-76', NULL, 'Client Comptoir', 350000.00, 'paye', '2026-06-25 14:03:46', '2026-06-25 14:04:12');

-- --------------------------------------------------------

--
-- Structure de la table `catering_order_items`
--

DROP TABLE IF EXISTS `catering_order_items`;
CREATE TABLE IF NOT EXISTS `catering_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `catering_order_id` bigint(20) UNSIGNED NOT NULL,
  `catering_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catering_order_items_catering_order_id_foreign` (`catering_order_id`),
  KEY `catering_order_items_catering_item_id_foreign` (`catering_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `catering_order_items`
--

INSERT INTO `catering_order_items` (`id`, `catering_order_id`, `catering_item_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 2500.00, '2026-06-14 20:25:38', '2026-06-14 20:25:38'),
(2, 1, 1, 1, 2500.00, '2026-06-14 20:25:38', '2026-06-14 20:25:38'),
(3, 1, 4, 1, 7500.00, '2026-06-14 20:25:38', '2026-06-14 20:25:38'),
(4, 2, 2, 1, 2500.00, '2026-06-14 20:26:59', '2026-06-14 20:26:59'),
(5, 2, 5, 1, 3500.00, '2026-06-14 20:26:59', '2026-06-14 20:26:59'),
(6, 2, 1, 1, 2500.00, '2026-06-14 20:26:59', '2026-06-14 20:26:59'),
(7, 2, 3, 1, 3500.00, '2026-06-14 20:26:59', '2026-06-14 20:26:59'),
(8, 3, 2, 1, 2500.00, '2026-06-14 20:30:20', '2026-06-14 20:30:20'),
(9, 3, 1, 1, 2500.00, '2026-06-14 20:30:20', '2026-06-14 20:30:20'),
(10, 3, 4, 1, 7500.00, '2026-06-14 20:30:20', '2026-06-14 20:30:20'),
(11, 4, 1, 1, 2500.00, '2026-06-14 20:41:03', '2026-06-14 20:41:03'),
(12, 4, 4, 1, 7500.00, '2026-06-14 20:41:03', '2026-06-14 20:41:03'),
(13, 4, 2, 1, 2500.00, '2026-06-14 20:41:03', '2026-06-14 20:41:03'),
(14, 5, 2, 3, 2500.00, '2026-06-15 10:03:19', '2026-06-15 10:03:19'),
(15, 6, 4, 10, 7500.00, '2026-06-15 10:11:44', '2026-06-15 10:11:44'),
(16, 7, 4, 1, 7500.00, '2026-06-15 10:12:23', '2026-06-15 10:12:23'),
(17, 7, 2, 2, 2500.00, '2026-06-15 10:12:23', '2026-06-15 10:12:23'),
(18, 8, 3, 3, 3500.00, '2026-06-15 10:20:41', '2026-06-15 10:20:41'),
(19, 8, 2, 2, 2500.00, '2026-06-15 10:20:41', '2026-06-15 10:20:41'),
(20, 9, 1, 1, 2500.00, '2026-06-15 10:24:06', '2026-06-15 10:24:06'),
(21, 9, 3, 3, 3500.00, '2026-06-15 10:24:06', '2026-06-15 10:24:06'),
(22, 10, 3, 3, 3500.00, '2026-06-15 10:31:28', '2026-06-15 10:31:28'),
(23, 10, 4, 5, 7500.00, '2026-06-15 10:31:28', '2026-06-15 10:31:28'),
(24, 10, 5, 10, 3500.00, '2026-06-15 10:31:28', '2026-06-15 10:31:28'),
(25, 11, 2, 2, 2500.00, '2026-06-15 10:40:42', '2026-06-15 10:40:42'),
(26, 11, 4, 3, 7500.00, '2026-06-15 10:40:42', '2026-06-15 10:40:42'),
(27, 12, 2, 5, 2500.00, '2026-06-15 10:46:55', '2026-06-15 10:46:55'),
(28, 12, 4, 3, 7500.00, '2026-06-15 10:46:55', '2026-06-15 10:46:55'),
(29, 12, 1, 2, 2500.00, '2026-06-15 10:46:55', '2026-06-15 10:46:55'),
(30, 13, 3, 2, 3500.00, '2026-06-15 14:14:43', '2026-06-15 14:14:43'),
(31, 13, 1, 3, 2500.00, '2026-06-15 14:14:43', '2026-06-15 14:14:43'),
(32, 14, 1, 2, 2500.00, '2026-06-15 16:10:46', '2026-06-15 16:10:46'),
(33, 14, 3, 250, 3500.00, '2026-06-15 16:10:46', '2026-06-15 16:10:46'),
(34, 15, 1, 2, 2500.00, '2026-06-16 14:42:21', '2026-06-16 14:42:21'),
(35, 15, 3, 200, 3500.00, '2026-06-16 14:42:21', '2026-06-16 14:42:21'),
(36, 16, 2, 2, 2500.00, '2026-06-16 15:13:23', '2026-06-16 15:13:23'),
(37, 16, 1, 3, 2500.00, '2026-06-16 15:13:23', '2026-06-16 15:13:23'),
(38, 16, 5, 7, 3500.00, '2026-06-16 15:13:23', '2026-06-16 15:13:23'),
(39, 17, 1, 2, 2500.00, '2026-06-17 08:58:44', '2026-06-17 08:58:44'),
(40, 17, 2, 3, 2500.00, '2026-06-17 08:58:44', '2026-06-17 08:58:44'),
(41, 17, 3, 100, 3500.00, '2026-06-17 08:58:44', '2026-06-17 08:58:44'),
(42, 18, 2, 1, 2500.00, '2026-06-17 09:33:28', '2026-06-17 09:33:28'),
(43, 18, 4, 1, 7500.00, '2026-06-17 09:33:28', '2026-06-17 09:33:28'),
(44, 19, 2, 2, 2500.00, '2026-06-17 15:10:01', '2026-06-17 15:10:01'),
(45, 19, 4, 5, 7500.00, '2026-06-17 15:10:01', '2026-06-17 15:10:01'),
(46, 19, 3, 10, 3500.00, '2026-06-17 15:10:01', '2026-06-17 15:10:01'),
(47, 20, 1, 1, 2500.00, '2026-06-17 15:27:39', '2026-06-17 15:27:39'),
(48, 20, 4, 5, 7500.00, '2026-06-17 15:27:39', '2026-06-17 15:27:39'),
(49, 21, 1, 2, 2500.00, '2026-06-17 15:30:51', '2026-06-17 15:30:51'),
(50, 21, 3, 10, 3500.00, '2026-06-17 15:30:51', '2026-06-17 15:30:51'),
(51, 22, 4, 10, 7500.00, '2026-06-17 15:49:40', '2026-06-17 15:49:40'),
(52, 22, 5, 5, 3500.00, '2026-06-17 15:49:40', '2026-06-17 15:49:40'),
(53, 23, 1, 5, 2500.00, '2026-06-18 14:21:49', '2026-06-18 14:21:49'),
(54, 24, 2, 5, 2500.00, '2026-06-18 15:12:11', '2026-06-18 15:12:11'),
(55, 24, 1, 3, 2500.00, '2026-06-18 15:12:11', '2026-06-18 15:12:11'),
(56, 25, 1, 2, 2500.00, '2026-06-19 10:26:16', '2026-06-19 10:26:16'),
(57, 25, 5, 3, 3500.00, '2026-06-19 10:26:16', '2026-06-19 10:26:16'),
(58, 26, 1, 3, 2500.00, '2026-06-19 13:14:32', '2026-06-19 13:14:32'),
(59, 26, 2, 7, 2500.00, '2026-06-19 13:14:32', '2026-06-19 13:14:32'),
(60, 27, 1, 2, 2500.00, '2026-06-19 15:09:06', '2026-06-19 15:09:06'),
(61, 27, 2, 5, 2500.00, '2026-06-19 15:09:06', '2026-06-19 15:09:06'),
(62, 28, 6, 3, 2500.00, '2026-06-19 16:17:28', '2026-06-19 16:17:28'),
(63, 29, 6, 10, 2500.00, '2026-06-25 08:50:36', '2026-06-25 08:50:36'),
(64, 29, 16, 5, 5000.00, '2026-06-25 08:50:36', '2026-06-25 08:50:36'),
(65, 30, 1, 8, 2500.00, '2026-06-25 13:39:28', '2026-06-25 13:39:28'),
(66, 30, 15, 3, 5000.00, '2026-06-25 13:39:28', '2026-06-25 13:39:28'),
(67, 31, 5, 7, 3500.00, '2026-06-25 13:40:01', '2026-06-25 13:40:01'),
(68, 32, 5, 140, 3500.00, '2026-06-25 13:47:35', '2026-06-25 13:47:35'),
(69, 33, 5, 100, 3500.00, '2026-06-25 14:03:46', '2026-06-25 14:03:46');

-- --------------------------------------------------------

--
-- Structure de la table `daily_closures`
--

DROP TABLE IF EXISTS `daily_closures`;
CREATE TABLE IF NOT EXISTS `daily_closures` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `closure_date` date NOT NULL,
  `theoretical_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `theoretical_mobile` decimal(12,2) NOT NULL DEFAULT 0.00,
  `theoretical_card` decimal(12,2) NOT NULL DEFAULT 0.00,
  `real_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `real_mobile` decimal(12,2) NOT NULL DEFAULT 0.00,
  `real_card` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discrepancy` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'clôturé',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_closures_closure_date_unique` (`closure_date`),
  KEY `daily_closures_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `event_space_id`, `formule_location`, `choix_periode`, `nombre_heures`, `client_name`, `start_time`, `end_time`, `status`, `total_amount`, `created_at`, `updated_at`) VALUES
(5, 3, 'journee', NULL, NULL, 'SOTRA', '2026-06-22 11:09:15', '2026-06-22 16:09:24', 'confirme', 1500000.00, '2026-06-17 11:09:32', '2026-06-17 11:10:01'),
(6, 2, 'journee', NULL, NULL, 'COCA COLA', '2026-06-28 09:30:25', '2026-06-28 17:50:43', 'confirme', 1500000.00, '2026-06-17 11:10:59', '2026-06-17 11:10:59'),
(4, 1, 'journee', NULL, NULL, 'Abidjan Terminal', '2026-06-24 08:30:29', '2026-06-24 17:00:00', 'confirme', 1500000.00, '2026-06-17 11:03:21', '2026-06-17 11:03:21');

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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(20, '2026_06_11_150319_add_booking_options_to_event_bookings_table', 17),
(21, '2026_06_14_152447_create_catering_orders_table', 18),
(22, '2026_06_18_114500_create_permission_tables', 19),
(23, '2026_06_22_082519_add_status_to_bookings_table', 20),
(24, '2026_06_22_114057_change_check_in_and_check_out_to_datetime_in_bookings_table', 21),
(25, '2026_06_25_125340_create_daily_closures_table', 22),
(26, '2026_06_25_132347_add_stock_to_catering_items_table', 23);

-- --------------------------------------------------------

--
-- Structure de la table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

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
  `event_booking_id` bigint(20) UNSIGNED DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `payments`
--

INSERT INTO `payments` (`id`, `event_booking_id`, `payment_type`, `receipt_number`, `amount`, `payment_method`, `status`, `paid_at`, `user_id`, `notes`, `created_at`, `updated_at`) VALUES
(6, 2, 'chambre', 'REC-20260609-160942', 8000.00, 'cash', 'completed', '2026-06-09 16:09:42', NULL, NULL, '2026-06-09 16:10:08', '2026-06-09 16:10:08'),
(5, 1, 'chambre', 'REC-20260609-160618', 5000.00, 'cash', 'completed', '2026-06-09 16:06:18', NULL, NULL, '2026-06-09 16:06:47', '2026-06-09 16:06:47'),
(8, 5, 'chambre', 'REC-20260611-091852', 15000.00, 'cash', 'validé / encaissé', '2026-06-11 09:19:20', NULL, NULL, '2026-06-11 09:19:20', '2026-06-11 09:19:20'),
(20, 4, 'chambre', 'REC-20260611-120650', 35000.00, 'cash', 'validé / encaissé', '2026-06-11 12:07:00', NULL, NULL, '2026-06-11 12:07:00', '2026-06-11 12:07:00'),
(21, NULL, 'chambre', 'REC-RESTO-20260612-100704', 2500.00, 'cash', 'validé / encaissé', '2026-06-12 10:07:06', NULL, NULL, '2026-06-12 10:07:06', '2026-06-12 10:07:06'),
(22, NULL, 'chambre', 'REC-RESTO-20260612-101237', 2500.00, 'cash', 'validé / encaissé', '2026-06-12 10:12:39', NULL, NULL, '2026-06-12 10:12:39', '2026-06-12 10:12:39'),
(23, NULL, 'chambre', 'REC-RESTO-20260612-102802', 3500.00, 'cash', 'validé / encaissé', '2026-06-12 10:28:05', NULL, NULL, '2026-06-12 10:28:05', '2026-06-12 10:28:05'),
(24, NULL, 'chambre', 'REC-RESTO-20260612-103704', 3500.00, 'cash', 'validé / encaissé', '2026-06-12 10:37:06', NULL, NULL, '2026-06-12 10:37:06', '2026-06-12 10:37:06'),
(25, NULL, 'chambre', 'REC-RESTO-20260614-170908', 2500.00, 'cash', 'validé / encaissé', '2026-06-14 17:09:14', NULL, NULL, '2026-06-14 17:09:14', '2026-06-14 17:09:14'),
(26, NULL, 'chambre', 'REC-RESTO-20260614-202554', 5000.00, 'cash', 'validé / encaissé', '2026-06-14 20:25:54', NULL, NULL, '2026-06-14 20:25:54', '2026-06-14 20:25:54'),
(27, NULL, 'chambre', 'REC-RESTO-20260614-204121', 12500.00, 'cash', 'validé / encaissé', '2026-06-14 20:41:21', NULL, NULL, '2026-06-14 20:41:21', '2026-06-14 20:41:21'),
(28, NULL, 'chambre', 'REC-RESTO-20260614-204351', 8500.00, 'mobile_money', 'validé / encaissé', '2026-06-14 20:43:51', NULL, NULL, '2026-06-14 20:43:51', '2026-06-14 20:43:51'),
(29, NULL, 'chambre', 'REC-RESTO-20260614-204418', 2500.00, 'cash', 'validé / encaissé', '2026-06-14 20:44:24', NULL, NULL, '2026-06-14 20:44:24', '2026-06-14 20:44:24'),
(30, NULL, 'chambre', 'REC-RESTO-20260615-100332', 0.00, 'cash', 'validé / encaissé', '2026-06-15 10:03:32', NULL, NULL, '2026-06-15 10:03:32', '2026-06-15 10:03:32'),
(31, NULL, 'chambre', 'REC-RESTO-20260615-101236', 12500.00, 'cash', 'validé / encaissé', '2026-06-15 10:12:36', NULL, NULL, '2026-06-15 10:12:36', '2026-06-15 10:12:36'),
(32, NULL, 'chambre', 'REC-RESTO-20260615-101257', 75000.00, 'cash', 'validé / encaissé', '2026-06-15 10:12:57', NULL, NULL, '2026-06-15 10:12:57', '2026-06-15 10:12:57'),
(33, NULL, 'chambre', 'REC-RESTO-20260615-102417', 13000.00, 'cash', 'validé / encaissé', '2026-06-15 10:24:17', NULL, NULL, '2026-06-15 10:24:17', '2026-06-15 10:24:17'),
(34, NULL, 'chambre', 'REC-RESTO-20260615-103136', 83000.00, 'cash', 'validé / encaissé', '2026-06-15 10:31:36', NULL, NULL, '2026-06-15 10:31:36', '2026-06-15 10:31:36'),
(35, NULL, 'chambre', 'REC-RESTO-20260615-103933', 7500.00, 'cash', 'validé / encaissé', '2026-06-15 10:39:33', NULL, NULL, '2026-06-15 10:39:33', '2026-06-15 10:39:33'),
(36, NULL, 'chambre', 'REC-RESTO-20260615-103957', 15500.00, 'cash', 'validé / encaissé', '2026-06-15 10:39:57', NULL, NULL, '2026-06-15 10:39:57', '2026-06-15 10:39:57'),
(37, NULL, 'chambre', 'REC-RESTO-20260615-104053', 27500.00, 'cash', 'validé / encaissé', '2026-06-15 10:40:53', NULL, NULL, '2026-06-15 10:40:53', '2026-06-15 10:40:53'),
(38, NULL, 'chambre', 'REC-RESTO-20260615-104703', 40000.00, 'cash', 'validé / encaissé', '2026-06-15 10:47:03', NULL, NULL, '2026-06-15 10:47:03', '2026-06-15 10:47:03'),
(40, 3, 'chambre', 'REC-20260615-105222', 5000.00, 'cash', 'validé / encaissé', '2026-06-15 10:52:33', NULL, NULL, '2026-06-15 10:52:33', '2026-06-15 10:52:33'),
(65, NULL, 'chambre', 'REC-RESTO-20260615-161101', 880000.00, 'cash', 'validé / encaissé', '2026-06-15 16:11:01', NULL, NULL, '2026-06-15 16:11:01', '2026-06-15 16:11:01'),
(58, NULL, 'chambre', 'REC-RESTO-20260615-141453', 14500.00, 'cash', 'validé / encaissé', '2026-06-15 14:14:53', NULL, NULL, '2026-06-15 14:14:53', '2026-06-15 14:14:53'),
(60, 1, 'chambre', 'REC-SALLE-20260615-142417', 800000.00, 'card', 'validé / encaissé', '2026-06-15 14:24:28', NULL, NULL, '2026-06-15 14:24:28', '2026-06-15 14:24:28'),
(62, 1, 'chambre', 'REC-SALLE-20260615-144123', 500000.00, 'card', 'validé / encaissé', '2026-06-15 14:41:45', NULL, NULL, '2026-06-15 14:41:45', '2026-06-15 14:41:45'),
(63, 1, 'chambre', 'REC-SALLE-20260615-144924', 50000.00, 'cash', 'validé / encaissé', '2026-06-15 14:49:40', NULL, NULL, '2026-06-15 14:49:40', '2026-06-15 14:49:40'),
(64, 1, 'chambre', 'REC-SALLE-20260615-153610', 82000.00, 'cash', 'validé / encaissé', '2026-06-15 15:36:22', NULL, NULL, '2026-06-15 15:36:22', '2026-06-15 15:36:22'),
(66, NULL, 'chambre', 'REC-RESTO-20260616-144244', 705000.00, 'cash', 'validé / encaissé', '2026-06-16 14:42:44', NULL, NULL, '2026-06-16 14:42:44', '2026-06-16 14:42:44'),
(67, NULL, 'chambre', 'REC-RESTO-20260616-151347', 37000.00, 'wave', 'validé / encaissé', '2026-06-16 15:13:47', NULL, NULL, '2026-06-16 15:13:47', '2026-06-16 15:13:47'),
(68, NULL, 'chambre', 'REC-RESTO-20260617-085909', 362500.00, 'wave', 'validé / encaissé', '2026-06-17 08:59:09', NULL, NULL, '2026-06-17 08:59:09', '2026-06-17 08:59:09'),
(69, 4, 'chambre', 'REC-20260617-085944', 10000.00, 'mobile_money', 'validé / encaissé', '2026-06-17 08:59:51', NULL, NULL, '2026-06-17 08:59:51', '2026-06-17 08:59:51'),
(70, 6, 'chambre', 'REC-20260617-090010', 20000.00, 'card', 'validé / encaissé', '2026-06-17 09:00:17', NULL, NULL, '2026-06-17 09:00:17', '2026-06-17 09:00:17'),
(71, 3, 'chambre', 'REC-20260617-090019', 10000.00, 'cash', 'validé / encaissé', '2026-06-17 09:00:24', NULL, NULL, '2026-06-17 09:00:24', '2026-06-17 09:00:24'),
(72, NULL, 'chambre', 'REC-20260617-091653', 20000.00, 'wave', 'validé / encaissé', '2026-06-17 09:17:06', NULL, NULL, '2026-06-17 09:17:06', '2026-06-17 09:17:06'),
(73, 4, 'chambre', 'REC-SALLE-20260617-110335', 1000000.00, 'wave', 'validé / encaissé', '2026-06-17 11:03:47', NULL, NULL, '2026-06-17 11:03:47', '2026-06-17 11:03:47'),
(74, 4, 'chambre', 'REC-SALLE-20260617-111432', 300000.00, 'cash', 'validé / encaissé', '2026-06-17 11:14:46', NULL, NULL, '2026-06-17 11:14:46', '2026-06-17 11:14:46'),
(75, NULL, 'chambre', 'REC-20260617-111528', 10000.00, 'wave', 'validé / encaissé', '2026-06-17 11:15:38', NULL, NULL, '2026-06-17 11:15:38', '2026-06-17 11:15:38'),
(76, NULL, 'chambre', 'REC-20260617-111821', 10000.00, 'cash', 'validé / encaissé', '2026-06-17 11:18:30', NULL, NULL, '2026-06-17 11:18:30', '2026-06-17 11:18:30'),
(77, 7, 'chambre', 'REC-20260617-113140', 7000.00, 'cash', 'validé / encaissé', '2026-06-17 11:31:53', NULL, NULL, '2026-06-17 11:31:53', '2026-06-17 11:31:53'),
(78, 7, 'chambre', 'REC-20260617-113213', 5000.00, 'wave', 'validé / encaissé', '2026-06-17 11:32:24', NULL, NULL, '2026-06-17 11:32:24', '2026-06-17 11:32:24'),
(79, 8, 'chambre', 'REC-20260617-115256', 25000.00, 'cash', 'validé / encaissé', '2026-06-17 11:53:03', NULL, NULL, '2026-06-17 11:53:03', '2026-06-17 11:53:03'),
(80, 7, 'chambre', 'REC-20260617-123016', 5000.00, 'wave', 'validé / encaissé', '2026-06-17 12:30:23', NULL, NULL, '2026-06-17 12:30:23', '2026-06-17 12:30:23'),
(81, 8, 'chambre', 'REC-20260617-124015', 12000.00, 'orange_money', 'validé / encaissé', '2026-06-17 12:40:24', NULL, NULL, '2026-06-17 12:40:24', '2026-06-17 12:40:24'),
(82, 8, 'chambre', 'REC-20260617-124337', 8000.00, 'mtn_momo', 'validé / encaissé', '2026-06-17 12:43:42', NULL, NULL, '2026-06-17 12:43:42', '2026-06-17 12:43:42'),
(83, 7, 'chambre', 'REC-20260617-133656', 3000.00, 'card', 'validé / encaissé', '2026-06-17 13:37:03', NULL, NULL, '2026-06-17 13:37:03', '2026-06-17 13:37:03'),
(84, 6, 'chambre', 'REC-SALLE-20260617-140941', 1000000.00, 'wave', 'validé / encaissé', '2026-06-17 14:09:49', NULL, NULL, '2026-06-17 14:09:49', '2026-06-17 14:09:49'),
(85, 5, 'chambre', 'REC-SALLE-20260617-145520', 1200000.00, 'wave', 'validé / encaissé', '2026-06-17 14:55:30', NULL, NULL, '2026-06-17 14:55:30', '2026-06-17 14:55:30'),
(86, 4, 'chambre', 'REC-SALLE-20260617-150508', 200000.00, 'wave', 'validé / encaissé', '2026-06-17 15:05:13', NULL, NULL, '2026-06-17 15:05:13', '2026-06-17 15:05:13'),
(87, 5, 'chambre', 'REC-SALLE-20260617-150901', 150000.00, 'mtn_momo', 'validé / encaissé', '2026-06-17 15:09:11', NULL, NULL, '2026-06-17 15:09:11', '2026-06-17 15:09:11'),
(88, NULL, 'chambre', 'REC-RESTO-20260617-151017', 10000.00, 'cash', 'validé / encaissé', '2026-06-17 15:10:17', NULL, NULL, '2026-06-17 15:10:17', '2026-06-17 15:10:17'),
(89, NULL, 'chambre', 'REC-RESTO-20260617-151032', 77500.00, 'wave', 'validé / encaissé', '2026-06-17 15:10:32', NULL, NULL, '2026-06-17 15:10:32', '2026-06-17 15:10:32'),
(90, NULL, 'chambre', 'REC-RESTO-20260617-152745', 40000.00, 'cash', 'validé / encaissé', '2026-06-17 15:27:45', NULL, NULL, '2026-06-17 15:27:45', '2026-06-17 15:27:45'),
(91, NULL, 'chambre', 'REC-RESTO-20260617-152919', 40000.00, 'wave', 'validé / encaissé', '2026-06-17 15:29:19', NULL, NULL, '2026-06-17 15:29:19', '2026-06-17 15:29:19'),
(92, NULL, 'chambre', 'REC-RESTO-20260617-153104', 40000.00, 'orange_money', 'validé / encaissé', '2026-06-17 15:31:04', NULL, NULL, '2026-06-17 15:31:04', '2026-06-17 15:31:04'),
(93, NULL, 'chambre', 'REC-RESTO-20260617-154954', 92500.00, 'cash', 'validé / encaissé', '2026-06-17 15:49:54', NULL, NULL, '2026-06-17 15:49:54', '2026-06-17 15:49:54'),
(94, 9, 'chambre', 'REC-20260618-072503', 85000.00, 'cash', 'validé / encaissé', '2026-06-18 07:25:13', NULL, NULL, '2026-06-18 07:25:13', '2026-06-18 07:25:13'),
(95, 9, 'chambre', 'REC-20260618-073233', 15000.00, 'wave', 'validé / encaissé', '2026-06-18 07:32:41', NULL, NULL, '2026-06-18 07:32:41', '2026-06-18 07:32:41'),
(96, 9, 'chambre', 'REC-20260618-073916', 3000.00, 'wave', 'validé / encaissé', '2026-06-18 07:39:24', NULL, NULL, '2026-06-18 07:39:24', '2026-06-18 07:39:24'),
(97, 9, 'chambre', 'REC-20260618-074331', 1000.00, 'wave', 'validé / encaissé', '2026-06-18 07:43:38', NULL, NULL, '2026-06-18 07:43:38', '2026-06-18 07:43:38'),
(98, 9, 'chambre', 'REC-20260618-075656', 1000.00, 'cash', 'validé / encaissé', '2026-06-18 07:57:02', NULL, NULL, '2026-06-18 07:57:02', '2026-06-18 07:57:02'),
(99, 10, 'chambre', 'REC-20260618-075907', 17000.00, 'cash', 'validé / encaissé', '2026-06-18 07:59:17', NULL, NULL, '2026-06-18 07:59:17', '2026-06-18 07:59:17'),
(100, 10, 'chambre', 'REC-20260618-080349', 8000.00, 'cash', 'validé / encaissé', '2026-06-18 08:03:59', NULL, NULL, '2026-06-18 08:03:59', '2026-06-18 08:03:59'),
(101, 10, 'chambre', 'REC-20260618-080901', 3000.00, 'wave', 'validé / encaissé', '2026-06-18 08:09:09', NULL, NULL, '2026-06-18 08:09:09', '2026-06-18 08:09:09'),
(102, 10, 'chambre', 'REC-20260618-082051', 2000.00, 'cash', 'validé / encaissé', '2026-06-18 08:20:58', NULL, NULL, '2026-06-18 08:20:58', '2026-06-18 08:20:58'),
(103, 11, 'chambre', 'REC-20260618-082340', 40000.00, 'wave', 'validé / encaissé', '2026-06-18 08:23:48', NULL, NULL, '2026-06-18 08:23:48', '2026-06-18 08:23:48'),
(104, 11, 'chambre', 'REC-20260618-082734', 15000.00, 'cash', 'validé / encaissé', '2026-06-18 08:27:45', NULL, NULL, '2026-06-18 08:27:45', '2026-06-18 08:27:45'),
(105, 11, 'chambre', 'REC-20260618-082913', 2000.00, 'cash', 'validé / encaissé', '2026-06-18 08:29:20', NULL, NULL, '2026-06-18 08:29:20', '2026-06-18 08:29:20'),
(106, 11, 'chambre', 'REC-20260618-090534', 3000.00, 'cash', 'validé / encaissé', '2026-06-18 09:05:39', NULL, NULL, '2026-06-18 09:05:39', '2026-06-18 09:05:39'),
(107, 12, 'chambre', 'REC-20260618-100544', 40000.00, 'cash', 'validé / encaissé', '2026-06-18 10:05:54', NULL, NULL, '2026-06-18 10:05:54', '2026-06-18 10:05:54'),
(108, 13, 'chambre', 'REC-20260618-100824', 10000.00, 'wave', 'validé / encaissé', '2026-06-18 10:08:31', NULL, NULL, '2026-06-18 10:08:31', '2026-06-18 10:08:31'),
(109, 12, 'chambre', 'REC-20260618-111319', 20000.00, 'mtn_momo', 'validé / encaissé', '2026-06-18 11:13:27', NULL, NULL, '2026-06-18 11:13:27', '2026-06-18 11:13:27'),
(110, 13, 'chambre', 'REC-20260618-111358', 5000.00, 'bank_transfer', 'validé / encaissé', '2026-06-18 11:14:03', NULL, NULL, '2026-06-18 11:14:03', '2026-06-18 11:14:03'),
(111, 14, 'chambre', 'REC-20260618-125217', 50000.00, 'cash', 'validé / encaissé', '2026-06-18 12:52:28', NULL, NULL, '2026-06-18 12:52:28', '2026-06-18 12:52:28'),
(112, NULL, 'chambre', 'REC-RESTO-20260618-142201', 12500.00, 'wave', 'validé / encaissé', '2026-06-18 14:22:01', NULL, NULL, '2026-06-18 14:22:01', '2026-06-18 14:22:01'),
(113, 15, 'chambre', 'REC-20260618-151111', 20000.00, 'cash', 'validé / encaissé', '2026-06-18 15:11:16', NULL, NULL, '2026-06-18 15:11:16', '2026-06-18 15:11:16'),
(114, 14, 'chambre', 'REC-20260618-151130', 25000.00, 'cash', 'validé / encaissé', '2026-06-18 15:11:36', NULL, NULL, '2026-06-18 15:11:36', '2026-06-18 15:11:36'),
(115, NULL, 'chambre', 'REC-RESTO-20260618-151228', 20000.00, 'mtn_momo', 'validé / encaissé', '2026-06-18 15:12:28', NULL, NULL, '2026-06-18 15:12:28', '2026-06-18 15:12:28'),
(116, 16, 'chambre', 'REC-20260618-153127', 90000.00, 'orange_money', 'validé / encaissé', '2026-06-18 15:31:43', NULL, NULL, '2026-06-18 15:31:43', '2026-06-18 15:31:43'),
(117, NULL, 'chambre', 'REC-RESTO-20260619-102632', 15500.00, 'wave', 'validé / encaissé', '2026-06-19 10:26:32', NULL, NULL, '2026-06-19 10:26:32', '2026-06-19 10:26:32'),
(118, 16, 'chambre', 'REC-20260619-103114', 15000.00, 'cash', 'validé / encaissé', '2026-06-19 10:31:22', NULL, NULL, '2026-06-19 10:31:22', '2026-06-19 10:31:22'),
(119, NULL, 'chambre', 'REC-RESTO-20260619-131440', 25000.00, 'cash', 'validé / encaissé', '2026-06-19 13:14:40', NULL, NULL, '2026-06-19 13:14:40', '2026-06-19 13:14:40'),
(120, NULL, 'chambre', 'REC-RESTO-20260619-150916', 17500.00, 'mtn_momo', 'validé / encaissé', '2026-06-19 15:09:16', NULL, NULL, '2026-06-19 15:09:16', '2026-06-19 15:09:16'),
(121, NULL, 'chambre', 'REC-RESTO-20260619-160420', 2500.00, 'cash', 'validé / encaissé', '2026-06-19 16:04:23', NULL, NULL, '2026-06-19 16:04:23', '2026-06-19 16:04:23'),
(122, NULL, 'chambre', 'REC-RESTO-20260619-160435', 3500.00, 'cash', 'validé / encaissé', '2026-06-19 16:04:37', NULL, NULL, '2026-06-19 16:04:37', '2026-06-19 16:04:37'),
(123, NULL, 'chambre', 'REC-RESTO-20260619-160442', 7500.00, 'cash', 'validé / encaissé', '2026-06-19 16:04:43', NULL, NULL, '2026-06-19 16:04:43', '2026-06-19 16:04:43'),
(124, NULL, 'chambre', 'REC-RESTO-20260619-160446', 3500.00, 'cash', 'validé / encaissé', '2026-06-19 16:04:47', NULL, NULL, '2026-06-19 16:04:47', '2026-06-19 16:04:47'),
(125, NULL, 'chambre', 'REC-RESTO-20260619-160449', 2500.00, 'cash', 'validé / encaissé', '2026-06-19 16:04:51', NULL, NULL, '2026-06-19 16:04:51', '2026-06-19 16:04:51'),
(126, NULL, 'chambre', 'REC-RESTO-20260619-162121', 7500.00, 'orange_money', 'validé / encaissé', '2026-06-19 16:21:21', NULL, NULL, '2026-06-19 16:21:21', '2026-06-19 16:21:21'),
(127, 16, 'chambre', 'REC-20260622-101134', 7000.00, 'wave', 'validé / encaissé', '2026-06-22 10:11:54', NULL, NULL, '2026-06-22 10:11:54', '2026-06-22 10:11:54'),
(128, 18, 'chambre', 'REC-20260622-101456', 100000.00, 'cash', 'validé / encaissé', '2026-06-22 10:15:10', NULL, NULL, '2026-06-22 10:15:10', '2026-06-22 10:15:10'),
(129, 20, 'chambre', 'REC-20260622-101633', 15000.00, 'card', 'validé / encaissé', '2026-06-22 10:16:45', NULL, NULL, '2026-06-22 10:16:45', '2026-06-22 10:16:45'),
(130, NULL, 'chambre', 'REC-RESTO-20260625-134751', 490000.00, 'cash', 'validé / encaissé', '2026-06-25 13:47:51', NULL, NULL, '2026-06-25 13:47:51', '2026-06-25 13:47:51'),
(131, NULL, 'chambre', 'REC-RESTO-20260625-140412', 350000.00, 'cash', 'validé / encaissé', '2026-06-25 14:04:12', NULL, NULL, '2026-06-25 14:04:12', '2026-06-25 14:04:12'),
(132, 37, 'chambre', 'REC-20260626-102736', 110000.00, 'cash', 'validé / encaissé', '2026-06-26 10:27:48', NULL, NULL, '2026-06-26 10:27:48', '2026-06-26 10:27:48'),
(133, 38, 'chambre', 'REC-20260626-112524', 40000.00, 'wave', 'validé / encaissé', '2026-06-26 11:25:31', NULL, NULL, '2026-06-26 11:25:31', '2026-06-26 11:25:31'),
(134, 41, 'chambre', 'REC-20260626-115825', 20000.00, 'orange_money', 'validé / encaissé', '2026-06-26 11:58:32', NULL, NULL, '2026-06-26 11:58:32', '2026-06-26 11:58:32'),
(135, 42, 'chambre', 'REC-20260626-120633', 15000.00, 'mtn_momo', 'validé / encaissé', '2026-06-26 12:06:39', NULL, NULL, '2026-06-26 12:06:39', '2026-06-26 12:06:39'),
(136, 43, 'chambre', 'REC-20260626-120708', 60000.00, 'moov_money', 'validé / encaissé', '2026-06-26 12:07:15', NULL, NULL, '2026-06-26 12:07:15', '2026-06-26 12:07:15');

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'ViewAny:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(2, 'View:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(3, 'Create:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(4, 'Update:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(5, 'Delete:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(6, 'DeleteAny:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(7, 'Restore:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(8, 'ForceDelete:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(9, 'ForceDeleteAny:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(10, 'RestoreAny:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(11, 'Replicate:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(12, 'Reorder:Role', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(13, 'ViewAny:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(14, 'View:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(15, 'Create:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(16, 'Update:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(17, 'Delete:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(18, 'DeleteAny:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(19, 'Restore:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(20, 'ForceDelete:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(21, 'ForceDeleteAny:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(22, 'RestoreAny:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(23, 'Replicate:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(24, 'Reorder:Booking', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(25, 'ViewAny:CateringItem', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(26, 'View:CateringItem', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(27, 'Create:CateringItem', 'web', '2026-06-18 11:53:06', '2026-06-18 11:53:06'),
(28, 'Update:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(29, 'Delete:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(30, 'DeleteAny:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(31, 'Restore:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(32, 'ForceDelete:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(33, 'ForceDeleteAny:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(34, 'RestoreAny:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(35, 'Replicate:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(36, 'Reorder:CateringItem', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(37, 'ViewAny:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(38, 'View:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(39, 'Create:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(40, 'Update:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(41, 'Delete:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(42, 'DeleteAny:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(43, 'Restore:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(44, 'ForceDelete:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(45, 'ForceDeleteAny:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(46, 'RestoreAny:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(47, 'Replicate:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(48, 'Reorder:CateringOrder', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(49, 'ViewAny:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(50, 'View:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(51, 'Create:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(52, 'Update:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(53, 'Delete:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(54, 'DeleteAny:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(55, 'Restore:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(56, 'ForceDelete:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(57, 'ForceDeleteAny:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(58, 'RestoreAny:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(59, 'Replicate:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(60, 'Reorder:EventBooking', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(61, 'ViewAny:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(62, 'View:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(63, 'Create:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(64, 'Update:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(65, 'Delete:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(66, 'DeleteAny:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(67, 'Restore:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(68, 'ForceDelete:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(69, 'ForceDeleteAny:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(70, 'RestoreAny:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(71, 'Replicate:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(72, 'Reorder:EventSpace', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(73, 'ViewAny:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(74, 'View:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(75, 'Create:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(76, 'Update:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(77, 'Delete:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(78, 'DeleteAny:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(79, 'Restore:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(80, 'ForceDelete:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(81, 'ForceDeleteAny:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(82, 'RestoreAny:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(83, 'Replicate:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(84, 'Reorder:KeyCard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(85, 'ViewAny:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(86, 'View:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(87, 'Create:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(88, 'Update:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(89, 'Delete:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(90, 'DeleteAny:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(91, 'Restore:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(92, 'ForceDelete:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(93, 'ForceDeleteAny:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(94, 'RestoreAny:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(95, 'Replicate:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(96, 'Reorder:Payment', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(97, 'ViewAny:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(98, 'View:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(99, 'Create:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(100, 'Update:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(101, 'Delete:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(102, 'DeleteAny:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(103, 'Restore:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(104, 'ForceDelete:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(105, 'ForceDeleteAny:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(106, 'RestoreAny:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(107, 'Replicate:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(108, 'Reorder:Room', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(109, 'ViewAny:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(110, 'View:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(111, 'Create:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(112, 'Update:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(113, 'Delete:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(114, 'DeleteAny:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(115, 'Restore:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(116, 'ForceDelete:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(117, 'ForceDeleteAny:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(118, 'RestoreAny:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(119, 'Replicate:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(120, 'Reorder:RoomType', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(121, 'View:Dashboard', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(122, 'View:Planning', 'web', '2026-06-18 11:53:07', '2026-06-18 11:53:07'),
(123, 'View:VueChambres', 'web', '2026-06-18 11:53:08', '2026-06-18 11:53:08'),
(124, 'View:PaymentMethodsChart', 'web', '2026-06-18 11:53:08', '2026-06-18 11:53:08'),
(125, 'View:RevenueChart', 'web', '2026-06-18 11:53:08', '2026-06-18 11:53:08'),
(126, 'View:StatsOverview', 'web', '2026-06-18 11:53:08', '2026-06-18 11:53:08'),
(127, 'ViewAny:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(128, 'View:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(129, 'Create:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(130, 'Update:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(131, 'Delete:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(132, 'DeleteAny:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(133, 'Restore:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(134, 'ForceDelete:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(135, 'ForceDeleteAny:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(136, 'RestoreAny:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(137, 'Replicate:User', 'web', '2026-06-18 12:28:16', '2026-06-18 12:28:16'),
(138, 'Reorder:User', 'web', '2026-06-18 12:28:17', '2026-06-18 12:28:17');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', '2026-06-18 11:47:35', '2026-06-18 11:47:35'),
(2, 'user1', 'web', '2026-06-18 12:31:42', '2026-06-18 12:31:42');

-- --------------------------------------------------------

--
-- Structure de la table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(41, 1),
(41, 2),
(42, 1),
(42, 2),
(43, 1),
(43, 2),
(44, 1),
(45, 1),
(45, 2),
(46, 1),
(46, 2),
(47, 1),
(47, 2),
(48, 1),
(48, 2),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(51, 1),
(51, 2),
(52, 1),
(52, 2),
(53, 1),
(53, 2),
(54, 1),
(54, 2),
(55, 1),
(55, 2),
(56, 1),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(59, 1),
(59, 2),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(65, 2),
(66, 1),
(66, 2),
(67, 1),
(67, 2),
(68, 1),
(69, 1),
(69, 2),
(70, 1),
(70, 2),
(71, 1),
(71, 2),
(72, 1),
(72, 2),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(87, 1),
(87, 2),
(88, 1),
(88, 2),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(91, 1),
(91, 2),
(92, 1),
(92, 2),
(93, 1),
(93, 2),
(94, 1),
(94, 2),
(95, 1),
(95, 2),
(96, 1),
(96, 2),
(97, 1),
(97, 2),
(98, 1),
(98, 2),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(101, 1),
(101, 2),
(102, 1),
(102, 2),
(103, 1),
(103, 2),
(104, 1),
(105, 1),
(105, 2),
(106, 1),
(106, 2),
(107, 1),
(107, 2),
(108, 1),
(108, 2),
(109, 1),
(109, 2),
(110, 1),
(110, 2),
(111, 1),
(111, 2),
(112, 1),
(112, 2),
(113, 1),
(113, 2),
(114, 1),
(114, 2),
(115, 1),
(115, 2),
(116, 1),
(117, 1),
(117, 2),
(118, 1),
(118, 2),
(119, 1),
(119, 2),
(120, 1),
(120, 2),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1);

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
(1, 4, '21', 'disponible', '2026-06-08 13:21:15', '2026-06-26 09:35:45', 'propre'),
(2, 4, '22', 'occupee', '2026-06-08 13:21:29', '2026-06-09 17:06:38', 'propre'),
(3, 4, '23', 'occupee', '2026-06-08 13:21:41', '2026-06-18 12:52:08', 'propre'),
(4, 4, '24', 'occupee', '2026-06-08 13:21:51', '2026-06-22 14:28:46', 'propre'),
(5, 1, '25', 'occupee', '2026-06-08 13:21:59', '2026-06-18 07:24:49', 'propre'),
(6, 3, '26', 'occupee', '2026-06-08 13:22:24', '2026-06-18 07:59:03', 'propre'),
(7, 2, '27', 'occupee', '2026-06-08 13:22:32', '2026-06-19 10:30:42', 'propre'),
(8, 2, '28', 'occupee', '2026-06-08 13:22:40', '2026-06-22 14:39:20', 'propre'),
(9, 2, '29', 'occupee', '2026-06-08 13:22:48', '2026-06-18 08:23:32', 'propre'),
(10, 2, '30', 'disponible', '2026-06-08 13:23:00', '2026-06-08 13:23:00', 'propre'),
(11, 1, '31', 'occupee', '2026-06-08 13:23:09', '2026-06-22 08:42:36', 'propre'),
(12, 5, '32', 'occupee', '2026-06-09 16:38:59', '2026-06-26 11:51:35', 'propre'),
(13, 5, '33', 'occupee', '2026-06-09 16:39:07', '2026-06-26 11:44:46', 'propre'),
(14, 5, '34', 'occupee', '2026-06-09 16:39:17', '2026-06-18 10:08:13', 'propre'),
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'coulsie@gmail.com', NULL, '$2y$12$yeTcnk5PNKO.tqWPd4Vikeer/Dxcf3EAQO5ceVVszUB4CXnWqvZCe', 'Zb80AHGFKDivmEQt1kIcRRtbs8cTgzXvpux2lhZmWdEuct8dGDc2T3Vg8aiC', '2026-06-08 11:39:21', '2026-06-08 11:45:00'),
(2, 'Koné Nafata', 'nafie410@gmail.com', NULL, '$2y$12$mmlRf58/BS8NH5iWok0uSeRt2ZtgrhZX7v6ljuNJCSqD8shwXGSBe', NULL, '2026-06-18 12:31:57', '2026-06-18 12:31:57');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
