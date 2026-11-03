-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql300.infinityfree.com
-- Generation Time: Nov 02, 2026 at 11:26 PM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43083347_mira_artcraft`
--

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `artwork_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(100) DEFAULT 'pending',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rejection_reason` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `featured_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`artwork_id`, `user_id`, `category_id`, `title`, `description`, `status`, `is_featured`, `created_at`, `rejection_reason`, `reviewed_at`, `reviewed_by`, `featured_order`) VALUES
(2, 6, 3, 'NSBM BEST EVENING', 'Most Beautiful Evenings At NSBM', 'approved', 1, '2026-10-12 13:30:09', NULL, '2026-10-12 13:58:44', 8, NULL),
(3, 9, 3, 'NSBM Medical Faculty', 'NSBM New Faculty', 'approved', 0, '2026-10-12 14:56:54', NULL, '2026-10-12 15:13:45', 8, NULL),
(4, 14, 3, 'NSBM Pirith Seramany', 'NSBM Pirith Seramany', 'approved', 0, '2026-10-27 08:43:57', NULL, '2026-10-27 08:49:55', 8, NULL),
(5, 16, 5, 'Morning Nature', 'Photo in a forest', 'approved', 1, '2026-10-28 15:03:00', NULL, '2026-10-30 06:18:26', 8, NULL),
(6, 19, 5, 'Neon Dreams', 'A futuristic city illuminated by colorful neon lights and modern architecture.', 'approved', 0, '2026-10-30 06:58:48', NULL, '2026-10-30 07:06:43', 18, NULL),
(7, 19, 5, 'Enchanted Forest', 'A magical forest filled with glowing trees, mysterious lights, and dreamlike colors.', 'approved', 1, '2026-10-30 07:00:39', NULL, '2026-10-30 07:06:39', 18, NULL),
(8, 19, 5, 'Beyond the Stars', 'A dreamy digital illustration exploring the beauty and mystery of the universe.', 'approved', 0, '2026-10-30 07:02:26', NULL, '2026-10-30 07:06:35', 18, NULL),
(9, 19, 5, 'Blooming Soul', 'A colourful portrait symbolizing personal growth, imagination, and inner beauty.', 'approved', 0, '2026-10-30 07:02:59', NULL, '2026-10-30 07:06:32', 18, NULL),
(10, 19, 5, 'Floating Paradise', 'An imaginative landscape featuring floating islands, waterfalls, and peaceful skies.', 'approved', 0, '2026-10-30 07:03:31', NULL, '2026-10-30 07:06:24', 18, NULL),
(11, 20, 2, 'Silent Emotions', 'A detailed pencil portrait capturing human emotions through realistic shading.', 'approved', 0, '2026-10-30 07:11:36', NULL, '2026-10-30 07:15:58', 18, NULL),
(12, 20, 2, 'Window to the Soul', 'A realistic eye sketch highlighting expression, reflections, and fine details.', 'approved', 0, '2026-10-30 07:12:07', NULL, '2026-10-30 07:15:54', 18, NULL),
(13, 20, 2, 'Forgotten Memories', 'A peaceful countryside sketch reflecting nostalgia and traditional village life.', 'approved', 0, '2026-10-30 07:12:28', NULL, '2026-10-30 07:15:52', 18, NULL),
(14, 20, 2, 'The Human Connection', 'A meaningful drawing of two hands representing friendship, support, and trust.', 'approved', 0, '2026-10-30 07:12:49', NULL, '2026-10-30 07:15:48', 18, NULL),
(15, 20, 2, 'Wild Spirit', 'A detailed wildlife sketch capturing the strength and beauty of a lion.', 'approved', 0, '2026-10-30 07:13:17', NULL, '2026-10-30 07:15:45', 18, NULL),
(16, 17, 3, 'Misty Mountains', 'A peaceful landscape photograph capturing mist-covered mountains and greenery.', 'approved', 0, '2026-10-30 07:31:12', NULL, '2026-10-30 07:39:36', 18, NULL),
(17, 17, 3, 'Chasing Sunsets', 'A golden-hour photograph capturing the warm beauty of a tropical beach.', 'approved', 0, '2026-10-30 07:31:39', NULL, '2026-10-30 07:39:33', 18, NULL),
(18, 17, 3, 'Wings of Freedom', 'A close-up photograph highlighting the delicate beauty of a butterfly.', 'approved', 0, '2026-10-30 07:32:07', NULL, '2026-10-30 07:39:26', 18, NULL),
(19, 17, 3, 'City After Dark', 'An atmospheric street photograph showcasing colorful lights and urban life.', 'approved', 0, '2026-10-30 07:33:07', NULL, '2026-10-30 07:39:23', 18, NULL),
(20, 17, 3, 'Morning Magic', 'A detailed nature photograph capturing tiny water droplets in morning sunlight.', 'approved', 0, '2026-10-30 08:08:22', NULL, '2026-10-30 08:08:30', 18, NULL),
(21, 21, 4, 'Forever Bloom', 'A beautiful handmade crochet flower bouquet created with colourful yarn.', 'approved', 0, '2026-10-30 08:12:49', NULL, '2026-10-31 05:49:27', 18, NULL),
(22, 21, 4, 'Woven Harmony', 'A decorative macramé wall hanging made using traditional knotting techniques.', 'approved', 0, '2026-10-30 08:13:42', NULL, '2026-10-31 05:49:33', 18, NULL),
(23, 21, 4, 'Paper Blossoms', 'A creative floral artwork made from rolled and shaped colourful paper strips.', 'approved', 0, '2026-10-30 08:14:30', NULL, '2026-10-31 05:49:14', 18, NULL),
(24, 21, 4, 'Earth & Elegance', 'A handcrafted clay vase combining traditional pottery and modern design.', 'approved', 0, '2026-10-30 08:15:02', NULL, '2026-10-31 05:49:19', 18, NULL),
(25, 21, 4, 'Beads of Joy', 'A collection of handmade accessories created using colorful beads and patterns.', 'approved', 0, '2026-10-30 08:15:45', NULL, '2026-10-31 05:49:02', 18, NULL),
(26, 16, 5, 'A women looking out side from a balcony', 'A women enjoying the nature from her balcony', 'approved', 0, '2026-10-31 05:00:15', NULL, '2026-10-31 05:49:08', 18, NULL),
(27, 24, 4, 'Blooming Creativity', 'A beautiful handmade floral artwork created using colourful paper and creative techniques.', 'approved', 0, '2026-10-31 05:37:33', NULL, '2026-10-31 05:48:45', 18, NULL),
(28, 24, 6, 'The Human Essence', 'An expressive clay sculpture representing human emotions through creative shapes and textures.', 'approved', 0, '2026-10-31 05:38:51', NULL, '2026-10-31 05:48:50', 18, NULL),
(29, 24, 1, 'Sunset Dreams', 'A colourful landscape painting capturing the peaceful beauty of a sunset.', 'approved', 0, '2026-10-31 05:40:47', NULL, '2026-10-31 05:48:40', 18, NULL),
(30, 24, 3, 'The Sacred Journey', 'A majestic cultural procession showcasing Sri Lankan traditions, featuring a beautifully decorated elephant, traditional drummers, and vibrant ceremonial lights.', 'approved', 0, '2026-10-31 05:43:22', NULL, '2026-10-31 05:48:36', 18, NULL),
(31, 26, 2, 'Rain in Paris', 'A lonely silhouette with an umbrella walks toward the Eiffel Tower on a rainy, charcoal-sketched evening', 'approved', 0, '2026-10-31 09:08:37', NULL, '2026-10-31 09:17:09', 18, NULL),
(32, 26, 4, 'Heritage in Wood', 'Hand-carved walnut wood boxes and bowls showcasing intricate traditional craftsmanship.', 'approved', 0, '2026-10-31 09:11:02', NULL, '2026-10-31 09:17:01', 18, NULL),
(33, 26, 2, 'Noble Spirit', 'A textured oil painting capturing the quiet strength and grace of a white horse in warm light.', 'approved', 0, '2026-10-31 09:15:00', NULL, '2026-10-31 09:16:58', 18, NULL),
(34, 26, 5, 'The Starlit Oracle', 'A celestial guardian gazing over floating islands in a magical twilight realm.', 'approved', 0, '2026-10-31 09:16:22', NULL, '2026-10-31 09:16:54', 18, NULL),
(35, 26, 6, 'Paola Grizi', 'Paola Grizi Adds New Meaning to ‘Literary Figures’ in Emotive Bronze Sculptures', 'approved', 0, '2026-10-31 09:28:52', NULL, '2026-10-31 09:30:35', 18, NULL),
(36, 26, 1, 'Girl With Rose', 'A girl with red rose drawn bn water colours', 'approved', 0, '2026-10-31 09:29:41', NULL, '2026-10-31 09:30:31', 18, NULL),
(37, 20, 3, 'Sigiriya', 'Sigiriya in an evening', 'approved', 0, '2026-11-01 09:31:15', NULL, '2026-11-01 15:42:09', 18, NULL),
(38, 20, 1, 'Golden Sunset – Elephants in the Wild', 'This beautiful artwork captures the peaceful beauty of an African sunset, featuring elephants walking across the savanna. The warm golden and orange sky, combined with dark silhouettes of elephants and trees, creates a calm and magical atmosphere. The painting symbolizes family, unity, freedom, and the beauty of wildlife, highlighting the harmony between nature and animals.', 'approved', 0, '2026-11-01 13:35:33', NULL, '2026-11-01 15:42:05', 18, NULL),
(39, 16, 1, 'Michael Angelo\'s painting', 'Michael Angelo\'s painting on top of the Palace of Versailles', 'approved', 0, '2026-11-02 06:15:55', NULL, '2026-11-02 06:39:42', 18, NULL),
(40, 16, 1, 'A Girl with Flowers', 'on the Grass. Creator: Maris. Creation Date: 1878. Institution: Rijksmuseum. Provider: Rijksmuseum. Providing Country: Netherlands. PD for Public Domain Mark 2', 'approved', 0, '2026-11-02 06:31:14', NULL, '2026-11-02 06:39:37', 18, NULL),
(41, 16, 1, 'Title: The Cannon Shot.', 'Title: The Cannon Shot. Date: 1680. Institution: Rijksmuseum. Provider: Rijksmuseum. Providing Country: Netherlands. Public Domain', 'approved', 0, '2026-11-02 06:32:27', NULL, '2026-11-02 06:39:32', 18, NULL),
(42, 16, 1, 'Portrait of a Woman', 'Portrait of a Woman, 1638-39 by Guido Reni. The subject may represent Artemisia II of Caria (d.350 BC) wife of Mausolus, the governor of Caria in Asia Minor. After the death of her husband, she mixed his ashes in liquid which she drank, making herself a living tomb. The story was used as a symbol of a widow\'s devotion to her husband\'s memory.', 'approved', 0, '2026-11-02 06:34:16', NULL, '2026-11-02 06:39:27', 18, NULL),
(43, 16, 1, 'Who killed cock robin?', 'Who killed cock robin?', 'approved', 0, '2026-11-02 06:36:13', NULL, '2026-11-02 06:39:22', 18, NULL),
(44, 30, 2, 'Together on the Edge to the World', 'A peaceful pencil sketch of two souls sitting together on a cliff under a lonely tree, watching the sunset over a quiet valley. A simple moment that shows love, calmness, and the beauty of being together away from the world.', 'approved', 0, '2026-11-02 14:51:47', NULL, '2026-11-03 03:27:14', 8, NULL),
(45, 30, 6, 'The Quiet Thinker', 'A minimalist sculpture of a faceless figure sitting cross-legged, lost in thought while reading a book. It represents calmness, deep thinking, and the peaceful world of books and imagination.', 'approved', 0, '2026-11-02 14:54:09', NULL, '2026-11-03 03:27:11', 8, NULL),
(46, 30, 1, 'Grace of Tradition', 'A beautiful painting of a woman in a white saree performing a traditional ritual with elegance and devotion. The soft colors and flowing fabric capture the timeless grace, purity, and cultural beauty of womanhood.', 'approved', 0, '2026-11-02 14:56:38', NULL, '2026-11-03 03:27:05', 8, NULL),
(47, 30, 1, 'A Walk Under the Starry Night', 'Inspired by Van Gogh’s style, this artwork shows a winding path through golden fields under a swirling night sky. A tall dark cypress tree stands beside the road as two figures walk quietly, capturing a feeling of peaceful journey, loneliness, and the beauty of a dreamlike night.', 'approved', 0, '2026-11-02 14:59:20', NULL, '2026-11-03 03:27:00', 8, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `artwork_images`
--

CREATE TABLE `artwork_images` (
  `image_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artwork_images`
--

INSERT INTO `artwork_images` (`image_id`, `artwork_id`, `image_path`, `uploaded_at`) VALUES
(1, 2, 'uploads/artworks/5382e3048f6dfe595bce969a26a1cf2fbe5fe02f.jpg', '2026-10-12 13:30:09'),
(2, 3, 'uploads/artworks/0557f7e6349ce6d836704795d8ad7e285a052c6a.jpg', '2026-10-12 14:56:54'),
(3, 4, 'uploads/artworks/1fcd8c32c9a055f9fd56c78ef6d6ef4d3c8e029c.jpg', '2026-10-27 08:43:57'),
(4, 5, 'uploads/artworks/f000b8b4ae084c13acd5e97fec092e13932d9ec8.jpg', '2026-10-28 15:03:00'),
(5, 6, 'uploads/artworks/f9c93d8d26364a22a9a3e2d60f1e8dc8d273b749.png', '2026-10-30 06:58:48'),
(6, 7, 'uploads/artworks/9417baa795ae1c630011de5b4737a261b687342c.png', '2026-10-30 07:00:39'),
(7, 8, 'uploads/artworks/c735a6befc24a4b102e8a74a5e9ba93343b54b80.png', '2026-10-30 07:02:26'),
(8, 9, 'uploads/artworks/1e36ea7bd38182fa19b270abce924bf619f71d23.png', '2026-10-30 07:02:59'),
(9, 10, 'uploads/artworks/53f196576738bec5c3cb2beee8fc51d6d68bc11e.png', '2026-10-30 07:03:31'),
(10, 11, 'uploads/artworks/66b6ba83a517ec8791f0132ea1a3575cc645bdc0.png', '2026-10-30 07:11:36'),
(11, 12, 'uploads/artworks/3c78b46e4487f9503b003f37b40bcd8c94a164d6.png', '2026-10-30 07:12:07'),
(12, 13, 'uploads/artworks/58ab72d47d02dcc800cb7d8d1e610a296c393bff.png', '2026-10-30 07:12:28'),
(13, 14, 'uploads/artworks/efb614f8cbd172c40e88974a182640273cb2c779.png', '2026-10-30 07:12:49'),
(14, 15, 'uploads/artworks/7f873d21e45179389b80a439e979c9c30cfefe0f.png', '2026-10-30 07:13:17'),
(15, 16, 'uploads/artworks/eba9d9401c5b2af312a3e4d30bf7381c0928d96b.png', '2026-10-30 07:31:12'),
(16, 17, 'uploads/artworks/3bd4fc8db0cb9c9445b28bdc49cab3e3b3f7de05.png', '2026-10-30 07:31:39'),
(17, 18, 'uploads/artworks/48852b8a653e652b1271a2a3f3a0afc51cb78807.png', '2026-10-30 07:32:07'),
(18, 19, 'uploads/artworks/8923b9e654aaea121ff22efe8195ff8100a1f819.png', '2026-10-30 07:33:07'),
(19, 20, 'uploads/artworks/8056d5702de361213ed0678a64e81da431daef93.png', '2026-10-30 08:08:22'),
(20, 21, 'uploads/artworks/3cbe1d25ab0917a106c1e6fdca0c6810cacdb133.png', '2026-10-30 08:12:49'),
(21, 22, 'uploads/artworks/005f7fcd00d88f7597807af47cb5773e9eb9464c.png', '2026-10-30 08:13:42'),
(22, 23, 'uploads/artworks/7964cd49986c1eefe7a59433d2a2d2949ccd227f.png', '2026-10-30 08:14:30'),
(23, 24, 'uploads/artworks/2c4431227eedb643e153c15f5c30eb9daad8b681.png', '2026-10-30 08:15:02'),
(24, 25, 'uploads/artworks/e9467df5e9bcc3bfe692b8c08b02eb7b16cca435.png', '2026-10-30 08:15:45'),
(25, 26, 'uploads/artworks/3e0dc1fec5509bff6638b65c7817f8f8bc25a014.jpg', '2026-10-31 05:00:15'),
(26, 27, 'uploads/artworks/6df2f7169a6e87976c8c44fd3d9af7912e91ba9a.jpg', '2026-10-31 05:37:33'),
(27, 28, 'uploads/artworks/b7a3c49ac4c15f5dee2b209c5fe719f9aa233094.jpg', '2026-10-31 05:38:51'),
(28, 29, 'uploads/artworks/fcf50066a631845972f0b42c5d2099b4a24e5e3a.jpg', '2026-10-31 05:40:47'),
(29, 30, 'uploads/artworks/69bbd9d33b5201debd895b6b93861d2441cdf248.jpg', '2026-10-31 05:43:22'),
(30, 31, 'uploads/artworks/e946a670d42108833f0c05cac1a47549856cd4ff.jpg', '2026-10-31 09:08:37'),
(31, 32, 'uploads/artworks/c3fcd53a64b5995e76ac35782d49b7a14b90bdbc.jpg', '2026-10-31 09:11:02'),
(32, 33, 'uploads/artworks/bdf1ef31706b82e4ca64543117da5dc8d27654ee.jpg', '2026-10-31 09:15:00'),
(33, 34, 'uploads/artworks/13f7d5a728ad05131b48ec8ec2362779f06f9ea0.jpg', '2026-10-31 09:16:22'),
(34, 35, 'uploads/artworks/46e58b4f7ab6f2bec1b068660c13fd7e6edfc3df.jpg', '2026-10-31 09:28:52'),
(35, 36, 'uploads/artworks/cdee4b1bf0e74511260b9b56e21737c564a0597f.jpg', '2026-10-31 09:29:41'),
(36, 37, 'uploads/artworks/e5471b5e7641b518cc356a826378193e029e49f0.webp', '2026-11-01 09:31:15'),
(37, 38, 'uploads/artworks/2a73c77d09546dd80f894d01a35e26203c6b8f09.webp', '2026-11-01 13:35:33'),
(38, 39, 'uploads/artworks/153913064aa78180fb8d0c7d2b691124ebdcad88.jpg', '2026-11-02 06:15:55'),
(39, 40, 'uploads/artworks/bb07de443d1d870029f423f1f2985aca269810ca.jpg', '2026-11-02 06:31:14'),
(40, 41, 'uploads/artworks/cc7f54a26c2c9d6abacfe44222a184f00cf53457.jpg', '2026-11-02 06:32:27'),
(41, 42, 'uploads/artworks/ed2b742cd4dc45f12435caeb09d5f4f5c09f57a2.jpg', '2026-11-02 06:34:16'),
(42, 43, 'uploads/artworks/ca830582a7a30f93c354829252b16d4b8ed16a68.jpg', '2026-11-02 06:36:13'),
(43, 44, 'uploads/artworks/1e74c52933a5fd703d6cd542b7033643ff638b6a.jpg', '2026-11-02 14:51:47'),
(44, 45, 'uploads/artworks/25158fabb540cd82ee4b2b04750d00471e7d6b9a.jpg', '2026-11-02 14:54:09'),
(45, 46, 'uploads/artworks/18ff6f16e5711253e075f9bd2f4fae3f3a117665.jpg', '2026-11-02 14:56:38'),
(46, 47, 'uploads/artworks/1d3cbedf48d2a63c3f9afd5b5c48c4a3be0d542a.jpg', '2026-11-02 14:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `artwork_likes`
--

CREATE TABLE `artwork_likes` (
  `like_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artwork_likes`
--

INSERT INTO `artwork_likes` (`like_id`, `artwork_id`, `user_id`, `created_at`) VALUES
(1, 2, 14, '2026-10-27 07:54:31'),
(2, 2, 16, '2026-10-28 14:45:42'),
(3, 2, 17, '2026-10-30 07:22:51'),
(4, 7, 17, '2026-10-30 07:23:41'),
(6, 12, 17, '2026-10-30 07:25:26'),
(7, 5, 17, '2026-10-30 07:26:33'),
(8, 15, 17, '2026-10-30 07:27:00'),
(9, 27, 27, '2026-10-31 09:22:22'),
(10, 34, 27, '2026-10-31 09:22:43'),
(11, 33, 29, '2026-11-01 09:36:11'),
(12, 33, 20, '2026-11-01 13:43:02'),
(13, 34, 30, '2026-11-02 05:09:58'),
(14, 11, 30, '2026-11-02 05:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `is_active`, `image_path`) VALUES
(1, 'Painting', 'Paintings created by students', 1, NULL),
(2, 'Drawing & Sketching', 'Pencil, charcoal and ink drawings', 1, NULL),
(3, 'Photography', 'Creative photography by students', 1, NULL),
(4, 'Handcrafts', 'Handmade creative artwork', 1, NULL),
(5, 'Digital Art', 'Digital illustrations and designs', 1, NULL),
(6, 'Sculpture', 'Three-dimensional artistic creations', 1, NULL),
(7, 'Test Art', 'created for testing', 1, NULL),
(8, 'Nature Art', 'Beautiful artworks inspired by nature, including landscapes, wildlife, forests, mountains, and sunsets.', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `artwork_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 2, 17, 'Great work !', '2026-10-30 07:23:09'),
(2, 7, 17, 'WOW ! Great work.', '2026-10-30 07:23:38'),
(3, 12, 17, 'Nice work !', '2026-10-30 07:25:38'),
(4, 5, 17, 'Nice', '2026-10-30 07:26:39'),
(5, 15, 17, 'Great !', '2026-10-30 07:27:14'),
(6, 34, 27, 'Wow', '2026-10-31 09:22:57'),
(7, 5, 16, 'Peacefull', '2026-11-02 06:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(500) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` varchar(500) DEFAULT NULL,
  `venue` varchar(250) DEFAULT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `registration_deadline` datetime DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 100,
  `poster_path` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `description`, `event_date`, `location`, `created_at`, `title`, `venue`, `starts_at`, `ends_at`, `registration_deadline`, `capacity`, `poster_path`, `status`, `created_by`) VALUES
(1, '', 'A celebration of creativity showcasing student artworks and handicrafts.', '0000-00-00 00:00:00', NULL, '2026-11-02 14:30:08', 'NSBM MIRA Arts & Crafts Exhibition', 'NSBM Green University Premises', '2026-11-22 09:00:00', '2026-11-25 16:00:00', '2026-11-20 12:50:00', 100, 'uploads/events/4cb5013866d626996ba1c63c3057b4bf.jpg', 'published', 8),
(2, '', 'A modern exhibition celebrating digital creativity through graphic design, digital paintings, 3D artwork, animations, and innovative student projects.', '0000-00-00 00:00:00', NULL, '2026-11-02 14:39:24', 'Creative Minds – Digital Art Showcase', 'NSBM Green University – Main Exhibition Area', '2027-01-15 09:00:00', '2027-01-17 17:00:00', '2027-01-10 20:00:00', 60, 'uploads/events/b4e38dcc2bf1850ec8c786bae55277d3.png', 'published', 18);

-- --------------------------------------------------------

--
-- Table structure for table `event_artworks`
--

CREATE TABLE `event_artworks` (
  `event_artwork_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `registration_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) NOT NULL DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`registration_id`, `event_id`, `user_id`, `registered_at`, `status`) VALUES
(1, 1, 24, '2026-11-02 14:33:51', 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`follow_id`, `follower_id`, `following_id`, `created_at`) VALUES
(1, 11, 12, '2026-10-21 05:52:53'),
(2, 19, 12, '2026-10-30 07:04:58'),
(3, 19, 14, '2026-10-30 07:05:12'),
(4, 19, 16, '2026-10-30 07:05:21'),
(5, 19, 17, '2026-10-30 07:05:30'),
(6, 20, 12, '2026-10-30 07:13:31'),
(7, 17, 19, '2026-10-30 07:23:46'),
(8, 17, 20, '2026-10-30 07:25:03'),
(9, 27, 24, '2026-10-31 09:20:45'),
(10, 27, 26, '2026-10-31 09:20:58'),
(11, 27, 21, '2026-10-31 09:21:27'),
(12, 24, 26, '2026-10-31 09:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `type` varchar(50) NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL DEFAULT 'Notification',
  `link_path` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`, `type`, `title`, `link_path`) VALUES
(1, 14, 'Your artwork \"NSBM Pirith Seramany\" was approved and is now visible in the gallery.', 1, '2026-10-27 08:49:55', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(2, 16, 'Your artwork \"Morning Nature\" was approved and is now visible in the gallery.', 0, '2026-10-30 06:18:26', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(3, 19, 'Your artwork \"Floating Paradise\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:06:24', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(4, 19, 'Your artwork \"Blooming Soul\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:06:32', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(5, 19, 'Your artwork \"Beyond the Stars\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:06:35', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(6, 19, 'Your artwork \"Enchanted Forest\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:06:39', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(7, 19, 'Your artwork \"Neon Dreams\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:06:43', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(8, 20, 'Your artwork \"Wild Spirit\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:15:45', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(9, 20, 'Your artwork \"The Human Connection\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:15:48', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(10, 20, 'Your artwork \"Forgotten Memories\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:15:52', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(11, 20, 'Your artwork \"Window to the Soul\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:15:54', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(12, 20, 'Your artwork \"Silent Emotions\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:15:58', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(13, 17, 'Your artwork \"City After Dark\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:39:23', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(14, 17, 'Your artwork \"Wings of Freedom\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:39:26', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(15, 17, 'Your artwork \"Chasing Sunsets\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:39:33', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(16, 17, 'Your artwork \"Misty Mountains\" was approved and is now visible in the gallery.', 0, '2026-10-30 07:39:36', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(17, 17, 'Your artwork \"Morning Magic\" was approved and is now visible in the gallery.', 0, '2026-10-30 08:08:30', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(18, 24, 'Your artwork \"The Sacred Journey\" was approved and is now visible in the gallery.', 1, '2026-10-31 05:48:36', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(19, 24, 'Your artwork \"Sunset Dreams\" was approved and is now visible in the gallery.', 1, '2026-10-31 05:48:40', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(20, 24, 'Your artwork \"Blooming Creativity\" was approved and is now visible in the gallery.', 1, '2026-10-31 05:48:45', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(21, 24, 'Your artwork \"The Human Essence\" was approved and is now visible in the gallery.', 1, '2026-10-31 05:48:50', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(22, 21, 'Your artwork \"Beads of Joy\" was approved and is now visible in the gallery.', 0, '2026-10-31 05:49:02', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(23, 16, 'Your artwork \"A women looking out side from a balcony\" was approved and is now visible in the gallery.', 0, '2026-10-31 05:49:08', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(24, 21, 'Your artwork \"Paper Blossoms\" was approved and is now visible in the gallery.', 0, '2026-10-31 05:49:14', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(25, 21, 'Your artwork \"Earth & Elegance\" was approved and is now visible in the gallery.', 0, '2026-10-31 05:49:19', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(26, 21, 'Your artwork \"Forever Bloom\" was approved and is now visible in the gallery.', 0, '2026-10-31 05:49:27', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(27, 21, 'Your artwork \"Woven Harmony\" was approved and is now visible in the gallery.', 0, '2026-10-31 05:49:33', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(28, 26, 'Your artwork \"The Starlit Oracle\" was approved and is now visible in the gallery.', 1, '2026-10-31 09:16:54', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(29, 26, 'Your artwork \"Noble Spirit\" was approved and is now visible in the gallery.', 1, '2026-10-31 09:16:58', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(30, 26, 'Your artwork \"Heritage in Wood\" was approved and is now visible in the gallery.', 1, '2026-10-31 09:17:01', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(31, 26, 'Your artwork \"Rain in Paris\" was approved and is now visible in the gallery.', 1, '2026-10-31 09:17:09', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(32, 26, 'Your artwork \"Girl With Rose\" was approved and is now visible in the gallery.', 0, '2026-10-31 09:30:31', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(33, 26, 'Your artwork \"Paola Grizi\" was approved and is now visible in the gallery.', 0, '2026-10-31 09:30:35', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(34, 20, 'Your artwork \"Golden Sunset – Elephants in the Wild\" was approved and is now visible in the gallery.', 0, '2026-11-01 15:42:05', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(35, 20, 'Your artwork \"Sigiriya\" was approved and is now visible in the gallery.', 0, '2026-11-01 15:42:09', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(36, 16, 'Your artwork \"Who killed cock robin?\" was approved and is now visible in the gallery.', 0, '2026-11-02 06:39:22', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(37, 16, 'Your artwork \"Portrait of a Woman\" was approved and is now visible in the gallery.', 0, '2026-11-02 06:39:27', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(38, 16, 'Your artwork \"Title: The Cannon Shot.\" was approved and is now visible in the gallery.', 0, '2026-11-02 06:39:32', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(39, 16, 'Your artwork \"A Girl with Flowers\" was approved and is now visible in the gallery.', 0, '2026-11-02 06:39:37', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(40, 16, 'Your artwork \"Michael Angelo\'s painting\" was approved and is now visible in the gallery.', 0, '2026-11-02 06:39:42', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(41, 30, 'Your artwork \"A Walk Under the Starry Night\" was approved and is now visible in the gallery.', 0, '2026-11-03 03:27:00', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(42, 30, 'Your artwork \"Grace of Tradition\" was approved and is now visible in the gallery.', 0, '2026-11-03 03:27:05', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(43, 30, 'Your artwork \"The Quiet Thinker\" was approved and is now visible in the gallery.', 0, '2026-11-03 03:27:11', 'artwork_approved', 'Artwork approved', 'my-artworks.php'),
(44, 30, 'Your artwork \"Together on the Edge to the World\" was approved and is now visible in the gallery.', 0, '2026-11-03 03:27:14', 'artwork_approved', 'Artwork approved', 'my-artworks.php');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating_value` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `artwork_id`, `user_id`, `rating_value`, `created_at`) VALUES
(1, 2, 16, 4, '2026-10-28 14:45:37'),
(2, 2, 17, 5, '2026-10-29 13:40:12'),
(3, 15, 17, 4, '2026-10-30 07:27:03'),
(4, 27, 27, 5, '2026-10-31 09:22:29');

-- --------------------------------------------------------

--
-- Table structure for table `saved_artworks`
--

CREATE TABLE `saved_artworks` (
  `user_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `saved_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saved_artworks`
--

INSERT INTO `saved_artworks` (`user_id`, `artwork_id`, `saved_at`) VALUES
(14, 2, '2026-10-27 07:54:36'),
(30, 11, '2026-11-02 05:10:30'),
(30, 34, '2026-11-02 05:10:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `USER_ID` int(11) NOT NULL,
  `FULL_NAME` varchar(100) NOT NULL,
  `EMAIL` varchar(250) NOT NULL,
  `PASSWORD` varchar(200) NOT NULL,
  `ROLE` enum('STUDENT','ARTIST','ADMIN') DEFAULT 'STUDENT',
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `ABOUT_ARTIST` text DEFAULT NULL,
  `PROFILE_PHOTO` varchar(255) DEFAULT NULL,
  `COVER_PHOTO` varchar(255) DEFAULT NULL,
  `account_status` varchar(20) NOT NULL DEFAULT 'active',
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`USER_ID`, `FULL_NAME`, `EMAIL`, `PASSWORD`, `ROLE`, `CREATED_AT`, `ABOUT_ARTIST`, `PROFILE_PHOTO`, `COVER_PHOTO`, `account_status`, `profile_image`) VALUES
(2, 'dinuja', 'dinujayathisum2005@gmail.com', '$2y$10$OBhYVukyVIIs0qtXkUifAOufZ8l/adn2t2NkAUW0.9tl9XjVPucQq', 'STUDENT', '2026-10-02 05:13:17', NULL, NULL, NULL, 'active', NULL),
(3, 'oshini', 'oshinininf@gmail.com', '$2y$10$n9ZKMiF.wLbSpgSTHFwUo.og/Cqduy1rUtU9P4Ry1DuC2VxtiXFrq', 'STUDENT', '2026-10-02 07:43:57', NULL, NULL, NULL, 'active', NULL),
(4, 'Heshan', 'shenuraheshan8@gmail.com', '$2y$10$/V7ZoADzLnLPi8dueYTZw.vsBmKSpu7XmPm2hkRuV1xu8k5nx0Exy', 'STUDENT', '2026-10-02 08:42:17', NULL, NULL, NULL, 'active', NULL),
(5, 'chamara', 'chamara123@gmail.com', '$2y$10$h594WHkVAApLP3KwFmL9WOhKP2svOhlFoDyx0EJHqBt3UfVJIDuj.', 'STUDENT', '2026-10-10 03:50:04', NULL, NULL, NULL, 'active', NULL),
(6, 'Malidu', 'maliduheshan@gmail.com', '$2y$10$5561tS799PQZg3bI2hQVKu5YFLXjujzT.3CHGRJrFhEzCPCoSWXMG', 'STUDENT', '2026-10-12 13:10:59', NULL, NULL, NULL, 'active', NULL),
(8, 'Shenura Heshan', 'shenuraheshan125@gmail.com', '$2y$10$RIm.SmQQkA0FC8vjBGE/s.iiY7c.laAtdc8/cloHY17d/.BUu2jdK', 'ADMIN', '2026-10-12 13:51:53', NULL, NULL, NULL, 'active', NULL),
(9, 'Disadi', 'disadi123@gmail.com', '$2y$10$ZgKhNBUd04AWDxPmLo702OhCUwKVl1Hbx33.SZk8Og16lyidTnFCC', 'STUDENT', '2026-10-12 14:07:50', 'I Love Creating Art Works And I Like To Captured A Beautiful Photos.', 'uploads/profiles/user_9_d1349ddd85746305bfa67668.jpg', 'uploads/profiles/user_9_f67518f5330bb565ee3b9924.jpg', 'active', NULL),
(10, 'namodya', 'namodya@gmail.com', '$2y$12$TZSVlVLn5A2SqeAMNRahVOw.QE8fLhJMSX/Zi088hkQsHUBbJMoVW', 'STUDENT', '2026-10-19 03:38:54', NULL, NULL, NULL, 'active', NULL),
(11, 'Thisum Abeysinghe', 'dinujayathisum1@gmail.com', '$2y$12$UFqtd.oKtI3kJVwnqPWGJ.JoyyXecJ7iF/HoB9bWXukAD5/MV8PFS', 'STUDENT', '2026-10-21 05:49:57', NULL, NULL, NULL, 'active', 'uploads/profiles/601359fa83bbbc01d2c7eabc6047fcb2.jpg'),
(12, 'Oshini Bimaya', 'okarunanayaka05@gmail.com', '$2y$12$60UxXbn2orCj2RpFQET2xOL9BjThXACWHS4H6FEWuvFQAjvSmt.ia', 'ARTIST', '2026-10-21 05:50:36', NULL, NULL, NULL, 'active', NULL),
(13, 'Keshani Weerasinghe', 'krweerasinghe821@gmail.com', '$2y$12$GHGho3Lt/3UPRs61xdCKBepsy7xjHcBJv2z6BFz093xSEIGcEnI1W', 'STUDENT', '2026-10-21 08:41:51', NULL, NULL, NULL, 'active', NULL),
(14, 'Shashimal Pleviyan', 'shashimalpleviyan@gmail.com', '$2y$12$IhMFxZdsiWipNvisfG8KKuQ5ZAMi/Ox0Asik8zIMlLbo3a21dEijO', 'ARTIST', '2026-10-26 05:59:30', NULL, NULL, NULL, 'active', NULL),
(15, 'Dumindu Sandeepa Jayakody', 'dumindu1122@gmail.com', '$2y$12$4H7Wh.edRMpqQHcQOr8iP.NSEJdyOy/JzuMD.Fxsu06hb3lKLNZPC', 'STUDENT', '2026-10-28 14:16:30', NULL, NULL, NULL, 'active', NULL),
(16, 'Td2Hydro', 'techtharuka2@gmail.com', '$2y$12$dzt5pBEeGBsHCHBYMyIcu.3ZLaOEu0jn2blfrKz3h5Foo9bLSIgCm', 'ARTIST', '2026-10-28 14:44:26', NULL, NULL, NULL, 'active', 'uploads/profiles/c12faae07bf1a0dc83ca586fb795f129.png'),
(17, 'inoch methaniya', 'methaniyainoch@gmail.com', '$2y$12$KOear1XIa8q3yrwUp8Xhcu881HfuLFKxVgwWOB0zTUzBkjvGI/oTC', 'ARTIST', '2026-10-29 13:38:36', NULL, NULL, NULL, 'active', NULL),
(18, 'Oshini Bimaya', 'kpdobimaya@students.nsbm.ac.lk', '$2y$12$DR6IXKogpE9YTigodgvLZ.rD1ZsdtxM49MSM3/NvZloNiRDdX/HPK', 'ADMIN', '2026-10-30 06:40:12', NULL, NULL, NULL, 'active', NULL),
(19, 'Pasindu Pokunugala', 'pgpnpokunugala@students.nsbm.ac.lk', '$2y$12$BoyCJeINKlzoxkcbYrPqeOkRosYkDp6rMpiUQ.qayAVoS7U/3Yf0K', 'ARTIST', '2026-10-30 06:50:50', NULL, NULL, NULL, 'active', NULL),
(20, 'Dumindu Sandeepa', 'dumindu2004@gmail.com', '$2y$12$AfihxCXCrH2MsyihjBIZw.TpyyI51B03wcvTYmLt34UztDGExuPgq', 'ARTIST', '2026-10-30 07:07:40', NULL, NULL, NULL, 'active', 'uploads/profiles/ca24b322b45b29ad4ba7d5bc038697ac.jpg'),
(21, 'Dulshini', 'dulshini@gmail.com', '$2y$12$nwwdr3miO46bTSKY3koz4eA0bgXddTSZ1vbi/ChAXFjlTKdSm1itO', 'ARTIST', '2026-10-30 08:11:03', NULL, NULL, NULL, 'active', NULL),
(22, 'Dumindu Sandeepa', 'dsandeepa2004@gmail.com', '$2y$12$XEkgg/IGcWONfuWqPBGN.Oxn6h9R9wiHk5vpsbLB9z3a/IryTKbHe', 'STUDENT', '2026-10-30 08:32:33', NULL, NULL, NULL, 'active', NULL),
(23, 'Kavidu damsath', 'kavidudamsath35@gmail.com', '$2y$12$TLSg0EqoXcz5ZBkR1nmSTuU2GXiMcwhaWp4XNh/3p0Bu/.LmJd1jm', 'ARTIST', '2026-10-30 16:42:59', NULL, NULL, NULL, 'active', NULL),
(24, 'Dinujaya Thisum', 'dinujayathisum138@gmail.com', '$2y$12$Dm/A/.LK8yoRRRaVOOcOy.rUzVKnt8QTkh/keeTCKS0L3ZXwx9Fp.', 'ARTIST', '2026-10-31 05:33:16', NULL, NULL, NULL, 'active', NULL),
(25, 'Osanda Rashmitha', 'isurunipathirathirathna@gmail.com', '$2y$12$QM/HQK.s64Q48yVdrN00nOQjsqrxET.Xa6zR9SOtKpQVQuqh.dY76', 'ARTIST', '2026-10-31 09:03:55', NULL, NULL, NULL, 'active', NULL),
(26, 'Isuruni Dulanjali', 'isurunipathirathna@gmail.com', '$2y$12$FKWu5fyQasnsXCiL09COT.sS46DPQsYy7X6rT.nP5yhvPRWeb2ibW', 'ARTIST', '2026-10-31 09:07:06', NULL, NULL, NULL, 'active', NULL),
(27, 'Pabasara', 'dinujayathisum@gmail.com', '$2y$12$Vsqq.fmX6AuJ7uU0vKgHl.gJMR0MYEoi5hSl/RGAO9T7n1F71T.ui', 'STUDENT', '2026-10-31 09:20:04', NULL, NULL, NULL, 'active', NULL),
(28, 'Dumindu Sandeepa Jayakody', 'dumindusandeepa22@gmail.com', '$2y$12$svIEIIbyAx3DjlKLaocjEezlv4vvGQj7xxqWUtSR.67rDHE6jhFiS', 'STUDENT', '2026-11-01 07:50:50', NULL, NULL, NULL, 'active', NULL),
(29, 'Dumindu Sandeepa', 'dumindusandeepa45@gmail.com', '$2y$12$3XYC41Gbme1NP/xkJ2w9J.l9zEINKv7dDNq9tBtH.01t3MyZ6IS3e', 'STUDENT', '2026-11-01 07:55:06', NULL, NULL, NULL, 'active', NULL),
(30, 'Keshani Weerasinghe', 'keshani2004@gmail.com', '$2y$12$R0xu1je0FKHDqzup8mtdBO8dZULm4.i8vE7SmMk8vb4ahYrcVeii2', 'ARTIST', '2026-11-02 05:05:45', NULL, NULL, NULL, 'active', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`artwork_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `artwork_images`
--
ALTER TABLE `artwork_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- Indexes for table `artwork_likes`
--
ALTER TABLE `artwork_likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `artwork_id` (`artwork_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `artwork_id` (`artwork_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_artworks`
--
ALTER TABLE `event_artworks`
  ADD PRIMARY KEY (`event_artwork_id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`artwork_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `follower_id` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `artwork_id` (`artwork_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saved_artworks`
--
ALTER TABLE `saved_artworks`
  ADD PRIMARY KEY (`user_id`,`artwork_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artwork_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `artwork_images`
--
ALTER TABLE `artwork_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `artwork_likes`
--
ALTER TABLE `artwork_likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event_artworks`
--
ALTER TABLE `event_artworks`
  MODIFY `event_artwork_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`),
  ADD CONSTRAINT `artworks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `artwork_images`
--
ALTER TABLE `artwork_images`
  ADD CONSTRAINT `artwork_images_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`) ON DELETE CASCADE;

--
-- Constraints for table `artwork_likes`
--
ALTER TABLE `artwork_likes`
  ADD CONSTRAINT `artwork_likes_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`),
  ADD CONSTRAINT `artwork_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE;

--
-- Constraints for table `event_artworks`
--
ALTER TABLE `event_artworks`
  ADD CONSTRAINT `event_artworks_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_artworks_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE;

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
