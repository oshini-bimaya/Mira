-- MIRA Art & Craft Cafe - combined schema and data
-- Schema: SqlUpatedFromProfilePHP.sql; data: miraSql.sql.
-- Where the source files overlap, miraSql.sql data takes precedence.
-- Requires MariaDB 10.4+ for ADD COLUMN IF NOT EXISTS.
-- Image files must be copied separately; this script contains paths only.
-- Select your existing InfinityFree database in phpMyAdmin before importing.
-- This script does not drop tables or delete data.
-- Existing rows with the same primary keys are left unchanged.
SET NAMES utf8mb4;
SET @MIRA_OLD_TIME_ZONE = @@SESSION.time_zone;
SET time_zone = '+00:00';

-- 1. Parent table: users
CREATE TABLE IF NOT EXISTS `users` (
  `USER_ID` int(11) NOT NULL AUTO_INCREMENT,
  `FULL_NAME` varchar(100) NOT NULL,
  `EMAIL` varchar(250) NOT NULL,
  `PASSWORD` varchar(200) NOT NULL,
  `ROLE` enum('STUDENT','ADMIN') DEFAULT 'STUDENT',
  `ABOUT_ARTIST` text DEFAULT NULL,
  `PROFILE_PHOTO` varchar(255) DEFAULT NULL,
  `COVER_PHOTO` varchar(255) DEFAULT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add the profile fields to existing databases too (safe to rerun on MariaDB 10.4+).
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `ABOUT_ARTIST` text DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `PROFILE_PHOTO` varchar(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `COVER_PHOTO` varchar(255) DEFAULT NULL;

INSERT IGNORE INTO `users` (`USER_ID`, `FULL_NAME`, `EMAIL`, `PASSWORD`, `ROLE`, `CREATED_AT`, `ABOUT_ARTIST`, `PROFILE_PHOTO`, `COVER_PHOTO`) VALUES
	(1, 'Shenura Heshan', 'shenuraheshan125@gmail.com', '$2y$10$bqlMrtSIQsSq2j241m3uk.KQ0WzaZZnHICQEVGeMgrJIET780l/hC', 'STUDENT', '2026-10-01 17:01:10', NULL, NULL, NULL),
	(2, 'dinuja', 'dinujayathisum2005@gmail.com', '$2y$10$OBhYVukyVIIs0qtXkUifAOufZ8l/adn2t2NkAUW0.9tl9XjVPucQq', 'STUDENT', '2026-10-02 05:13:17', NULL, NULL, NULL),
	(3, 'oshini', 'oshinininf@gmail.com', '$2y$10$n9ZKMiF.wLbSpgSTHFwUo.og/Cqduy1rUtU9P4Ry1DuC2VxtiXFrq', 'STUDENT', '2026-10-02 07:43:57', NULL, NULL, NULL),
	(4, 'Heshan', 'shenuraheshan8@gmail.com', '$2y$10$/V7ZoADzLnLPi8dueYTZw.vsBmKSpu7XmPm2hkRuV1xu8k5nx0Exy', 'STUDENT', '2026-10-02 08:42:17', NULL, NULL, NULL),
	(5, 'Oshini', 'kpdobimaya@students.nsbm.ac.lk', '$2y$10$/gcbKFyRjqFE5KdKvKu3guDiixgTKSQes3mNL4yIAb0rVCrkFB.fa', 'STUDENT', '2026-10-04 12:54:13', NULL, NULL, NULL),
	(6, 'Dumindu', 'dumindu2004@gmail.com', '$2y$10$Btab4Gi4q3SAVF67WYToxO99ATUIG5/Jms3wzv0HUwNDw2ddQqwcG', 'STUDENT', '2026-10-04 14:53:30', NULL, NULL, NULL),
	(7, 'Tharuka Amarathunga', 'techtharuka2@gmail.com', '$2y$10$pC9raCAC3OTN9T9PFZikp.KVfvaA3v.3/SEZNlJt3kCcsMdwgHAkW', 'STUDENT', '2026-10-11 05:55:35', 'The Artist is a digital illustrator and concept designer specializing in vibrant fantasy landscapes and character art. Working primarily with digital painting software and stylized vectors, they blend ethereal lighting with rich, textured details to bring imaginary worlds to life. Active in the online creative community since 2021, they frequently share process videos, step-by-step tutorials, and finished gallery pieces with a global network of fellow creators. Driven by a passion for world-building, they continuously experiment with new palettes and cinematic perspectives to push the boundaries of their digital canvas.', 'uploads/profiles/user_7_79dc795af4af8f9ff127480c.jpg', 'uploads/profiles/user_7_0ca05164f8ca540f8dd7c84f.jpg'),
	(8, 'Tharuka Damisth', 'techtharuka0@gmail.com', '$2y$10$pC9raCAC3OTN9T9PFZikp.KVfvaA3v.3/SEZNlJt3kCcsMdwgHAkW', 'ADMIN', '2026-10-11 09:54:39', NULL, NULL, NULL);

-- 2. Parent table: categories
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `categories` (`category_id`, `category_name`, `description`) VALUES
	(1, 'DigitalArt', 'Nice');

-- 3. artworks depends on users and categories
CREATE TABLE IF NOT EXISTS `artworks` (
  `artwork_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(100) DEFAULT 'pending',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`artwork_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`),
  CONSTRAINT `artworks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `artworks` (`artwork_id`, `user_id`, `category_id`, `title`, `description`, `status`, `is_featured`, `created_at`, `rejection_reason`) VALUES
	(1, 7, 1, 'One Piece', 'Very nice', 'pending', 0, '2026-10-11 07:58:05', NULL);

-- 4. artwork_images depends on artworks
CREATE TABLE IF NOT EXISTS `artwork_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `artwork_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `artwork_id` (`artwork_id`),
  CONSTRAINT `artwork_images_ibfk_1` FOREIGN KEY (`artwork_id`)
    REFERENCES `artworks` (`artwork_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `artwork_images` (`image_id`, `artwork_id`, `image_path`) VALUES
	(1, 1, 'uploads/artworks/af778e304e8c9316bbb036f35f9c06dc41da40ee.jpg');

-- 5. artwork_likes depends on artworks and users
CREATE TABLE IF NOT EXISTS `artwork_likes` (
  `like_id` int(11) NOT NULL AUTO_INCREMENT,
  `artwork_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`like_id`),
  UNIQUE KEY `artwork_id` (`artwork_id`, `user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `artwork_likes_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`),
  CONSTRAINT `artwork_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET time_zone = @MIRA_OLD_TIME_ZONE;
