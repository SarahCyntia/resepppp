/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - ukk
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ukk` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `ukk`;

/*Table structure for table `alat` */

DROP TABLE IF EXISTS `alat`;

CREATE TABLE `alat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alat_resep_id_foreign` (`resep_id`),
  CONSTRAINT `alat_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `alat` */

insert  into `alat`(`id`,`resep_id`,`nama`,`created_at`,`updated_at`) values 
(1,1,'Wajan',NULL,NULL),
(2,1,'Spatula',NULL,NULL);

/*Table structure for table `bahan` */

DROP TABLE IF EXISTS `bahan`;

CREATE TABLE `bahan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bahan_resep_id_foreign` (`resep_id`),
  CONSTRAINT `bahan_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bahan` */

insert  into `bahan`(`id`,`resep_id`,`nama`,`jumlah`,`created_at`,`updated_at`) values 
(1,1,'Nasi putih','1 piring',NULL,NULL),
(2,1,'Telur','1 butir',NULL,NULL),
(3,1,'Kecap manis','1 sdm',NULL,NULL),
(4,1,'Garam','secukupnya',NULL,NULL);

/*Table structure for table `failed_jobs` */

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

/*Data for the table `failed_jobs` */

/*Table structure for table `favorit_resep` */

DROP TABLE IF EXISTS `favorit_resep`;

CREATE TABLE `favorit_resep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `resep_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorit_resep_user_id_resep_id_unique` (`user_id`,`resep_id`),
  KEY `favorit_resep_resep_id_foreign` (`resep_id`),
  CONSTRAINT `favorit_resep_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorit_resep_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `favorit_resep` */

/*Table structure for table `kategori` */

DROP TABLE IF EXISTS `kategori`;

CREATE TABLE `kategori` (
  `kategori_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kategori_id`),
  UNIQUE KEY `kategori_nama_unique` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kategori` */

insert  into `kategori`(`kategori_id`,`nama`,`created_at`,`updated_at`) values 
(1,'Sarapan','2025-07-23 08:10:29','2025-07-23 08:10:29'),
(7,'asdfg','2025-08-05 09:43:48','2025-08-05 09:43:48'),
(9,'qwer','2025-08-05 09:45:00','2025-08-05 09:45:00'),
(11,'pisang','2025-08-05 09:46:51','2025-08-05 09:46:51'),
(12,'baru','2025-08-05 09:58:59','2025-08-05 09:58:59'),
(13,'bubur','2025-08-05 10:18:55','2025-08-05 10:18:55');

/*Table structure for table `kategori_resep` */

DROP TABLE IF EXISTS `kategori_resep`;

CREATE TABLE `kategori_resep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `kategori_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategori_resep_resep_id_kategori_id_unique` (`resep_id`,`kategori_id`),
  KEY `kategori_resep_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `kategori_resep_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE CASCADE,
  CONSTRAINT `kategori_resep_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kategori_resep` */

insert  into `kategori_resep`(`id`,`resep_id`,`kategori_id`,`created_at`,`updated_at`) values 
(1,1,1,NULL,NULL);

/*Table structure for table `komentar` */

DROP TABLE IF EXISTS `komentar`;

CREATE TABLE `komentar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `komentar_resep_id_foreign` (`resep_id`),
  KEY `komentar_user_id_foreign` (`user_id`),
  CONSTRAINT `komentar_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE,
  CONSTRAINT `komentar_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `komentar` */

/*Table structure for table `langkah` */

DROP TABLE IF EXISTS `langkah`;

CREATE TABLE `langkah` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `langkah_resep_id_foreign` (`resep_id`),
  CONSTRAINT `langkah_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `langkah` */

insert  into `langkah`(`id`,`resep_id`,`deskripsi`,`urutan`,`created_at`,`updated_at`) values 
(1,1,'Panaskan wajan dan tumis telur.',1,NULL,NULL),
(2,1,'Masukkan nasi, aduk rata.',2,NULL,NULL),
(3,1,'Tambahkan kecap dan garam.',3,NULL,NULL),
(4,1,'Aduk rata dan sajikan hangat.',4,NULL,NULL);

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2023_12_12_072543_create_permission_tables',1),
(6,'2023_12_31_064553_create_settings_table',1),
(7,'2025_07_23_070435_create_resep_table',1),
(8,'2025_07_23_071054_create_bahan_table',1),
(9,'2025_07_23_071114_create_langkah_table',1),
(10,'2025_07_23_071202_create_favorit_resep_table',1),
(11,'2025_07_23_071559_create_komentar_table',1),
(12,'2025_07_23_071632_create_rating_table',1),
(13,'2025_07_23_073612_create_tag_table',1),
(14,'2025_07_23_073758_create_kategori_table',1),
(15,'2025_07_23_073943_create_alat_table',1),
(16,'2025_07_23_080024_create_kategori_resep_table',1),
(17,'2025_07_23_080107_create_resep_tag_table',1);

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_permissions` */

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_roles` */

