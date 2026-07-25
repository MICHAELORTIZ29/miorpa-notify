/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.18-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: miorpa_notify
-- ------------------------------------------------------
-- Server version	10.11.18-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `businesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `name` varchar(150) NOT NULL,
  `legal_name` varchar(200) DEFAULT NULL,
  `tax_id` varchar(30) DEFAULT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'America/Lima',
  `status` varchar(20) NOT NULL DEFAULT 'trial',
  `contact_name` varchar(150) DEFAULT NULL,
  `contact_email` varchar(254) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `suspended_at` datetime(6) DEFAULT NULL,
  `suspension_reason` varchar(30) DEFAULT NULL,
  `closed_at` datetime(6) DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `businesses_public_id_unique` (`public_id`),
  KEY `businesses_status_index` (`status`),
  KEY `businesses_created_at_index` (`created_at`),
  KEY `businesses_tax_id_index` (`tax_id`),
  KEY `businesses_suspension_reason_index` (`suspension_reason`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES
(1,'01KXSM99A4XYCJAV93T8TNZM1M','dayron sac','10713473508','dayron@gmail.com','America/Lima','active',NULL,NULL,'917374145',NULL,NULL,NULL,'2026-07-18 08:28:26.000000','2026-07-18 08:28:26.000000'),
(2,'01KXSMWY2B0VCG5NM00N4KG45S','MIORPASOFT','MCIHAEL OIRTUZ','10726315208','America/Lima','active',NULL,NULL,'917374145',NULL,NULL,NULL,'2026-07-18 08:39:10.000000','2026-07-18 09:02:16.000000'),
(3,'01KY0M11517FKEW6CHP649V23N','COMERCIAL MANA','EBER PORTOCARRERO','10777777777','America/Lima','active',NULL,NULL,'999999999',NULL,NULL,NULL,'2026-07-21 01:38:36.000000','2026-07-21 01:38:36.000000'),
(4,'01KY0MD28QJFTV1FBNC48HTVRG','POLLERIA MAYCOL','MAYCOL TUTCTO','20222222222','America/Lima','active','MAYCOL TUCTO TUCTO','maycol@gmail.com','888888888',NULL,NULL,NULL,'2026-07-21 01:45:11.000000','2026-07-21 01:45:11.000000'),
(5,'01KY5RCEQFQP3HRJAZ27BM7P31','LAS CHURRAS','LAS CHURRAS','20333333333','America/Lima','active','BELDAD GRATELLY','churras@gmail.com','666666666',NULL,NULL,NULL,'2026-07-23 01:30:57.000000','2026-07-23 01:30:57.000000');
/*!40000 ALTER TABLE `businesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `authorized_by` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `type` varchar(30) NOT NULL,
  `platform` varchar(30) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `token_hash` char(64) DEFAULT NULL,
  `installation_hash` char(64) DEFAULT NULL,
  `app_version` varchar(40) DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `capabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`capabilities`)),
  `authorized_at` timestamp NULL DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `disabled_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `devices_public_id_unique` (`public_id`),
  UNIQUE KEY `devices_business_installation_unique` (`business_id`,`installation_hash`),
  UNIQUE KEY `devices_token_hash_unique` (`token_hash`),
  KEY `devices_authorized_by_foreign` (`authorized_by`),
  KEY `devices_business_status_index` (`business_id`,`status`),
  KEY `devices_business_type_index` (`business_id`,`type`),
  KEY `devices_last_seen_at_index` (`last_seen_at`),
  CONSTRAINT `devices_authorized_by_foreign` FOREIGN KEY (`authorized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `devices_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` VALUES
(1,'01KXT1TG4RJM96K7KCFKB06QZ9',1,2,'Emisor local de pruebas','emitter','android','active',NULL,'9e9287e898cbfd8c5e34d2774f7c43da4432d1d437bb984c4095ff5b1590a590','simulator-1.0',NULL,NULL,NULL,'2026-07-18 12:25:01','2026-07-18 12:25:01',NULL,NULL,'2026-07-18 12:25:01','2026-07-18 12:25:01'),
(2,'01KXT8N4HJCMWQVMQ80DVC1AEC',1,2,'Xiaomi M2004J19C','emitter','android','revoked',NULL,'8d43a6768c4bf90c905830094ec07f0dd6c85322da38c1fa43353e03ac1fc783','1.0','127.0.0.1','okhttp/4.12.0','{\"notification_listener\":true,\"payment_emitter\":true,\"offline_queue\":true}','2026-07-18 14:24:26','2026-07-21 03:24:45','2026-07-21 03:24:45','2026-07-21 03:24:45','2026-07-18 14:24:26','2026-07-21 03:24:45'),
(3,'01KY0PTY5A982BMT4J7VDHK6F9',4,6,'cajero1','receiver','web','revoked',NULL,'2c57af939a455e4de83276707c0ecd4107edfbfd8d524ce9ff7a4e906428d7b9',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"browser_receiver\":true,\"web_notifications\":true}','2026-07-21 02:27:43','2026-07-21 02:45:59','2026-07-21 03:12:27','2026-07-21 03:12:27','2026-07-21 02:27:43','2026-07-21 03:12:27'),
(4,'01KY0R2MFD3F6RJNQW7AW97F2T',4,6,'Navegador de cajero2','receiver','web','revoked',NULL,'6d1d4dd54a09509e441510394597af662159b1c4c054c3811be4506df3c36f74',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"browser_receiver\":true,\"web_notifications\":true}','2026-07-21 02:49:23','2026-07-21 03:11:57','2026-07-21 03:12:22','2026-07-21 03:12:22','2026-07-21 02:49:23','2026-07-21 03:12:22'),
(5,'01KY0SF80DM77Q7906HZZ7G826',4,6,'Navegador de cajero1','receiver','web','active','62cfe90e9e7c1732caeb33f0b16c53bbf73568cab31b715b41994df7489f2761','9a699b5e71dfc13b116ceedf1695b1393fe7ebd6341bcafb38ba9c2ca5357d97',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"browser_receiver\":true,\"web_notifications\":true}','2026-07-21 03:13:45','2026-07-23 01:14:28',NULL,NULL,'2026-07-21 03:13:45','2026-07-23 01:14:28'),
(6,'01KY0TG0G1E3HXSA3901YAF0MR',4,6,'Xiaomi M2004J19C','emitter','android','active','f8e3784065fe83145af42feaa7f8263a4691c829eb12f4dcaaeeb809365d9a72','8d43a6768c4bf90c905830094ec07f0dd6c85322da38c1fa43353e03ac1fc783','1.0','127.0.0.1','okhttp/4.12.0','{\"notification_listener\":true,\"payment_emitter\":true,\"offline_queue\":true}','2026-07-21 03:31:39','2026-07-23 01:06:53',NULL,NULL,'2026-07-21 03:31:39','2026-07-23 01:06:53'),
(7,'01KY17N8FE7TQ6XJ3AT2Q3CZXV',4,6,'Navegador de MAYCOL TUCTO TUCTO','receiver','web','active','a5cc3ca0af606050b4279a7ae7e631cd2e88503b60ef4a529f5834c196f4744e','4f28206448cdf619f62c43f08da94ac3036d0de11ab27a2616ff414cdd04fad7',NULL,'172.20.10.1','Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/142.0.7444.148 Mobile/15E148 Safari/604.1','{\"browser_receiver\":true,\"web_notifications\":true}','2026-07-21 07:21:42','2026-07-21 07:33:07',NULL,NULL,'2026-07-21 07:21:42','2026-07-21 07:33:07'),
(8,'01KY36Q9Q633QJCG83XFPH9GSJ',4,6,'Tabelt','receiver','web','active','9c2ab8e0165ea1a9f95ed1ab70d620dede6d72ed37edb301b22a5f411260457d','3719cfa77adb9136f0e69f70ca8188b2de042358cd592d75164634c2796b2044',NULL,'172.20.10.2','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36','{\"browser_receiver\":true,\"web_notifications\":true}','2026-07-22 01:43:49','2026-07-22 02:06:18',NULL,NULL,'2026-07-22 01:43:49','2026-07-22 02:06:18'),
(9,'01KY5RJ98MC2DY9DEV3RZ69A18',5,9,'Xiaomi 25028RN03L','emitter','android','active','e196c0b05d0a7569c1a10a05891740e91054910d877e3756a82b4f53711e26d5','c8867bc2e09bb2b8b80d0fe86e3cac45d7757d1f1d23e5dba1c5a475ea1aa3cd','1.0','127.0.0.1','okhttp/4.12.0','{\"notification_listener\":true,\"payment_emitter\":true,\"offline_queue\":true}','2026-07-23 01:34:08','2026-07-23 02:01:01',NULL,NULL,'2026-07-23 01:34:08','2026-07-23 02:01:01'),
(10,'01KY5RN2WWC3Z0X0JW7CN0PXTB',5,9,'Navegador de BELDAD GRATELLY','receiver','web','active','997fee7d9ffed54888f394a0fc346d9fbbb9fc9b5f1afaa823e9b0f1e0645d61','9a699b5e71dfc13b116ceedf1695b1393fe7ebd6341bcafb38ba9c2ca5357d97',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"browser_receiver\":true,\"web_notifications\":true}','2026-07-23 01:35:40','2026-07-23 05:36:03',NULL,NULL,'2026-07-23 01:35:40','2026-07-23 05:36:03');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_07_17_085922_create_businesses_table',2),
(5,'2026_07_17_085930_add_miorpa_fields_to_users_table',2),
(6,'2026_07_18_053127_create_devices_table',3),
(7,'2026_07_18_053128_create_pairing_codes_table',3),
(8,'2026_07_18_062918_create_payment_providers_table',4),
(9,'2026_07_18_062919_create_payments_table',4),
(10,'2026_07_18_062920_create_payment_acknowledgements_table',4),
(11,'2026_07_20_000001_create_saas_subscriptions_tables',5),
(12,'2026_07_20_000002_add_suspension_reason_to_businesses_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pairing_codes`
--

DROP TABLE IF EXISTS `pairing_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pairing_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `used_by_device_id` bigint(20) unsigned DEFAULT NULL,
  `code_hash` char(64) NOT NULL,
  `code_suffix` varchar(4) NOT NULL,
  `device_type` varchar(30) NOT NULL,
  `max_uses` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `uses_count` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pairing_codes_public_id_unique` (`public_id`),
  UNIQUE KEY `pairing_codes_code_hash_unique` (`code_hash`),
  KEY `pairing_codes_created_by_foreign` (`created_by`),
  KEY `pairing_codes_used_by_device_id_foreign` (`used_by_device_id`),
  KEY `pairing_codes_business_expiration_index` (`business_id`,`expires_at`),
  KEY `pairing_codes_business_type_index` (`business_id`,`device_type`),
  CONSTRAINT `pairing_codes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `pairing_codes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `pairing_codes_used_by_device_id_foreign` FOREIGN KEY (`used_by_device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pairing_codes`
--

LOCK TABLES `pairing_codes` WRITE;
/*!40000 ALTER TABLE `pairing_codes` DISABLE KEYS */;
INSERT INTO `pairing_codes` VALUES
(1,'01KXSXDDTVN7MVRJ5RBMAFC0WQ',1,2,NULL,'dccb486fdf562fc69be6b45a857aedc141786ace3a5d321a9707b77eb7ba864d','MR3D','receiver',1,0,'2026-07-18 11:12:59',NULL,NULL,'2026-07-18 11:07:59','2026-07-18 11:07:59'),
(2,'01KXT8KTKS9CDT30CWWSDE5ZGR',1,2,2,'8bfa035cb2d70feb4d8c9d60cbbfc3ce5b0fd28207ddabdf2362c456e7e4366c','CVVX','emitter',1,1,'2026-07-18 14:33:43','2026-07-18 14:24:26',NULL,'2026-07-18 14:23:43','2026-07-18 14:24:26'),
(3,'01KY0NNNFS3E8WF7Y7C03HFHMN',4,6,NULL,'1a9487c30fc03919d08f7aabb56504bc3d2af3b3101cde0ee918f639e1794d9d','ZUGL','receiver',1,0,'2026-07-21 02:17:21',NULL,NULL,'2026-07-21 02:07:21','2026-07-21 02:07:21'),
(4,'01KY0NPN9VM58ZY7BYAJQB10H2',4,6,NULL,'ada712ea70269d6bfa7bea33d047585085fd60eea06064c8c134fa88cae520fe','KLWF','receiver',1,0,'2026-07-21 02:17:54',NULL,NULL,'2026-07-21 02:07:54','2026-07-21 02:07:54'),
(5,'01KY0NPVVT00RFDG85RNH2SR30',4,6,NULL,'1a6adf8773cdca9ab26587dc46ba50ba3bcce1082f9b2ec5e9a073e2380bfff9','LTZX','receiver',1,0,'2026-07-21 02:18:01',NULL,NULL,'2026-07-21 02:08:01','2026-07-21 02:08:01'),
(6,'01KY0PT9CQCE4JDJ192R37GA38',4,6,3,'c91781393fab2f99dd3182ffb0fd915ab483ff5ca957c5e5c0591ba530b96f43','9ANL','receiver',1,1,'2026-07-21 02:37:21','2026-07-21 02:27:43',NULL,'2026-07-21 02:27:21','2026-07-21 02:27:43'),
(7,'01KY0Q469J0KSW5FA0B6956QJ9',4,6,NULL,'1df12fd10f63df05bdf339a52abc7484364cd385b334d42bd11faa0abff0670a','P29J','receiver',1,0,'2026-07-21 02:42:46',NULL,NULL,'2026-07-21 02:32:46','2026-07-21 02:32:46'),
(8,'01KY0QX810MQ7BFM9J0MJQ32K0',4,6,4,'f5844552b72cb25cf9b38f77d553b463915d9670fed0b08742f38649baf89fb2','GEW2','receiver',1,1,'2026-07-21 02:56:27','2026-07-21 02:49:23',NULL,'2026-07-21 02:46:27','2026-07-21 02:49:23'),
(9,'01KY0SE0G9939NNHT238P37568',4,6,5,'675e3901eb5a6edb35142a01d12d9ce2a98d03c23abbc3da3f2facf987dfe64f','6VYA','receiver',1,1,'2026-07-21 03:28:05','2026-07-21 03:13:45',NULL,'2026-07-21 03:13:05','2026-07-21 03:13:45'),
(10,'01KY0TF1NR6H9VNRBR8A28NK86',4,6,6,'3307fee2a3ef5feb5d1df4e6cb194dde555595a3905ee508259450355a644162','4KLS','emitter',1,1,'2026-07-21 03:41:07','2026-07-21 03:31:39',NULL,'2026-07-21 03:31:07','2026-07-21 03:31:39'),
(11,'01KY17KH6CN0ZGEJFEZHB42526',4,6,7,'160c7ba4104b73e368d3fc6ad439a0512b9cff6d2c1adc808094a56713878392','AMX9','receiver',1,1,'2026-07-21 07:30:46','2026-07-21 07:21:42',NULL,'2026-07-21 07:20:46','2026-07-21 07:21:42'),
(12,'01KY36NSP7R7AJ3EV92EA18XN2',4,6,8,'d4d68c49e7dea6ba2bc36f12dea3bd467f6d2dfa0f148ea4f09154ece1ff3dc7','FH4J','receiver',1,1,'2026-07-22 01:53:00','2026-07-22 01:43:49',NULL,'2026-07-22 01:43:00','2026-07-22 01:43:49'),
(13,'01KY5QA77CCWD0C6NKS04XWFX5',4,6,NULL,'7332deb1f0a150e54d43f43b8822d285ca0371800fa67a2a30b5416f50a7c6ed','TJLX','emitter',1,0,'2026-07-23 01:22:15',NULL,NULL,'2026-07-23 01:12:15','2026-07-23 01:12:15'),
(14,'01KY5QFNGFNPVJZYXKW7VNM4HG',4,6,NULL,'15cf0c30155a28ab88fd22f2082c8113155b8672ad66983f07c478cee3478a20','PFJR','emitter',1,0,'2026-07-23 01:25:14',NULL,NULL,'2026-07-23 01:15:14','2026-07-23 01:15:14'),
(15,'01KY5R7ZQJRWNF3BM5T5ZVYZ6R',4,6,NULL,'a4798dbf4f0f8674786320f3ad55c7270dae6dfbdca4ab05a9349f7c729be1ca','QY7A','emitter',1,0,'2026-07-23 01:38:31',NULL,NULL,'2026-07-23 01:28:31','2026-07-23 01:28:31'),
(16,'01KY5RECV77SXXR2TVQ03VWH6W',5,9,9,'9d27757153649a39d7453b3b26bffbc1a5e914fcdc2471c375a05116b8e1154f','A869','emitter',1,1,'2026-07-23 01:42:01','2026-07-23 01:34:08',NULL,'2026-07-23 01:32:01','2026-07-23 01:34:08'),
(17,'01KY5RMMXJMSCSFDQ17QRSR4EE',5,9,10,'ae92e2bb976e54973bf6c8de8bac10107ddc0d3f77fad88cbb19e22579838e60','XRBC','receiver',1,1,'2026-07-23 01:45:26','2026-07-23 01:35:40',NULL,'2026-07-23 01:35:26','2026-07-23 01:35:40');
/*!40000 ALTER TABLE `pairing_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_acknowledgements`
--

DROP TABLE IF EXISTS `payment_acknowledgements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_acknowledgements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `payment_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `receiver_device_id` bigint(20) unsigned DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_acknowledgements_public_id_unique` (`public_id`),
  UNIQUE KEY `payment_acknowledgements_payment_user_unique` (`payment_id`,`user_id`),
  KEY `payment_acknowledgements_user_id_foreign` (`user_id`),
  KEY `payment_acknowledgements_receiver_device_id_foreign` (`receiver_device_id`),
  KEY `payment_acknowledgements_viewed_at_index` (`viewed_at`),
  KEY `payment_acknowledgements_confirmed_at_index` (`confirmed_at`),
  CONSTRAINT `payment_acknowledgements_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_acknowledgements_receiver_device_id_foreign` FOREIGN KEY (`receiver_device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_acknowledgements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_acknowledgements`
--

LOCK TABLES `payment_acknowledgements` WRITE;
/*!40000 ALTER TABLE `payment_acknowledgements` DISABLE KEYS */;
INSERT INTO `payment_acknowledgements` VALUES
(1,'01KXT1VFGMJ3WH39SPQDKQ3XKC',2,2,NULL,'2026-07-18 12:26:27','2026-07-18 12:26:27','2026-07-18 12:25:34','2026-07-18 12:26:27'),
(2,'01KY0TJG10HHMZSGKJQJ045KB7',12,7,5,'2026-07-21 03:33:00','2026-07-21 03:33:06','2026-07-21 03:33:00','2026-07-21 03:33:06'),
(3,'01KY0TKKCT7H6F3BT9HXRQ40FZ',12,6,5,'2026-07-21 03:33:36',NULL,'2026-07-21 03:33:36','2026-07-21 03:33:36'),
(4,'01KY0TN50HRE45HNZ4GECKRNFC',13,6,5,'2026-07-21 03:34:27','2026-07-21 03:34:39','2026-07-21 03:34:27','2026-07-21 03:34:39'),
(5,'01KY16Q9E8EVEYZWH0BHXNKVQ6',14,6,5,'2026-07-21 07:05:16','2026-07-21 07:05:23','2026-07-21 07:05:20','2026-07-21 07:05:23'),
(6,'01KY16T52KM73MXXW5ACRXDX4N',15,7,5,'2026-07-21 07:06:54','2026-07-21 07:06:56','2026-07-21 07:06:54','2026-07-21 07:06:56'),
(7,'01KY16TS170VEKGH3SACX6AKHY',16,7,5,'2026-07-21 07:07:14','2026-07-21 07:07:17','2026-07-21 07:07:14','2026-07-21 07:07:17'),
(8,'01KY16V1RKDV30QBM7QEP5VZ61',14,7,5,'2026-07-21 07:07:23',NULL,'2026-07-21 07:07:23','2026-07-21 07:07:23'),
(9,'01KY17NPZ6WYR6GT2Q3MCXAPT4',17,6,7,'2026-07-21 07:21:57','2026-07-21 07:22:02','2026-07-21 07:21:57','2026-07-21 07:22:02');
/*!40000 ALTER TABLE `payment_acknowledgements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_providers`
--

DROP TABLE IF EXISTS `payment_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_providers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `code` varchar(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  `android_packages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`android_packages`)),
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_providers_public_id_unique` (`public_id`),
  UNIQUE KEY `payment_providers_code_unique` (`code`),
  KEY `payment_providers_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_providers`
--

LOCK TABLES `payment_providers` WRITE;
/*!40000 ALTER TABLE `payment_providers` DISABLE KEYS */;
INSERT INTO `payment_providers` VALUES
(1,'01KXSZCV0XMZV5ZW38P5XSPN7E','yape','Yape','[\"com.bcp.innovacxion.yapeapp\"]','active','{\"parser_enabled\":true,\"currency\":\"PEN\",\"parser_version\":\"yape-1.0.0\"}','2026-07-18 11:42:37','2026-07-23 00:16:52'),
(2,'01KY5M4T5BVH0QDPC1YD7H9RT3','plin','Plin','[\"pe.com.interbank.mobilebanking\"]','active','{\"parser_enabled\":true,\"currency\":\"PEN\",\"supported_sources\":[\"interbank\"],\"parser_versions\":{\"interbank\":\"plin-interbank-1.0.0\"}}','2026-07-23 00:16:53','2026-07-23 00:16:53'),
(3,'01KY5M4T5GJ3EMJJSE9PXQ0E5T','bim','BIM','[]','disabled','{\"parser_enabled\":false,\"currency\":\"PEN\"}','2026-07-23 00:16:53','2026-07-23 00:16:53');
/*!40000 ALTER TABLE `payment_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `payment_provider_id` bigint(20) unsigned NOT NULL,
  `emitter_device_id` bigint(20) unsigned NOT NULL,
  `source_event_hash` char(64) NOT NULL,
  `external_reference` varchar(120) DEFAULT NULL,
  `payer_name` varchar(190) DEFAULT NULL,
  `payer_document` varchar(30) DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PEN',
  `status` varchar(30) NOT NULL DEFAULT 'received',
  `parser_version` varchar(40) DEFAULT NULL,
  `occurred_at` timestamp(3) NOT NULL,
  `received_at` timestamp(3) NOT NULL,
  `raw_payload` longtext DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_public_id_unique` (`public_id`),
  UNIQUE KEY `payments_business_source_event_unique` (`business_id`,`source_event_hash`),
  KEY `payments_business_occurred_index` (`business_id`,`occurred_at`),
  KEY `payments_business_status_index` (`business_id`,`status`),
  KEY `payments_provider_occurred_index` (`payment_provider_id`,`occurred_at`),
  KEY `payments_emitter_device_id_index` (`emitter_device_id`),
  KEY `payments_amount_index` (`amount`),
  CONSTRAINT `payments_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `payments_emitter_device_id_foreign` FOREIGN KEY (`emitter_device_id`) REFERENCES `devices` (`id`),
  CONSTRAINT `payments_payment_provider_id_foreign` FOREIGN KEY (`payment_provider_id`) REFERENCES `payment_providers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,'01KXT1TG52GMHWPAK0F8E2BW3S',1,1,1,'4fba950d068f21bded41383ab896f35ec65e451fa1b3662631b322f2398231ec',NULL,'Cliente de prueba',NULL,25.50,'PEN','received','simulator-1.0','2026-07-18 12:25:01.000','2026-07-18 12:25:01.000','eyJpdiI6InluVnRmNjh3NWdCckxONytvTUZJVFE9PSIsInZhbHVlIjoibjJDT2hSemZqdUw2eUpxY3VFYnVHQzEraC9pN21uK2oyNDdwa05PTmU2OW5nQVAxbXlmVFVPQ3pMcmhueVVVS2orSXZob1BESldVeEU5UDVLTE9vY3o1WFgwY09UU095WjJGaDcwZ0U5bDU2MkVtVTlIbWdhN3M1dFFMMDVXNHA2YzBhMjBuYXltOGJnNlEwdm5Ic0t3PT0iLCJtYWMiOiJmZTc0MTRjYjllNzIwMTY4MzU5Mjk0YWI2MjRhOWFjMWU5ZmZhYmU3OWU4MzM5NGI3NTA0YWMzNjU4YTQyN2IxIiwidGFnIjoiIn0=','{\"environment\":\"local\"}','2026-07-18 12:25:01','2026-07-18 12:25:01'),
(2,'01KXT1TY9C44YGXSHSJZFXX1GX',1,1,1,'c27844a4e22d96782514462bcf76539e10c71e9fa1dbc6bc7b4f296b619b50a0',NULL,'Carlos Mendoza',NULL,35.80,'PEN','confirmed','simulator-1.0','2026-07-18 12:25:16.000','2026-07-18 12:25:16.000','eyJpdiI6IitTZkZtSytQNEZHTUdZbllyVUpZdnc9PSIsInZhbHVlIjoicVVLR20rK1VFZ3diNXVmbExqUFd2Y0NvS052V1NtbHZ1T0JJS1RiZG9EUWNWdHI2UUVCVldmSmhjRkdZdklTWVI4YjM0bFlQU2xNSWRKVGIrdTdrY2tDQUNncWZrOGcraGpvMTlwWGlHRjF2S1FSRFdQbXcxQ0dtWVJaQmdwY1giLCJtYWMiOiJhNWNjNmQ2ZTY3ODYwZWJmOTFjYWEzYjY2NDI2ODY3ZmQzOWJjZDIwNTQ0NTg3ZDY4YWZiZTZjOGE0ZGIwNjg5IiwidGFnIjoiIn0=','{\"environment\":\"local\"}','2026-07-18 12:25:16','2026-07-18 12:26:27'),
(3,'01KXV6E69SKVY1F9ADD183V52F',1,1,2,'af3b4c51eecf5418bc8a39e45cc2054303d0b54174e3b1328dc0cf3340181ce5',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-18 21:55:38.000','2026-07-18 23:04:55.000','eyJpdiI6Ik1hTzVzUWtlUG5FMStubkQ0NUFUSEE9PSIsInZhbHVlIjoiL0p2N1dGbEo1OFU0TzVLdUpHVGlhY294L3BSM204dUtkdEpCQ25wZkgyejJXUVhNM2JtWDh0RVhXVENkeENwU0dxa0JZbWhqa1hybDhjOGEvQWRJcHhkWUZzcklxQTJuK3pPVkFsREdjUHV2Z0VZQTdVMlBLd3V3dDRsMW4vTkdObkxNQ3cxbXpWTC95WTVCR1RUbW5aQUwyTE5LeEVPQXhUaTZKSFNMNFhBNm12aHRVR0tDaWxxWU1iRVQ5QlZiMmk4MWEvNDZhV3pJV2JSZko4d0JKQT09IiwibWFjIjoiNmQ2MGEzZTUyYWIwNmI0NzA1YWNiNTY2NTJhZjM5ZmFlMjRlMDk0YjM5MDRhYzgxNDQ2YzVmOWY0ZjgzYzQyYiIsInRhZyI6IiJ9',NULL,'2026-07-18 23:04:55','2026-07-18 23:04:55'),
(4,'01KXV6E6PEY2A5Z2BNT3WFAYSB',1,1,2,'89839cb663d1e9946c2a895e512b4a22b300ab1769d0faa6cfd243cb0b33eb85',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-18 22:06:55.000','2026-07-18 23:04:56.000','eyJpdiI6InFBdWZVcnlhSzUyaDV6NCs0Vzh5Z0E9PSIsInZhbHVlIjoiaU9uNEtqMTl3ZTdLTGU0THBVeDR6VXR6em1Cek03UG12RU9xM3Bmck9UQVJqemNnQkRYMlFHRW4wMHFENUZDQ21aeStkUGhTMkdnNVBWSng4a0FaemJ4VEUvZ3BSSXR5QkVOTzN4MzFUN1k2OENUclk0azF2M01aMGVza244czAxZWM0WXQ3cFNFeVpQOGs4UDVxenhoWGxQcVladmtWVytiQ3VXOUhvS1hNdldFM3lZaEpZcU1XbXJScGl5MDlPY3RNNUFHZkJuWUFkMUtyYW1ieG1OZz09IiwibWFjIjoiZTM2MTlkMDNhMDVhMDc2ZDBlYjU0MWFmNmFjOTc4ZWRhNWM3NTBiZDczOTAzYTVkZDI1OWI4ODg4NTVjYjYwNyIsInRhZyI6IiJ9',NULL,'2026-07-18 23:04:56','2026-07-18 23:04:56'),
(5,'01KY090WC8DRM0D7YFMGFFAJK6',1,1,2,'6bdab2263bfa437bed7e5e6358ddbd353c9dff2016605a9ade3d72b8b5c4e0e6',NULL,'HADJI CHOQUE',NULL,100.00,'PEN','received','yape-1.0.0','2026-07-19 20:25:17.000','2026-07-20 22:26:17.000','eyJpdiI6InRvRnV1U3hPN3hQcTNXcFJRMzY1VEE9PSIsInZhbHVlIjoia0VON3pMMzFPazMwN25nOG8xcUhONHByQmxHRi9lNUs3ckY4cnRuWmtWNS9lVnNBVHgwcjFRU1h0TzVkcCtjcUhDbktpNlh6M3l5ZmNoQWNmamlvQzZ1LytCWEJscWpvUUtmNUFCa3J0aE81MEdqSm8ybEVKTnlLU0ZDbmUxOEdBczM4VUlkNVdhQVgrdkxnaVpZL0Z6Y1ZrS0pRWmpxelJqMVhYWnExelJlNlF6UzNLRldhMHFyRXhWaFUxc0gyR3R2U2lCT2N0TmpMMnBOQ2R1Y3k3dz09IiwibWFjIjoiZGMxNDI2YTY5YTU4Nzk3NTFhMzFlMTAwZDk0OWUwYTRmMDMzYmI1MTMwOGJmMGQwM2UxNjgzMjA0OWMwMGYyNCIsInRhZyI6IiJ9',NULL,'2026-07-20 22:26:17','2026-07-20 22:26:17'),
(6,'01KY090X97B74XN3J5PME5Y8GA',1,1,2,'e3f89518a22652978b8a2d99b5031c3d720b6ff811c51d18fd767e0a00e611bf',NULL,'DENIS LUIS OLIVERA',NULL,21.00,'PEN','received','yape-1.0.0','2026-07-19 22:03:59.000','2026-07-20 22:26:18.000','eyJpdiI6IkN0VFh6SUovdlRLOExabHA4Mm8yN1E9PSIsInZhbHVlIjoibSsrK01ubzlMRDZNV09MTHRVRXdWS0dRZ1A0SXhCK01GMEVjV21jUk15dGJScXdZb3dMVVlXSmNCRzI3ZzEzY3VzNk4zcDdFQy8wS2VKaERZMW5INkk2bjdpT3ozRCtwdURPRGw4cHpPRnNWRW10YURldmhTQ3Q5L1BST0paZmJjUFNCM1ZTRTBYVzgzTEtsVGdySm03eEs2QkUzVEliV0FUYWVaaWRPYWdYeU5BeHlrZnkwTWpGR1pHZS8vNzNONmsxQlNUSi93dkc2dTZjNnowRER4dz09IiwibWFjIjoiMjdhZTg2ZWY3YTFmMzcwZGFkOTVjZWZjZDRjYzA4NGY3ODg1MGJhNWIzOTVlODlmMjhiZDAxMmUyNjk3NTkxOSIsInRhZyI6IiJ9',NULL,'2026-07-20 22:26:18','2026-07-20 22:26:18'),
(7,'01KY090XXFN4N63CGSHY74XQG3',1,1,2,'6e69d28f15ce437f1c8dbc20484d0eac888ec88f592ec309406c060a72371cae',NULL,'NEIL HUMBERTO TAPIA',NULL,30.00,'PEN','received','yape-1.0.0','2026-07-19 05:11:40.000','2026-07-20 22:26:19.000','eyJpdiI6IjdKVFFDSGlldzlNbzRJOU1zdEtPMUE9PSIsInZhbHVlIjoiWHJvcVliNWtnTFVIc3V1aWlSQjNZYVhIOGcrank3MkNmNzNsdmVxREt0VTc1TGRlYjQ0c2NvN25zckM0MkFzOEt4TzVQRnZxdURjODJyelk1amhHQVpHUFdQYS9MV25CZ21rbWFQUWVaV1ZWTFcxK2hlVkRwTlFDK3plcy9WZE81WnAwNDRNMlNxbytFeEVTNW5aQkhla3BwcU1VOVhwenFaQXE1d0dBaDltRTdOY0VuU3hqcXFUeW5YVjFCdWZJS1lqTHFYZ3lHODQ1ZzBBMndNaFpoUT09IiwibWFjIjoiMTM0YzQ5MTc3NTVmN2Q3YWVmNWQ0ZDI5MGJjZGMxMGZhZjgzMTE1MzNmNzdlNTY2ZWIwZWJkNmQ1ZmQxZDU1MyIsInRhZyI6IiJ9',NULL,'2026-07-20 22:26:19','2026-07-20 22:26:19'),
(8,'01KY0CJ78JWD5K8QJKP5A9EDJN',1,1,2,'b8ac7ee810c27113e27203e0453eca95cd894df0f643b6f7358a30e5586150df',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-20 23:17:04.000','2026-07-20 23:28:11.000','eyJpdiI6IkFiN0x2T3RBVENJTGNZMjQwczNoeGc9PSIsInZhbHVlIjoia2JheUlxZVZTQlNjd041VTI4Z2JSbEZxdU1YLzJJait4RzRHaGJIN1o5OGZ4dG1HZko1N1FDdytxUk52a3ZXdFc1QVhTMlIxZlJqaDE2aU1kVVdVQWp4MU9NcEMrQXZSUENETlBWU0VrRGpEaythTWkvNlMrM3BRdmw1dGlzWEF2blJZbkQ3d3N1eVZNUFp0UlpHMGh2cVYwU2trSXhMOWdEUTY4bVgxbkxnenRMQ0lSbHh3ZTBoQlFQZlBiSVZzRE5pc21tOWNESmo3ZjM3R3FmM2poUT09IiwibWFjIjoiMDY5YmE5OGU0YzhmMzY4Nzk0YmMxZDBiNzA3MDBhYjc2MGM3YjI1MzNlNmQ3NTQ0OTQ5Y2I3ZThkNzU2MDgyNSIsInRhZyI6IiJ9',NULL,'2026-07-20 23:28:11','2026-07-20 23:28:11'),
(9,'01KY0DH7RXR1DE405G7MA1F1HC',1,1,2,'6a3c7a03bc78e04cd37c19900d8e914e907c2f6e572e7c8923f94c376670c8d6',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-20 23:45:05.000','2026-07-20 23:45:07.000','eyJpdiI6IkpVZkZZYmdkRzFlOVpVMmh2Q3dON3c9PSIsInZhbHVlIjoiS3hNMW5LaDNOS1dSaExMeXROaHRVSnptSHRTd2tFWEQ5WmpTSkJSdXZyRTBobnBzTCt3QUdxZlUwenF3d1ZCQ3k2NTRKbE54Wm8zdGhaUmU2d2hLWEhnNXdLbGhzRG9OOXR4WTkxbWIrdm5XZjN3ZS9COTRUS3VjTDh0TE9CYUR0dU5nc09aWG55eHdBMENMUmNKRVhIREJaTUtCb1RHZXMrU1R1ZjZTOWZ1dGF1VExXYjhTUnBZbVJYeEpVTkpvRmRnT3lOaEZqQ0JhMWRXd0J2Uk5BZz09IiwibWFjIjoiNWYxZDk5YzRhMWYzNzhiMjc4YzE0NzllNDM0NDE5OGU4NzYwN2Q4NGNmNmU2YmM3NjhmMDIxOTZiMDVkYmRiOSIsInRhZyI6IiJ9',NULL,'2026-07-20 23:45:07','2026-07-20 23:45:07'),
(10,'01KY0DK8BKXABGEXCBWKCVJX49',1,1,2,'d397453ce68acfc77e194920594620274c616302df9cb9dadd6aa652ba5ebff6',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-20 23:45:54.000','2026-07-20 23:46:14.000','eyJpdiI6IlV5blJ1aHFwa0J3ODFCUTVwWXgvMWc9PSIsInZhbHVlIjoieGV2QXBnejdTQWlnUTR3VTJCMVFhS2tyNUFUd1F5b1RKaG16Ulgrd2FFNjQwbVp1YzI2ZCtwUUlGNTJ3SzVPT2k0RER1TDRFVjNnV3ZNeVlOTUFIRGxmZXZXK1A1Y2VDRlpYUmwrRFpxVDVlVDRVVGN2UENBQXNiVlJudWhiUFBxL1NIVHZ3MHhQekFYWDRtQnF5NHRsVlZURmE2VitjVnlaNHFhdWp4RWNVUERIa0R4WEpCQnNHUE5nN0lzQWxtdkJ2cVQrcXdkZDJ0N0E2OTRWLytsUT09IiwibWFjIjoiZGMyZTliMGU1MGNhMzE5OGZkMTkwMWZjZmUwNGUwZjU2ZGUyYTYyZjUwNTA4OGMyMzM5ZDJjMmZlN2UwNTFlYyIsInRhZyI6IiJ9',NULL,'2026-07-20 23:46:14','2026-07-20 23:46:14'),
(11,'01KY0HKM5C5CGKQNT2MMT74HKF',1,1,2,'86720e7db94afd00dbcd076e9b922653a7d33e207bf51370ce13577b8f6ceee8',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-21 00:56:18.000','2026-07-21 00:56:20.000','eyJpdiI6Im1CZUw2dnp2UTg2c0cwWm8rYjZZR2c9PSIsInZhbHVlIjoiRHFpZHJLRjUxazRITEJOby9OZCt3eDhMQWhFM2IzSDI2Qk9MQjVQc1FOeHRGaHNQbGdaSXk0dzc5VmQ2ZnhZQTJxblR1ZkVkQ1ZHSStqMXA4dWNSdDMxSFVFQWQvS0xRYmpXYi9aTkx0TjBYYTZURG9sSGpwUTJCeTJXNGtnaU90dFR5R1IzN3QyRWd6aG50Y2hkVmp5cU9JSmpCUjFUb21MN25ibWhTaStZMkZPQ0ZGdkg5a0lMajJJVHNLbUdsQjZCQloxc2RzZDVVNUJXV3JMZVJCQT09IiwibWFjIjoiNjU2NmFlNzJhMDI4ZWUwYWEyMzAyYjgxNDdmYWZjNDZiMDVlZmM5ZWM5MmRiNzczMTFiZWY0NTJkMzA4NjkxOSIsInRhZyI6IiJ9',NULL,'2026-07-21 00:56:20','2026-07-21 00:56:20'),
(12,'01KY0TJ66Y2WDYCEN6KBVMS5JD',4,1,6,'3cc5e96796701194e9038e7b6ad9ee068db445bb9678cf8746f18fa94aca6475',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','confirmed','yape-1.0.0','2026-07-21 03:32:48.000','2026-07-21 03:32:50.000','eyJpdiI6InNIWDF6SnZRcFlzd0hzOUFlRnhTSkE9PSIsInZhbHVlIjoiVGE4UFFWVXYxOSs0MkV4eSt3KzZxUUx1WFdEdE4rMHJ3U2RxTytHNnNmN2xiRDFaZGZOSXZBUjU3OTlIKzNna0FqUitxSG5OSDlZbVpsdXhselZaWGcrRHRxdWtwZDBwc2l3N1p2QkkwZTE1dzRUSGRNYnYxWndaalU4UlJ2MXdMYU9XdStXVlZLM2s1WFdSOHlOOXdGUGFmYlAzcjJ4MndMcjdISFMzblAyWWt3U2tmTy9mUEF5WWJ2VHVPSjFuQ2Qvd0t6TmRCQWtuNlM3RUQreUEwZz09IiwibWFjIjoiYmNiNzg0ODA5YzA5ZDg5MDllYTgwOTMwMzAyNGNkNmUxN2I3MTA4MGU3YmVmMTA2NGNmNWIxMzUzYmY4OGFjMCIsInRhZyI6IiJ9',NULL,'2026-07-21 03:32:50','2026-07-21 03:33:06'),
(13,'01KY0TMS1MCCTAPTQZ41F5798Z',4,1,6,'9565e0e1ca8185465c978e4aa689c4d02f9f485e24b5f3e3767245214f487f3c',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','confirmed','yape-1.0.0','2026-07-21 03:34:13.000','2026-07-21 03:34:15.000','eyJpdiI6ImdTdG5pNnA4QjM0R2V4Z1d2dW13eVE9PSIsInZhbHVlIjoiQUZDUitqM0FKay9BbHFTTERRd3JvL253SUVUTnZ6MkE0NVN3dDhoNzRhSEVxb0pqWmt6c1RBeFdwNzJncEQ0U2o0VGRTKzFNNnlPMy94Slk5bXFrZkhVRFd1RHhmYTlBRDY5czVXNWd2ak55TWZKeGJXMFNBZG4yUStGV2duVWFyNHdydE9UejV2bnRJYnpNZjBSNHNrQkdDNWlvSlBkVXlSV0d4Z1BxWmxqeEJZaWk0K0lGblJyVGpwZURPaC9PMDUvTTIvemdVOVFGNVR0MGJRSkNFdz09IiwibWFjIjoiYjE5NmM0NTM3ZjZmMDU5NWI1YmMxMzliZTY5NjI1NDYxMTQ2YjgzNTJmMmQ1ODAxNGY5ZGM0NWM3NTQxOWEyYyIsInRhZyI6IiJ9',NULL,'2026-07-21 03:34:15','2026-07-21 03:34:39'),
(14,'01KY0WFTPSX307TQ1BP3PA3WQ9',4,1,6,'312a6a5ea672ca3a1e6d9ad16ed55b574db54f59d4cbed3610343093213eb1be',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','confirmed','yape-1.0.0','2026-07-21 04:06:28.000','2026-07-21 04:06:30.000','eyJpdiI6IitaMERmS3JwLzk5ekFIcjU0Z0dSNlE9PSIsInZhbHVlIjoieDRRS3A5amlINHFiN0h4VTBUVnJ3VzBNWW4rbnVodFdHbnV1aTBxa2RRZDVJeHJ3cXhxK3RibEY1WFZaS0IzUitxMU9YQ3RaVTYreEU3S1lwakdWRWZVVGs2Ui9qWkdLM3JncWl3S2pFY01wMUp2dVZObHVPZC9pcENNMTRKR2ZrQnp0bjc0c2FPaW43b09VWGNWUXBnVjFTaTZMZEtqMUI2SE9mbUFRVXF6ZGhwL09FQmdCOTV6UWJpYkVQdVM0aC9WTmJNRWVOWSs0emRyQ0NadWZNUT09IiwibWFjIjoiYzdkYTVlMjQ5NTNlNDY2NTAwMDQxODhiYzYyNjYyNTRiYWFlOGViYzVlMmZiMTZjMDgzYjI0Mzc1YWVmNTk4NyIsInRhZyI6IiJ9',NULL,'2026-07-21 04:06:30','2026-07-21 07:05:23'),
(15,'01KY0XGD4EAYEKAC25Y9BXQXJG',4,1,6,'111f858439a5488e2003745c4dc10703d7f747d6966ecfb192db20ace3309d68',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','confirmed','yape-1.0.0','2026-07-21 04:24:15.000','2026-07-21 04:24:17.000','eyJpdiI6IkFMbVFJWTRTek8vOUxTMnN1aTcyUXc9PSIsInZhbHVlIjoiNXFkL2JWdm16bUFYZG92NS96ZlkzUm4xNmxsdFNZaVNZOTZBUDJKajVDU3VPTno3ZEdORkpkZXVtallDaFNVb3FFcG9uREZ4OGtyUkhPZjIrcUN4ZmZtZkJ6ZXA3RC9nclNUWGpkTWZsSjIrcmxqNzgrL1l6RitNNVhEdGw5VGwxdlk4VGhwNC8xdUtqV0dsellGRlZZb2lCNkZLdWlicVgzUFhJN0NhNFZjKzhWRUsxMjFNelNhUHhoaVNhR1NSekNnRnl0Zm9LQVY5Q2hVdUdhaDZodz09IiwibWFjIjoiZTJkM2NlZWU5NmZjYjc3MWJlYjM2MmI3YjNmNTE4OTdmN2MyZDY0MmY2Njg3ZTljYjY1NGIzMGNkYjVhYmNlNiIsInRhZyI6IiJ9',NULL,'2026-07-21 04:24:17','2026-07-21 07:06:57'),
(16,'01KY0XHQFPS94K0YFYKVSXQGZ7',4,1,6,'9463ff4e110b5b7185badacff9a15ce55b3a098a65590fe24afaedf13f1aa314',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','confirmed','yape-1.0.0','2026-07-21 04:24:59.000','2026-07-21 04:25:01.000','eyJpdiI6IllMNCtUVm15QkM5OGdWV0JHZDNNMFE9PSIsInZhbHVlIjoiS0JkSnJ5aklxbnprVjQ5QTEyVjM0QWRlbDVwKzBLUHU4NlJpQnBmMjhVZGlnNGoxZEhDSmxLZVcrUkEzN3YxVkg5aWp0eTM1TE1UWC8vQWtEQk5BVy9QdkpncHQyVTZ2RTRNdno2YURDRis5R3FGNUJ2dVFOdGFIVVUvU3lXSndVdDNGOHhKRUY1VGl2YU4xYjM0QUFNbHdVVTIrOGhzT2JNUzVQaEdENEZpNTZrV1I2VHRqSHNjQ081UlBWR2ozZHIvUTgrSHo2cjJBaHRnMXVGUTUyZz09IiwibWFjIjoiNWMzYmEzYjkwZGMxYmNkYTZjNzIwNjRiMWZmMTNiZDk3MDNkMTdhMGUyZmViNTQyNjhiNGNlMWRkYjQ3Mzk0MyIsInRhZyI6IiJ9',NULL,'2026-07-21 04:25:01','2026-07-21 07:07:17'),
(17,'01KY0YC76PE41T611J05RANVMG',4,1,6,'04b2a4a2b3aacad465814ce1146a651af972ea9fece5a5728bf3b0c8530e858a',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','confirmed','yape-1.0.0','2026-07-21 04:39:27.000','2026-07-21 04:39:29.000','eyJpdiI6IjNuUFVqSlliMytzZW9PTmtXNHNDK2c9PSIsInZhbHVlIjoiRFBlZzdpSDNYQ2xweU9RQWZGUVA2V29id0IwTXNhbVFCYlZjWk1RSTFhemdKOEd3R1RhVVpocjZ5ZHNOVC8xUGpVaWEzazA3YXh1VXNiemw1YlVIZjN5ZGhoNGdsNTlMQVZxUHJVUTd0dlZKZVd6bzhVZEJZem1vV2JXaTZHcnJVbmJ3YjhGV09aRVlyZjlZdkozRG9EZkRtN2F2K0daZ0dUank5czdySU8rWm5DejZsZ1hIWCt3TmtpVkVudTVKclRmZUtBdDk5TlVaNFZ0RmdObm1IZz09IiwibWFjIjoiNDA4Y2I4MTRhM2Y0Yjk4MWVkMDg1ZTBjNDg3MTdjMzExZTczMTQ1YzUwMTRiNTIyNzUxMTcyMDkwNTRmNTFjYyIsInRhZyI6IiJ9',NULL,'2026-07-21 04:39:29','2026-07-21 07:22:02'),
(18,'01KY0ZQN48MF72BW6AHF1PCFJA',4,1,6,'2f023447b801c615f6b499337d03656eebb9a948c8a982677702869fd105a16c',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-21 05:03:10.000','2026-07-21 05:03:12.000','eyJpdiI6IndNeEJ5VEx2QUwxZHRlYUplTlZNZlE9PSIsInZhbHVlIjoiakM0VVFvYkp1WVlQa2lvZTlOR1IvYzlNbEw0bjNVRjlPSS91MUVtL3JBaGo5NHU5RDY0TE9DL3VsMG84c05ueG0rTTRnUjR1MUtXQjRGejFKWDJKSWlVcTJ4Mk1qdHV6U2NkOVkrNm5YZktlaitZL2EzVXJyaVRFK1R6WnpkMDJTU09jMUtXcVZ5ZzllRFdEbDJHZ2wwQm1zUWZZSy9FZWxlc0VQNzVnQ1FHaVpINHhad2xLL2JRL2w5THpuSjVNNTJuK0YyQmNvdXlscHBDSkdYUmF2UT09IiwibWFjIjoiZGVhNDA1ZmQ0NTU3NWUyYWJjZjYzOTYxY2NiNDRjMjkxNDIwOTNkZjkwMTQ3ZjUwZTk4NGUzMGIxZjhmZDhkYSIsInRhZyI6IiJ9',NULL,'2026-07-21 05:03:12','2026-07-21 05:03:12'),
(19,'01KY0ZRNKKSBHAFXAFXZHBJ28G',4,1,6,'56afd552c3503fea61a5ec996b2716001c47235c537dd4889c1a06924b1f3a50',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-21 05:03:43.000','2026-07-21 05:03:45.000','eyJpdiI6ImJGcmRKRUxYelpKeE41cGlKTHhrVUE9PSIsInZhbHVlIjoiVlBTdXpOWnlxWUxVMnBKV0lhQmVxSHQwSEFUd3BhdFh1NkZQbGhRZk1lTGNmcm9CaklpYXlSdkppR2FHQmlJZmxydlllMnhxQjZBbFNLM28zRkVkMEZxQ01BSlY1UEhVY2ZLY1A5VW1CY1Y0bUltd3pUZ0RPQVRwQTZkSmhoVnVIeXZPWVZiU2drbTFYS0N1bEdSVFhTNDBmL0tmWjE0ZGRBK3JmTXY4R0l4OEo1RGdkWVhOZ1paakwrKzQ0REFkU1FkK2syek9QbUVaTG5odWI2VzZVUT09IiwibWFjIjoiNzZmZGQyOTE0YjM3NzMwMzgzNGYzYzY1YzA0OWRkZDJiYThhNWFkYmI3NTQ1MGM3YTkxYTI0YWE3NjgzOGI4NiIsInRhZyI6IiJ9',NULL,'2026-07-21 05:03:45','2026-07-21 05:03:45'),
(20,'01KY10RSWWJT9N3X0WM3PAEY3C',4,1,6,'a9e2851d11934defc5a6c478cffe5ae02b199b657b5f29f26157e789efb531fe',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-21 05:21:16.000','2026-07-21 05:21:18.000','eyJpdiI6InBtL2lDWFY0NUExTm42bDhoVGp6bUE9PSIsInZhbHVlIjoiUm1hTU5YME9pL3RMbThBMzR0RjVJUzR4Qi85M1EwZnV3RGVMcGl6c2NrZk53K0xxOTJEVG8ya3JPVTJxaVJ6UlNodTJ0ZTlydVlFYTlSN1Z3UFBWT2tMTUFobHExV1c3OTFkTDE1aUJ2NUxnaWR1YmVSWmhQZkg5dUU3SU1xTTJlS1JwMEo1TG4rSE10T3hvVHZacXlNaUVodzMzQ3hNb08rbDdYb0hRMi9wTzVlU3VOUjlCR0ZyZGoxRDJ5Q1NPcVV5ZW4raHBLUkFCM2lXQmlBTDM4Zz09IiwibWFjIjoiODYwZTNhZDRkMjYwN2Q4YzliNjllZjExZGE0MWMzY2YzNjdjMTY1YWEyZWZhYTgwMTRiMzY5NmRmYjU2NjUzMSIsInRhZyI6IiJ9',NULL,'2026-07-21 05:21:18','2026-07-21 05:21:18'),
(21,'01KY128H4ZF8W6V6PPBPMHTHD7',4,1,6,'11965e0d4f3f95d3e727c87322e76c46077554b44cb21dff4fbcc33475211a46',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-21 05:47:20.000','2026-07-21 05:47:22.000','eyJpdiI6IllhS0dSMWU3RW41Wmp2ZW1sU1VaT1E9PSIsInZhbHVlIjoicEpTU1d0SGJXSlFSUGNsTVF5cGN3Sk9Tc0QvU2wzSWwwUmQxNFRXQXcrOTN4RHFIL1VmYzFIMytoYzlwdFZmNmJuM1l6MVFHN0hEK1Uwa0V4RkV3b2pBdGZZcUl4MTFhLzBzOVNpbG1HUFdPbG1zcVhkdERPb3ZFRVZSb2pkSmhJcFcyUUVFdnp0QzFTdFo3UHBUcXNKYnUxTTJubDBHS3l1cktURzZEcDdTblM0NExFVGJxR3RidkR6a3hvS1lTcytSZ2lrTWtxa3FuazZtMUNwT1ozUT09IiwibWFjIjoiYWQ0YjNiMzlhNDZjM2FlYmYxNmJjMGFkNzFiZDEwZDcwNmU1YmNiZDQ2ZDMyZWY3YWEwOWIxNmZlODllZDhlMSIsInRhZyI6IiJ9',NULL,'2026-07-21 05:47:22','2026-07-21 05:47:22'),
(22,'01KY372AG3VG9PXQ18R7GQMEXQ',4,1,6,'b5463e4a036639ab8e2aa57a6ea45448035543b02d18799a8f4d67a60855e758',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-22 01:49:50.000','2026-07-22 01:49:51.000','eyJpdiI6ImlLbGoyb2J1VjAxYW5ONmFPZjhJOFE9PSIsInZhbHVlIjoiQjVxK081SVhqdXlIZlBlTHJDcENTSG5lVWtqUEV2MDhsL255a1pFQm5mQzBCWm9JU21lYUJUbUhMd004MXBiRE0wTStUaGt0bkZMM1MzSU11dGRRQUpMSkxHR0U4ZEpNeU9aajk5ci93bWlya3NJdjhPbWlHQUpwWUc1TlhRcVJHQm1YbGdzaW4wQzJJbUIxaW0xT0hSK1dyVGlrc2pOU01UVkczM1c4YU1YSkpyd01FTVM3U1U4R0phMk5vQlUwSkVDM2lwdzVnMytnS1hPWGowc285QT09IiwibWFjIjoiZDc4MzIwM2UwNDAzM2U5ZWRmNGVmMTFiZTlhM2FhODQwOTAxYTExYmRkOWFmOTQxMDRhZTI1MDkxYmE5MTEyMSIsInRhZyI6IiJ9',NULL,'2026-07-22 01:49:51','2026-07-22 01:49:51'),
(23,'01KY5MRDT141JS7CHW4QTVE19H',4,2,6,'f4e4f572ad0d432981f4f6833e4acdf9df51b8b55de16523ba3516ee978c2e0e',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:20:58.000','2026-07-23 00:27:35.000','eyJpdiI6IkxjTzBucVdSRmFsT25YT0VrTEJsRXc9PSIsInZhbHVlIjoiZkV6WE1Jc2U1aW9uRGhCZUF3ZG54YWtXUUFKNllXREJJL0ZrbjROYVFwN3VMZlFaUVhyLythRHBCQ0E3YTRFS1Z1UlJ6VExnTWcxRmhmOFlXVWY5MzRhZm1BZ2tjRFR4dCtTeDd4UE9jc2xuc2h1dkd5SGtYemFMMi9qMXlZYSt3RDJrcVk0REZzc3liV2dtNlliaW8vajdoYndkNDY3c2VnSitCUk1hb2ZSNjJKcEMybXo3OEk4YkovNmNwRFJLIiwibWFjIjoiZTRjY2MxMWU4NzU2MTQ1M2FlNzYzNzVlMWJjNjQ3ZjA3NDdjMmZhOTI4Y2FkZWU4MTUyNTc0NTdlYjQzNTQwNiIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:35','2026-07-23 00:27:35'),
(24,'01KY5MREA9JXT4Z9YAF19YDB93',4,2,6,'ddf6c4c68d324e99781981c9975ccbb7b20e1fc88fe4446928187843c9921a51',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:11:06.000','2026-07-23 00:27:36.000','eyJpdiI6IlFqTFF3TVJ6YU9CWE1FRHF0YkpzcFE9PSIsInZhbHVlIjoidnV5VTVnV2VIbkxnbkJ1SS80b1E5VnI5ZEVzdXE2ZXgrbGZuQmNMT3hUWXMwbWZyZ0JaaHJxVDFpYm11TkNHWFliRXFCVXpVNDE5ZXhtSWd5enFZQmFmTE1UNXJja3ZyOHZLTU5PdGsxUUNKTlVLNWZ4TTdRN0Y4MGpSb21XSkltVk9QV0F2THl0ZWNhTWM2cVV2a0NOWDYxZzBXc0NyaitTbndRSVd6WGVxTjVCZWc0RHNiVVd1ODAxVWxZZWZaIiwibWFjIjoiY2RkZDJiOTRlZDI4YTNlZDViOGY3ZWY4YTVmYWQ0YjdhNjY0ZmE2OTIxZjIwNjM2MzYwZmE1YzJjMmQxYTA1MiIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:36','2026-07-23 00:27:36'),
(25,'01KY5MRF385V7B4T53YC3W8R9S',4,2,6,'5a7dc818d4aa44f76b2c5b6fb446ad3c4f3adb0b0a82e76ed9642b2c5e8032a1',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:06:18.000','2026-07-23 00:27:37.000','eyJpdiI6ImQyQjNWZEs4elJGc2Zjdk9qZW5WcGc9PSIsInZhbHVlIjoibWswcHM4ck5hZ2ZDeGd3MUhzclJuWEx4dGJuMWhFV3BLODdHSHdwTHJCenp3UlpHUXhhK3FSUEY0M09teWN6SkdvRUdMb1k1dGlJbklBTE9sMjlZTklNMUlhWFY5YjEvMWRZdDhoZjE0MUpqYmRkU1VuaUp0VkxMemtlTjlJNCtrUjgySzM2NUpJcDlsZ1BOL2crT0JPeS96clJXMXpVS21BQ21KNmVURG9Jb00yME1GUjR4ZjBVWkh5MGR3SHdYIiwibWFjIjoiNDkwYWZiMjg3ZGJiMDVlOWIzODNkNGIyZjBkMTlhZjBlYWExNTJjODk2Mzk3YmE3NzhhZTk5NGZlOTlkYmRmZiIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:37','2026-07-23 00:27:37'),
(26,'01KY5MRFZG75JRHAXEFYRYBNHA',4,2,6,'a8c0de26061db0e2d3f154ec96f35d054d23183aa46f2cc224781ba28db4c06c',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:06:18.000','2026-07-23 00:27:37.000','eyJpdiI6IjlEeWhHZEZHZGdIOUVId2xwUFpjSUE9PSIsInZhbHVlIjoiTzBySVJ1SjhNRVFJSnN0KzlSUjBiK1lKQnRIZnplU3hmN2pzT2tLUFBqeUNwbFBuMzZmdnc5QjdwNGw1YWNON2xKQzFjellTeVFWb0RhWUtSVnZHN1NWSXdUMXV6V2pKaTM5MkF4U3pxcGk4MjlRUFk3aEJ4SHlQM2I5NXJ1N2VmQmFiSXJmZ1ZTTElDUnBJd3VxTllDeS9rNy90MFpsZWswMXVXNktPZVZERFgzS1VJSmlYek4wdDljRzdwZFNGIiwibWFjIjoiNjk2YTE3MGIwNDIxMmRkOTMwYmQwNTRiYzYzYmNkODQzNTgxMWQyZWUwYWMxNGYzNzQ5MjFlOWFkMGE1OGI5NiIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:37','2026-07-23 00:27:37'),
(27,'01KY5MRGP8E1VPDB4V5B44BAZ6',4,2,6,'aefa3059dda07eef38c733763e956145da5821fa713ebcd0134aad8d4af828fb',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:06:00.000','2026-07-23 00:27:38.000','eyJpdiI6InN0R0k5MHJ6UXlRb0tKSGx6cVkydFE9PSIsInZhbHVlIjoiVEVIazYzclQzSGE3dzhHUmxLUzJxUWgwUnkrQWJvTnRoeGc4eDczVXhVSWhqZnFzTisxZHA1V2VMd1loTTZSUHJIZERHSkxSalZ0b3E3TG81MnZWcnZWUE1iOFNJL1dqa3F2UzMxZURqZklVTWZlbzNZbWRTV0VyekFKaUhNTTk0Y3JwbkgxMXkwanNYeWFXWG9SSmRzSEpxSDkxNStmSkE0ajk4eTR1aHJKTFFwY0t0MG5KS1pTcm45cDlSQ1hoIiwibWFjIjoiY2MzYWY3ZjIzYTU2NjU4NmYxZjA1M2ZhNzdiOGE2NTE0YmQxMTI5YzNlYWE3NjI4MDhkYmRhODFlZDE1MWU1OSIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:38','2026-07-23 00:27:38'),
(28,'01KY5MRH9C3B9JPPWQBEQ33S0T',4,2,6,'0c68b5a4c72ae8469459fee6747265ba8451c59aba6edbc27725287f7dc68efb',NULL,'Michael Osiel Ortiz Pacaya',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-22 23:57:03.000','2026-07-23 00:27:39.000','eyJpdiI6ImJFay9ZT2JoYjUvZ0g2VWxONE9KblE9PSIsInZhbHVlIjoiQzdVb1NpVTI1OXEwZHhpRm42QVlta3orYitoT2loNzVHam9nQ0plMzErTW9Jcm0xSkxkRWlSWWp4QzlXbDk1UmxJc01pTU1HeWFJYnlmNjFLWDZURDRXZXZxekVaV0l0MGYzUVdsOGd1L3NlaVJBS3dSTmlxbXdMQkdUVkJFQTZKM3hwRGNHM1I5SVNFNmxzUGdLbXVaOWtPK0xWc040RUZZNjMyalRMNzBTbXM0SkdKZHNiekQwbHdXcHVxNmFSIiwibWFjIjoiODQxZGI1YzM2ZDFhODMzY2Q4NWY3YWJkOGUwODY1ZDRhZjUyNjk5NWQyMTY4YjcxMzI2Y2E5ZGNmNzRmMTkzMSIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:39','2026-07-23 00:27:39'),
(29,'01KY5MRJMGHVYA22WRRY8SXP85',4,2,6,'e0cb79536f0cc1f8cb3dc782f6729a31a736aef674bbbd67f395c5e2d166455f',NULL,'Michael Osiel Ortiz Pacaya',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-22 23:55:30.000','2026-07-23 00:27:40.000','eyJpdiI6IlFwcWRpOVV3enhwNzB2UDFRMytqaEE9PSIsInZhbHVlIjoiK1Nma042WEZsWnpFVWhvN3dkQmlTYzVEU2ZwZzJ5U1MwS1JERGVlc2lLK1ovYU1FZEd6cVRWUEhOVzdhQzlrQ2lBUzlPeis5cVNtNHpLQi9TL3doY1N5SGNoM0N2WEZiSHd2SUVmdWwzV2lGTUdGRmg1cFh1a3Y2TEpnaWhSNThZaXAwMWRqemdUc2hLTjFzMjlUTnVuQ1RqaDRBYU4yNmhzV3RDZHBpTndER0VwY2t2ZFNJeHNwT0hlNjl4cTluIiwibWFjIjoiMmZjYzQwZGRlMzVhZDdkMzBhNDdhZTc3YjY4ZjBiNDhkZDNiMTFkZmRiZDU3OWIxZmQwY2U0ODRhMGU2ZDExOCIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:40','2026-07-23 00:27:40'),
(30,'01KY5MRK6KGG9AWD283P25PY9Q',4,2,6,'3d76eeb0041265399236546919f8f1983657d5e3069481d8f9c79a8eb258b2d2',NULL,'Michael Osiel Ortiz Pacaya',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-22 23:53:11.000','2026-07-23 00:27:41.000','eyJpdiI6IitqRDE2a3NrMTIvU2tBd0NwbnZoQlE9PSIsInZhbHVlIjoiNHN0Ym1QWUVWYitBWkYrVG83RmVpWHk0bi9yd011UjR0dGEzN0M3aVdQc3RNb3F0SFRDZERJNGNBWU1wR21wYmVhSlVyMk9vWEZYWmRFYXVjY2NRZmxMWWoxVWxjTzRyNE45MUVLZ21lSjhaN2loeUtRL1Fud2hPNnM2dHZEUTVBQXZveFphSXhjaEVPekpJTi9SSWxobHZPRG1hL21xcTZ1aDZrQUlzbjVRVzZLc3BTSG95R1VWNlNuVkp2alhiIiwibWFjIjoiYmE5MWU2ZDQ2MzJlNDYyODdhNDkxMTg4OGJjMjM1ZDQxYzNmMzBiNjA2ZTcwYzQ1MWM4ZTI5MzFjODhlYTMwOCIsInRhZyI6IiJ9',NULL,'2026-07-23 00:27:41','2026-07-23 00:27:41'),
(31,'01KY5N7T5RDJQB4D42TK606HVJ',4,2,6,'4f543410445b6d704f760da5e23ddf9eba806940dcc16b11cd07994b5388e67a',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:35:58.000','2026-07-23 00:35:59.000','eyJpdiI6IlMvWUJhRU9acHdWYXBNMXJJc1I5MlE9PSIsInZhbHVlIjoiY2lsbjNSYkRrM25Nc0p0QTVBd3VXL05xZzBkMEFvZ2JuR0MvQUsvRmpta2FKRms2Z0lRT2lLNlUveW03WUZTckk0Q2UxWkdkelZGbUp1RVB0bmZEbGh0NUthR28wOW1OQ2R5R2RVNHFVN3NsTkFOM3lvcnByT3Nic3JueDFjR0NTY3hiSVQ5NGVXVXdXcjlBaDhxODFvVlFwTlVQVUZzT0tmRTJicjFWZW8vVkE5VVpBem9xR3JmVFZqYm43V0QyIiwibWFjIjoiOGZjY2IwYzFlMWZiNjQ1ZjJlYmNjMmMwNjU3OTM2NWZmM2NmNDAzMjVhNWRjNTkzNmExMjM1YzRhNzZlZmM4OSIsInRhZyI6IiJ9',NULL,'2026-07-23 00:35:59','2026-07-23 00:35:59'),
(32,'01KY5NH5PXCSD6A2YFS753XD2M',4,1,6,'a508e5247aca88d16abeb840509befced0516da9927ec6313d9304121bd7e1f2',NULL,'MICHAEL OSIEL ORTIZ',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-23 00:41:04.000','2026-07-23 00:41:06.000','eyJpdiI6Imh6OGpWbFFmWmV4RUFmakdoVVh4TVE9PSIsInZhbHVlIjoiUTNheVlDdFlrMTFqRVp4TTd4MHlib0xaTGhHWGgrd2FvUm82Z0JGdVRPbGZQMkdhbXNKTGUvSmFwZkd3Q0ZVUUFLZTBZeFRuR2F1VCs1aWN1RHVjQmdNQitLTmt0WDdsSWd4MmFHYUs4NkJuWXlzVGZuU29HT1RYWEZ0Q3RWU1g3YmVzbEFtcnAxSldXTC91ajl5aU5yTjlyNGY0d1QyUVQxbCs1UzNZbUdHQnRVSlA4NnZjaEhKZThpRVRQR0cydVBOLzEzZDBPQVFOaVJ5STlWMDJaUT09IiwibWFjIjoiZTU3YWRmYTk2Y2U4ZTcxNmUzMWRmODYwNGNkMGI4OGE4N2RiNzRkMjRmY2ZmYjZkMmE2ODg0NjViMTE0MWJlMCIsInRhZyI6IiJ9',NULL,'2026-07-23 00:41:06','2026-07-23 00:41:06'),
(33,'01KY5NHDAHFCTPWAQT462Y8HCP',4,2,6,'1ab0326b6d68de57658f01a3a1dbb02be853fa82150769252ad438235c9506f3',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:41:12.000','2026-07-23 00:41:14.000','eyJpdiI6IkNIRlI2ZENSZUJSYzgzcWtISFREQVE9PSIsInZhbHVlIjoiN0h1VTdBK1hoaWNLcFdReW92V2xVblBKWTE3VmRHYlN4blIvRngxY1gvRkR0OEtDODV0amxIODM0YTZyTXBLR2lpNUJPQ0ROUEhpQnRpd0JLUVBxS2I3cWlsVkNHMDJyV0JPZHFrcHlIVCtPTFlNcEdvSjluNEJzODJobWNpd1Zla1NnRWlEcnFsS3I1QlNIc3FBS05mNktWK282amtxTmVhZkJxenZSZ0MvL1NoNlJGaDJmZDFhcDBsYVJTVnRIIiwibWFjIjoiMmYyNTIyNGIyZWE1MWZhYjRmMmYyZTA5YzMwNDMwNGU4MTkzYWRmOTBiNTY2YzExNWEzMzE3YzEyNmNmZTY2NyIsInRhZyI6IiJ9',NULL,'2026-07-23 00:41:14','2026-07-23 00:41:14'),
(34,'01KY5P3AFVCRQJ25S2YPYKWN0M',4,2,6,'631e72c7aa4b72dd48a7e84813f4e8b5de4032394233b753d79263b12678ffea',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.50,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:50:59.000','2026-07-23 00:51:01.000','eyJpdiI6ImF5QS9RR0tlUnIxVXFzT1c4ZkJJRGc9PSIsInZhbHVlIjoiaGlMdDFlQmVTWGRaM25WaHFUd2kwanZZMllYc1FQU2dwTEg5dmQzTXZEVHFrSkhDUmRiZVhDN0Jabyt1dlUwdGk4eTR5WHVDUkVld0thWDAxRU83Z2UxUFB0VkFnbkk3cTNFQ3FsZ1VBcHpkQlhMVWhNSjVQUU5OVUU4WUhqOFdZWjdvZGlHV3dkM2hEWmNCdlZzdVhsY3pveURZbUkrMlJCaEQrakFuOGpYckhwVHZZTzh4YWNWdTJieXYzYWkvIiwibWFjIjoiZWRlZjg5YTQ4ZTVhOWZmZWIxZmZkNTIwNTlhYTBjNzRjMTY0OWQ3YzMyOWE0NGVkMTdkM2Y4OThiYjBjYmZhNSIsInRhZyI6IiJ9',NULL,'2026-07-23 00:51:01','2026-07-23 00:51:01'),
(35,'01KY5P3AR164VXK0PQFQVAKYH4',4,2,6,'314ebe7020d70cc247a0856edf9f77a8cf5eef2d20fad3638d46d81bfce13913',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.60,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:50:59.000','2026-07-23 00:51:01.000','eyJpdiI6InFQZ1RrMEdVbG1ISnIvNjBaTVVONEE9PSIsInZhbHVlIjoiNXlJRXpFYTIrbzNaODVWYTlYbjJyejZHZno0YzNWbG95djZzdEdTamtHbjNrRUNNM2s1U1FqR2xIYms4eUtwVzJibmtxNnZ0emhJeDVMdzdmWnNBbnR0cjhJZmJXbTJ5MlRqVi8rUElURjlndlJvTS9MMW1oS0pqWVV2Y28zcjY1ZCtBbEpXQUw2enpJTEZ6NkVQVDQvUHFuUzkrWC9ORFJYZktVVUFNWXpTL0UwcmIxQ0FFOVNuTUFGRHJGZGMzIiwibWFjIjoiMDVlMzI2YjJjZWY0NWY4MmExYWYxYzI3ZjMzM2Y4ZGI0NzQ3NzZlNzUwZDJiNDdiOWRjYjk2MzhlZjUwZTUzMSIsInRhZyI6IiJ9',NULL,'2026-07-23 00:51:01','2026-07-23 00:51:01'),
(36,'01KY5PCMJJ609JSGQ5RB9SS5MH',4,2,6,'4f8361115cc8ef53403864f11cef9ebedbe8db0e47fee23e30af7fd095c25c49',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.30,'PEN','received','plin-interbank-1.0.0','2026-07-23 00:56:04.000','2026-07-23 00:56:06.000','eyJpdiI6InpVRHdBWSs0VnVTYzBaK2xNV1lVd1E9PSIsInZhbHVlIjoiMlFCem4yMHo1bW1iSXZEMVNrUmxlTzA0RTgvL3VXY1Jwb0o4UTAwUVRYekxLa0tqM0FnNGlKR2x3dnlTb1ZCc0hmdDBXKzJNb1VZNS9xdlgvYlh4anpGUDZ1cDlXRjJWcVdIaHVxMzI1ekREQkFYWjRPZ3pJdWE2MGxOODFHaU9GblBEM0NHbjVwNzlnWTJXTlEzS0hvbk5MQk1tZFZtbjNKOGVVU3k3cGtaTVJzUFJMUW5iZjhHU0RlNW83N0dkIiwibWFjIjoiODk4NjdiOGY5NmMzMDQ4YmI5ZDg0MWIwNzUyYTQ2NTY4MDEwZTAzMTJiMjU1MTA2ZTVlZTVhY2M2Y2Y4Nzc3YSIsInRhZyI6IiJ9',NULL,'2026-07-23 00:56:06','2026-07-23 00:56:06'),
(37,'01KY5RPVQ5XTW50VCZV9EP4S36',5,1,9,'2d1567ea0daa7669cab25cf8e5820c811bb2d5da8437d3ec73e012334c75e176',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','yape-1.0.0','2026-07-23 01:36:34.000','2026-07-23 01:36:38.000','eyJpdiI6ImNwNklBMWcwejVXdnV5VFZla1A5QWc9PSIsInZhbHVlIjoiYTlHbThRelFGNTNKM3llMlZEMlpXaXVxSlhDeWRLNk9obDM2M1UvZDRiZG5RSzBXTHVSN09jVy9WbW9GeENwTUx0KytuenREdDAzRGk0L3BmeFlRSExUQ0UvRStnOUpJZFBUeGlxMmVyVWowKzgrRlRGM1NkdmxvT1NLcjlnZHBaMU1hOEJaMUtKdDh2WnRpbEZtOTk0cHBtdWVFVWczZTdJdml6MGpYc2laalB2eGs0MEx4NXJ6eVpsS2FGQkYyb1NIVGUvSnNaWjQ1SHhGVlZIOW1WZz09IiwibWFjIjoiMGQ1MzAwMmMwNTY2MjhmZWM2N2ZiMTM0NTI2OTY5YmM4YjQ5ZGFlNDczNzQ3NTI4YzE3OTliY2VjODQyMGIwYyIsInRhZyI6IiJ9',NULL,'2026-07-23 01:36:38','2026-07-23 01:36:38'),
(38,'01KY5SD24KBCGZZ33V4YGSYQQE',5,2,9,'6d6e70fc6f40c1784023d7bd3ea15ca7dd36569bfc07365c1e4ad5c50c8dc198',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-bbva-1.0.0','2026-07-23 01:39:22.000','2026-07-23 01:48:46.000','eyJpdiI6ImtSMXN3QlNCS2YzVCsvZmVWaE5tb2c9PSIsInZhbHVlIjoiUG8vSE9palVHdUlrdWZSa2huMFZLazMyZENiVmhXdFBGelFreDIyNHJhZlV1SENqT21FTFA4N1YyUlFVYzJsWUI3NDBhRmxHcUd5Ykt5N0pyTWpFYWRFSFRuRzlIZE1kZjczSWU4UXJ3VDlibDFGM3ZKV1JjaC9rODliSEllUkhBaWtEMmlMZUpZUjZVSnhIdU9kUDhPd0FyRGpSc0x5TUNsdGU5VWJZUjB6NjN2UnJhSXFhTy9vYzIrUW81RGh0IiwibWFjIjoiNTgzMWRiOTJiZWQ1ODIwZWM3ZDY0YjdmN2VlNTZlYTA5OGQwZTRiNGVjMDEyZjNmNzlhZTY0ODBjODBiOTA5OSIsInRhZyI6IiJ9',NULL,'2026-07-23 01:48:46','2026-07-23 01:48:46'),
(39,'01KY5SENNMRCNK8K1Q6M66JNBR',5,2,9,'e5f2820576d5cf3ae569095f6a5986cbc5ed9ad3267ef2602f2937bb4782738b',NULL,'MICHAEL OSIEL ORTIZ PACAYA',NULL,1.00,'PEN','received','plin-bbva-1.0.0','2026-07-23 01:49:35.000','2026-07-23 01:49:38.000','eyJpdiI6InJpSFpCTHVKMThVdEQ3aEpyRnhZVmc9PSIsInZhbHVlIjoid3BzdDFYVkd5MEtQNHRiSElvWWVBUXlJK283ZDY1REViUzZHYU1TYytObXJpOUVMMlplTnBaOU1EdHZGTjRzcmcrWitTS3d6NjRtYmwvc1JXcEpHMzBHbmVDRDNHN3hJcWpqZFlvV3FxM24yN0RmWWpiZEQybWVtcXlPWmdzYUp4Si9SZkFMdWRjVXBEVlRkSWZGMWlDakhnNVdhWXl5RVlUUzMzTUsrSjlqdGNRQUtlMDM0THh3ZlYvTlFwMzBjIiwibWFjIjoiNWU3MzRhNmIxYWQ0NWFkMjViNmViNmUzN2NhNGRiMDg3ODVjN2MzYjk3MzJkNzhlNzQ0YmRmNTg3MmU2NDVlMSIsInRhZyI6IiJ9',NULL,'2026-07-23 01:49:38','2026-07-23 01:49:38');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan_limits`
--

DROP TABLE IF EXISTS `plan_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan_limits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` bigint(20) unsigned NOT NULL,
  `limit_code` varchar(100) NOT NULL,
  `numeric_value` decimal(18,4) DEFAULT NULL,
  `boolean_value` tinyint(1) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_limits_plan_id_limit_code_unique` (`plan_id`,`limit_code`),
  CONSTRAINT `plan_limits_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan_limits`
--

LOCK TABLES `plan_limits` WRITE;
/*!40000 ALTER TABLE `plan_limits` DISABLE KEYS */;
INSERT INTO `plan_limits` VALUES
(1,1,'devices.emitters',1.0000,NULL,NULL,'2026-07-21 01:22:01','2026-07-21 01:22:01'),
(2,1,'devices.receivers',3.0000,NULL,NULL,'2026-07-21 01:22:01','2026-07-21 01:22:01'),
(3,1,'users.cashiers',3.0000,NULL,NULL,'2026-07-21 01:22:01','2026-07-21 01:22:01'),
(4,2,'devices.emitters',2.0000,NULL,NULL,'2026-07-21 01:22:01','2026-07-21 01:22:01'),
(5,2,'devices.receivers',10.0000,NULL,NULL,'2026-07-21 01:22:01','2026-07-21 01:22:01'),
(6,2,'users.cashiers',10.0000,NULL,NULL,'2026-07-21 01:22:01','2026-07-21 01:22:01');
/*!40000 ALTER TABLE `plan_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES
(1,'pilot','Piloto','Plan inicial para negocios en etapa de prueba.','active',1,'2026-07-21 01:22:01','2026-07-21 01:22:01'),
(2,'business','Negocio','Plan para negocios con más dispositivos y cajeros.','active',1,'2026-07-21 01:22:01','2026-07-21 01:22:01');
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('OcE3pWxrfGIJpGFK2LZCmMfHCFti7whmrofYMaPV',9,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ5Y2llUW5Wc2daQjdvVjdJeklUaU52bGFRVHB3dmNScklKcXBHTGoyIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2J1c2luZXNzXC9wYXltZW50cyIsInJvdXRlIjoiYnVzaW5lc3MucGF5bWVudHMuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjl9',1784766963);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_limit_overrides`
--

DROP TABLE IF EXISTS `subscription_limit_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_limit_overrides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `limit_code` varchar(100) NOT NULL,
  `numeric_value` decimal(18,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_limit_overrides_subscription_id_limit_code_unique` (`subscription_id`,`limit_code`),
  CONSTRAINT `subscription_limit_overrides_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_limit_overrides`
--

LOCK TABLES `subscription_limit_overrides` WRITE;
/*!40000 ALTER TABLE `subscription_limit_overrides` DISABLE KEYS */;
INSERT INTO `subscription_limit_overrides` VALUES
(1,1,'devices.emitters',3.0000,'2026-07-21 01:45:11','2026-07-23 01:14:03'),
(2,1,'devices.receivers',4.0000,'2026-07-21 01:45:11','2026-07-21 07:20:05'),
(3,1,'users.cashiers',4.0000,'2026-07-21 01:45:11','2026-07-21 07:20:05'),
(4,2,'devices.emitters',2.0000,'2026-07-23 01:30:58','2026-07-23 01:30:58'),
(5,2,'devices.receivers',4.0000,'2026-07-23 01:30:58','2026-07-23 01:30:58'),
(6,2,'users.cashiers',4.0000,'2026-07-23 01:30:58','2026-07-23 01:30:58');
/*!40000 ALTER TABLE `subscription_limit_overrides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `billing_cycle` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `price` decimal(18,2) DEFAULT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PEN',
  `starts_at` datetime(6) NOT NULL,
  `current_period_ends_at` datetime(6) NOT NULL,
  `grace_ends_at` datetime(6) DEFAULT NULL,
  `suspended_at` datetime(6) DEFAULT NULL,
  `ended_at` datetime(6) DEFAULT NULL,
  `auto_suspend` tinyint(1) NOT NULL DEFAULT 1,
  `terms_snapshot_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`terms_snapshot_json`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_public_id_unique` (`public_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_business_id_status_index` (`business_id`,`status`),
  KEY `subscriptions_status_current_period_ends_at_index` (`status`,`current_period_ends_at`),
  CONSTRAINT `subscriptions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES
(1,'01KY0MD2G0FWNSBEZE7C6EA09G',4,2,'monthly','active',80.00,'PEN','2026-07-20 05:00:00.000000','2026-08-21 04:59:59.000000','2026-08-26 04:59:59.000000',NULL,NULL,1,'{\"billing_cycle\":\"monthly\",\"price\":\"80.00\",\"grace_days\":5}','2026-07-21 01:45:11','2026-07-21 02:29:02'),
(2,'01KY5RCFDJ1WFXD0CKYXMPA3X3',5,2,'monthly','active',30.00,'PEN','2026-07-22 05:00:00.000000','2026-08-23 04:59:59.000000','2026-08-26 04:59:59.000000',NULL,NULL,1,'{\"billing_cycle\":\"monthly\",\"price\":\"30\",\"grace_days\":3}','2026-07-23 01:30:58','2026-07-23 01:30:58');
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` char(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `role_code` varchar(30) NOT NULL DEFAULT 'cashier',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` datetime(6) DEFAULT NULL,
  `password_changed_at` datetime(6) DEFAULT NULL,
  `disabled_at` datetime(6) DEFAULT NULL,
  `created_by_user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_public_id_unique` (`public_id`),
  KEY `users_created_by_user_id_foreign` (`created_by_user_id`),
  KEY `users_business_id_role_code_status_index` (`business_id`,`role_code`,`status`),
  KEY `users_business_id_name_index` (`business_id`,`name`),
  CONSTRAINT `users_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `users_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'01KXSHWSPN49GE0KHE283TKC3M',NULL,'superadmin','Michael','m@gmail.com',NULL,'2026-07-18 07:46:39','$2y$12$zzv/ppMB/dXp5fkRu2Abdus3kcfFtp53n94TQbWdp4sYN4N4ot7VG','active',NULL,'2026-07-18 07:46:40','2026-07-18 07:46:40',NULL,'2026-07-18 02:46:39.000000',NULL,NULL),
(2,'01KXSM99H48RQCQGKQ27WHHZX4',1,'administrator','dayron ortiz','dayron@gmail.com','917374145','2026-07-18 08:28:26','$2y$12$u1pMjO.HiAaEMeFObPDhAub5MxLhKVGNV1nFlcRSCQxDHn0zf5FFa','active',NULL,'2026-07-18 08:28:26','2026-07-18 08:28:26',NULL,'2026-07-18 03:28:26.000000',NULL,1),
(3,'01KXSMWY93MZKKAE9ZBDFXQ693',2,'administrator','michael1','miorpasoft@gmail.com','923878736','2026-07-18 08:39:10','$2y$12$qhlON3VHc/jn7ZtEUwHgkOVSkXmwsGRDXL4d5TdoyLUNFERVOrGby','active',NULL,'2026-07-18 08:39:10','2026-07-18 09:16:38',NULL,'2026-07-18 03:39:10.000000',NULL,1),
(4,'01KXST12FSFQF6X4GMSW6CE7FP',1,'cashier','HAYASHI','hayashi@gmail.com',NULL,NULL,'$2y$12$wBAYwg96AxEvTh/rZ/fETecbDAhF4GYpi.lDITWruW1GNbwXKhUfq','active',NULL,'2026-07-18 10:08:48','2026-07-18 10:19:28',NULL,NULL,NULL,NULL),
(5,'01KY0M11CR6W1TVEHEDK8EDPQS',3,'administrator','SEÑOR EBER','mana@gmail.com',NULL,NULL,'$2y$12$BAdbyk0MyfeMOMFxrjsrsORmfjaZs2dMVDiGxt5VwjqzhArjb7GDu','active',NULL,'2026-07-21 01:38:37','2026-07-21 01:38:37',NULL,NULL,NULL,NULL),
(6,'01KY0MD2FS1C8QWTHR673J7X1T',4,'administrator','MAYCOL TUCTO TUCTO','maycol@gmail.com','888888888',NULL,'$2y$12$Y3dp1CIEjy5oRqWUm3MRbO753DtBfZjr.VVdT3ChGRafvRLSfawWW','active',NULL,'2026-07-21 01:45:11','2026-07-21 01:45:11',NULL,NULL,NULL,NULL),
(7,'01KY0NSBV1XT222NMKBKP4Y58H',4,'cashier','cajero1','cajero1@gmail.com',NULL,NULL,'$2y$12$aLtcRzj4sMMAXrkRs7OP/uNMvMrkDQOhRUvi0HbPtGIOtFuS5FI5u','active',NULL,'2026-07-21 02:09:22','2026-07-21 02:09:22',NULL,NULL,NULL,NULL),
(8,'01KY0Q3B9KX3SCJHTS9ZV177RB',4,'cashier','cajero2','cajero2@gmail.com',NULL,NULL,'$2y$12$FG9JYGfnRftq82sKvjBhleauueKaFOjQlnGgbrn0bl5Fzj4Hzyh32','active',NULL,'2026-07-21 02:32:18','2026-07-21 02:32:18',NULL,NULL,NULL,NULL),
(9,'01KY5RCFBPWPANAV4YKQVJ3VC4',5,'administrator','BELDAD GRATELLY','churras@gmail.com','666666666',NULL,'$2y$12$ttaG84rt9MEljsAnEIbIau5eTdFXfwMN5f2rWLRGM3ZKxiTWzsUi6','active',NULL,'2026-07-23 01:30:58','2026-07-23 01:30:58',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'miorpa_notify'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-22 19:37:00