insert  into `model_has_roles`(`role_id`,`model_type`,`model_id`) values 
(1,'App\\Models\\User','1');

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values 
(1,'dashboard','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(2,'master','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(3,'master-user','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(4,'master-role','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(5,'website','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(6,'setting','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(7,'dasboard_pengguna','api','2025-07-23 08:10:08','2025-07-23 08:10:08'),
(8,'resep','api','2025-07-23 08:10:08','2025-07-23 08:10:08');

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `rating_resep` */

DROP TABLE IF EXISTS `rating_resep`;

CREATE TABLE `rating_resep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rating_resep_user_id_resep_id_unique` (`user_id`,`resep_id`),
  KEY `rating_resep_resep_id_foreign` (`resep_id`),
  CONSTRAINT `rating_resep_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rating_resep_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rating_resep` */

/*Table structure for table `resep` */

DROP TABLE IF EXISTS `resep`;

CREATE TABLE `resep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_masak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resep_user_id_foreign` (`user_id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `resep_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `resep` */

insert  into `resep`(`id`,`user_id`,`judul`,`deskripsi`,`gambar`,`waktu_masak`,`kategori_id`,`created_at`,`updated_at`) values 
(1,1,'Nasi Goreng Sederhana','Resep nasi goreng mudah dan cepat untuk sarapan.','nasi-goreng.jpg','15 menit',1,'2025-07-23 08:10:29','2025-07-23 08:10:29');

/*Table structure for table `resep_tag` */

DROP TABLE IF EXISTS `resep_tag`;

CREATE TABLE `resep_tag` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resep_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resep_tag_resep_id_tag_id_unique` (`resep_id`,`tag_id`),
  KEY `resep_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `resep_tag_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resep_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `resep_tag` */

insert  into `resep_tag`(`id`,`resep_id`,`tag_id`,`created_at`,`updated_at`) values 
(1,1,1,NULL,NULL),
(2,1,2,NULL,NULL);

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

insert  into `role_has_permissions`(`permission_id`,`role_id`) values 
(1,1),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1);

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`full_name`,`guard_name`,`created_at`,`updated_at`) values 
(1,'admin','Administrator','api','2025-07-23 08:10:08','2025-07-23 08:10:08');

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bg_auth` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dinas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemerintah` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `settings` */

insert  into `settings`(`id`,`uuid`,`app`,`description`,`logo`,`banner`,`bg_auth`,`dinas`,`pemerintah`,`alamat`,`telepon`,`email`,`created_at`,`updated_at`) values 
(1,'2c68bdb6-3649-48f5-87a2-2a6162f53c2e','e-SAKIP DLH','Aplikasi e-SAKIP Dinas Lingkungan Hidup','/media/logo.png','/media/misc/banner.jpg','/media/misc/bg-auth.jpg','Dinas Lingkungan Hidup','Pemerintah Provinsi Jawa Timur','','','','2025-07-23 08:10:08','2025-07-23 08:10:08');

/*Table structure for table `tag` */

DROP TABLE IF EXISTS `tag`;

CREATE TABLE `tag` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_nama_unique` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tag` */

insert  into `tag`(`id`,`nama`,`created_at`,`updated_at`) values 
(1,'cepat','2025-07-23 08:10:29','2025-07-23 08:10:29'),
(2,'mudah','2025-07-23 08:10:29','2025-07-23 08:10:29');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`uuid`,`name`,`email`,`phone`,`photo`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(1,'6d41ea16-3520-4e46-9fce-867c51e3f785','Admin','admin@gmail.com','08123456789',NULL,'$2y$12$cx.SuUMSSNoM3JWnQt0egu9Xb8txovbpi2pU1Oyb88Elst.4uo4oa',NULL,'2025-07-23 08:10:08','2025-07-23 08:10:08');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
