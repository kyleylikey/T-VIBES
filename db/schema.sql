-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2025 at 06:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taaltourismdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logid` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`logid`, `action`, `datetime`, `userid`) VALUES
(1, 'accepted tour request', '2025-01-20 14:49:18', 13),
(2, 'accepted tour request', '2025-02-04 14:49:18', 4),
(3, 'displayed review', '2025-01-30 14:49:18', 13),
(4, 'created tourist site', '2025-01-23 14:49:18', 13),
(5, 'archived review', '2025-02-03 14:49:18', 7),
(6, 'logged in', '2025-02-05 14:49:18', 4),
(7, 'logged out', '2025-02-13 14:49:18', 10),
(8, 'archived review', '2025-01-25 14:49:18', 13),
(9, 'logged in', '2025-02-11 14:49:18', 13),
(10, 'accepted tour request', '2025-02-03 14:49:18', 10);

-- --------------------------------------------------------

--
-- Table structure for table `rev`
--

CREATE TABLE `rev` (
  `revid` int(11) NOT NULL,
  `review` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('submitted','displayed','archived') NOT NULL DEFAULT 'submitted',
  `userid` int(11) DEFAULT NULL,
  `siteid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rev`
--

INSERT INTO `rev` (`revid`, `review`, `date`, `status`, `userid`, `siteid`) VALUES
(1, 'Excellent historical site!', '2025-02-18 14:35:08', 'submitted', 1, 1),
(2, 'A beautiful, peaceful place.', '2025-02-18 14:35:08', 'submitted', 2, 2),
(3, 'Must-visit for camera enthusiasts.', '2025-02-18 14:35:08', 'submitted', 3, 3),
(4, 'Stairs are a bit challenging but worth it!', '2025-02-18 14:35:08', 'submitted', 4, 4),
(5, 'A small museum, but very educational.', '2025-02-18 14:35:08', 'submitted', 5, 5),
(6, 'Loved the stories behind the museum exhibits.', '2025-02-18 14:35:08', 'submitted', 6, 6),
(7, 'A quaint town with lots of charm.', '2025-02-18 14:35:08', 'submitted', 7, 7),
(8, 'A lovely shrine with a spiritual atmosphere.', '2025-02-18 14:35:08', 'submitted', 8, 8),
(9, 'Great place to buy unique handcrafted knives.', '2025-02-18 14:35:08', 'submitted', 9, 9),
(10, 'Perfect spot for local goods and food!', '2025-02-18 14:35:08', 'submitted', 10, 10),
(11, 'A bit worn down, but still historic.', '2025-02-18 14:35:08', 'submitted', 11, 11),
(12, 'Beautiful place to visit for wedding history.', '2025-02-18 14:35:08', 'submitted', 12, 12),
(13, 'Nice boutique hotel with a lot of character.', '2025-02-18 14:35:08', 'submitted', 13, 13),
(14, 'A small but interesting museum within the church.', '2025-02-18 14:35:08', 'submitted', 14, 14),
(15, 'The view is absolutely stunning!', '2025-02-18 14:35:08', 'submitted', 15, 15),
(16, 'Very educational and informative.', '2025-02-18 14:35:08', 'submitted', 1, 1),
(17, 'A great spot for a quiet afternoon.', '2025-02-18 14:35:08', 'submitted', 2, 2),
(18, 'Rich in history and culture.', '2025-02-18 14:35:08', 'submitted', 3, 3),
(19, 'Challenging stairs, but the view is worth it!', '2025-02-18 14:35:08', 'submitted', 4, 4),
(20, 'A perfect museum for Philippine history buffs.', '2025-02-18 14:35:08', 'submitted', 5, 5),
(21, 'Amazing historical site, highly recommended!', '2025-02-19 05:49:12', 'displayed', 1, 1),
(22, 'Breathtaking views and peaceful atmosphere.', '2025-02-19 05:49:12', 'displayed', 2, 2),
(23, 'Perfect place for history lovers.', '2025-02-19 05:49:12', 'displayed', 3, 3),
(24, 'A wonderful heritage spot with great ambiance.', '2025-02-19 05:49:12', 'displayed', 4, 4),
(25, 'The museum was informative and well-organized.', '2025-02-19 05:49:12', 'displayed', 5, 5),
(26, 'A hidden gem filled with rich history.', '2025-02-19 05:49:12', 'displayed', 6, 6),
(27, 'Great place to learn about the local culture.', '2025-02-19 05:49:12', 'displayed', 7, 7),
(28, 'One of the best places to visit in town!', '2025-02-19 05:49:12', 'displayed', 8, 8),
(29, 'A must-see landmark for tourists.', '2025-02-19 05:49:12', 'displayed', 9, 9),
(30, 'Perfect place to relax and enjoy nature.', '2025-02-19 05:49:12', 'displayed', 10, 10),
(31, 'Loved the architecture and historical significance.', '2025-02-19 05:49:12', 'displayed', 11, 11),
(32, 'The staff was very friendly and knowledgeable.', '2025-02-19 05:49:12', 'displayed', 12, 12),
(33, 'A peaceful and spiritual experience.', '2025-02-19 05:49:12', 'displayed', 13, 13),
(34, 'One of the most picturesque spots in the area.', '2025-02-19 05:49:12', 'displayed', 14, 14),
(35, 'The guided tour was exceptional!', '2025-02-19 05:49:12', 'displayed', 15, 15),
(36, 'Fantastic place for photography enthusiasts.', '2025-02-19 05:49:12', 'displayed', 1, 2),
(37, 'The exhibits were well-curated and informative.', '2025-02-19 05:49:12', 'displayed', 2, 3),
(38, 'A great place for families and kids.', '2025-02-19 05:49:12', 'displayed', 3, 4),
(39, 'A historical treasure that shouldn’t be missed.', '2025-02-19 05:49:12', 'displayed', 4, 5),
(40, 'A remarkable experience from start to finish.', '2025-02-19 05:49:12', 'displayed', 5, 6),
(41, 'Too crowded and noisy.', '2025-02-19 05:49:12', 'archived', 3, 1),
(42, 'Great visuals, but lacking info.', '2025-02-19 05:49:12', 'archived', 5, 2),
(43, 'Mediocre exhibits and service.', '2025-02-19 05:49:12', 'archived', 2, 3),
(44, 'Beautiful but poorly maintained.', '2025-02-19 05:49:12', 'archived', 7, 4),
(45, 'Underwhelming overall.', '2025-02-19 05:49:12', 'archived', 9, 5),
(46, 'Charming, yet minimal guidance.', '2025-02-19 05:49:12', 'archived', 11, 6),
(47, 'Impressive history, outdated facilities.', '2025-02-19 05:49:12', 'archived', 4, 7),
(48, 'Overhyped and disappointing.', '2025-02-19 05:49:12', 'archived', 6, 8),
(49, 'Lovely place, needs work.', '2025-02-19 05:49:12', 'archived', 8, 9),
(50, 'Concise tour but lacked depth.', '2025-02-19 05:49:12', 'archived', 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `siteid` int(11) NOT NULL,
  `sitename` varchar(100) NOT NULL,
  `siteimage` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `opdays` binary(7) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `status` enum('displayed','archived') NOT NULL DEFAULT 'displayed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`siteid`, `sitename`, `siteimage`, `description`, `opdays`, `rating`, `price`, `status`) VALUES
(1, 'Taal Basilica', 'taal_basilica.jpg', 'Largest Catholic church in Asia.', 0x31313131313131, 4.8, 400, 'displayed'),
(2, 'Casa Villavicencio', 'casa_villavicencio.jpg', 'A historic ancestral home.', 0x30313031303130, 4.6, 200, 'displayed'),
(3, 'Galleria Taal', 'galleria_taal.jpg', 'A museum of vintage cameras.', 0x30303131313030, 4.7, 650, 'displayed'),
(4, 'San Lorenzo Ruiz Steps', 'san_lorenzo_steps.jpg', '125-step stairs leading to the Basilica.', 0x31303030303031, 4.5, 200, 'displayed'),
(5, 'Marcela Agoncillo Museum', 'marcela_agoncillo.jpg', 'Home of the first Philippine flag maker.', 0x30313130313130, 4.6, 750, 'displayed'),
(6, 'Leon Apacible Museum', 'leon_apacible.jpg', 'A museum showcasing revolutionary history.', 0x30303031313131, 4.5, 750, 'displayed'),
(7, 'Taal Heritage Town', 'taal_heritage_town.jpg', 'A well-preserved Spanish colonial town.', 0x31313131313130, 4.9, 610, 'displayed'),
(8, 'Our Lady of Caysasay Shrine', 'caysasay_shrine.jpg', 'A miraculous Marian shrine.', 0x31303130313031, 4.7, 750, 'displayed'),
(9, 'Balisong Knife Factory', 'balisong_factory.jpg', 'Famous for handcrafted balisong knives.', 0x30313131303030, 4.4, 200, 'displayed'),
(10, 'Taal Public Market', 'taal_market.jpg', 'Best place for local goods and delicacies.', 0x31313030313130, 4.3, 800, 'displayed'),
(11, 'Escuela Pia', 'escuela_pia.jpg', 'An old Spanish-era school building.', 0x30303030313030, 4.2, 180, 'archived'),
(12, 'Villavicencio Wedding Gift House', 'wedding_gift_house.jpg', 'Symbol of love and generosity.', 0x31303031313030, 4.5, 100, 'displayed'),
(13, 'Paradores Del Castillo', 'paradores_castillo.jpg', 'A boutique hotel with heritage appeal.', 0x31313130303030, 4.8, 500, 'displayed'),
(14, 'Basilica Museum', 'basilica_museum.jpg', 'A museum inside Taal Basilica.', 0x30313030313131, 4.6, 500, 'displayed'),
(15, 'Taal Volcano Viewpoint', 'taal_volcano.jpg', 'A breathtaking view of Taal Volcano.', 0x30303131303131, 4.9, 240, 'displayed');

-- --------------------------------------------------------

--
-- Table structure for table `tour`
--

CREATE TABLE `tour` (
  `tourid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `status` enum('request','submitted','accepted','cancelled') NOT NULL DEFAULT 'request',
  `date` date NOT NULL,
  `companions` tinyint(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour`
--

INSERT INTO `tour` (`tourid`, `siteid`, `userid`, `status`, `date`, `companions`, `created_at`) VALUES
(1, 2, 1, 'request', '2025-02-20', 3, '2025-02-19 02:00:00'),
(1, 3, 1, 'request', '2025-02-20', 3, '2025-02-19 02:00:00'),
(2, 4, 2, 'request', '2025-03-05', 4, '2025-02-19 02:10:00'),
(3, 1, 3, 'request', '2025-02-25', 2, '2025-02-19 02:20:00'),
(3, 5, 3, 'request', '2025-02-25', 2, '2025-02-19 02:20:00'),
(3, 6, 3, 'request', '2025-02-25', 2, '2025-02-19 02:20:00'),
(4, 7, 4, 'request', '2025-03-10', 3, '2025-02-19 02:30:00'),
(5, 8, 5, 'request', '2025-03-15', 5, '2025-02-19 02:40:00'),
(5, 9, 5, 'request', '2025-03-15', 5, '2025-02-19 02:40:00'),
(6, 10, 6, 'request', '2025-02-28', 2, '2025-02-19 02:50:00'),
(7, 11, 7, 'request', '2025-03-03', 4, '2025-02-19 03:00:00'),
(7, 12, 7, 'request', '2025-03-03', 4, '2025-02-19 03:00:00'),
(7, 13, 7, 'request', '2025-03-03', 4, '2025-02-19 03:00:00'),
(8, 14, 8, 'request', '2025-03-12', 3, '2025-02-19 03:10:00'),
(9, 15, 9, 'request', '2025-02-22', 2, '2025-02-19 03:20:00'),
(10, 3, 10, 'request', '2025-03-08', 3, '2025-02-19 03:30:00'),
(10, 5, 10, 'request', '2025-03-08', 3, '2025-02-19 03:30:00'),
(11, 1, 1, 'submitted', '2025-02-20', 2, '2025-02-18 02:15:00'),
(11, 2, 1, 'submitted', '2025-02-20', 2, '2025-02-18 02:15:00'),
(11, 5, 1, 'submitted', '2025-02-20', 2, '2025-02-18 02:15:00'),
(12, 3, 2, 'submitted', '2025-02-22', 3, '2025-02-18 03:05:00'),
(12, 4, 2, 'submitted', '2025-02-22', 3, '2025-02-18 03:05:00'),
(13, 6, 3, 'submitted', '2025-02-25', 1, '2025-02-18 04:30:00'),
(14, 7, 4, 'submitted', '2025-02-27', 4, '2025-02-18 05:45:00'),
(14, 8, 4, 'submitted', '2025-02-27', 4, '2025-02-18 05:45:00'),
(14, 9, 4, 'submitted', '2025-02-27', 4, '2025-02-18 05:45:00'),
(15, 10, 5, 'submitted', '2025-03-01', 2, '2025-02-18 06:10:00'),
(15, 11, 5, 'submitted', '2025-03-01', 2, '2025-02-18 06:10:00'),
(16, 12, 6, 'submitted', '2025-03-03', 3, '2025-02-18 07:20:00'),
(17, 13, 7, 'submitted', '2025-03-05', 5, '2025-02-18 08:00:00'),
(17, 14, 7, 'submitted', '2025-03-05', 5, '2025-02-18 08:00:00'),
(18, 15, 8, 'submitted', '2025-03-07', 1, '2025-02-18 09:10:00'),
(19, 2, 9, 'submitted', '2025-03-10', 2, '2025-02-18 10:30:00'),
(19, 5, 9, 'submitted', '2025-03-10', 2, '2025-02-18 10:30:00'),
(19, 6, 9, 'submitted', '2025-03-10', 2, '2025-02-18 10:30:00'),
(20, 1, 10, 'submitted', '2025-03-12', 3, '2025-02-18 11:45:00'),
(20, 3, 10, 'submitted', '2025-03-12', 3, '2025-02-18 11:45:00'),
(20, 7, 10, 'submitted', '2025-03-12', 3, '2025-02-18 11:45:00'),
(21, 2, 5, 'accepted', '2025-03-10', 2, '2025-01-15 02:00:00'),
(21, 6, 5, 'accepted', '2025-03-10', 2, '2025-01-15 02:00:00'),
(21, 9, 5, 'accepted', '2025-03-10', 2, '2025-01-15 02:00:00'),
(22, 1, 3, 'accepted', '2025-04-20', 1, '2025-02-01 01:00:00'),
(22, 4, 3, 'accepted', '2025-04-20', 1, '2025-02-01 01:00:00'),
(23, 7, 10, 'accepted', '2025-05-15', 4, '2025-01-20 06:30:00'),
(23, 11, 10, 'accepted', '2025-05-15', 4, '2025-01-20 06:30:00'),
(23, 15, 10, 'accepted', '2025-05-15', 4, '2025-01-20 06:30:00'),
(24, 5, 8, 'accepted', '2025-06-01', 3, '2025-02-10 04:00:00'),
(24, 12, 8, 'accepted', '2025-06-01', 3, '2025-02-10 04:00:00'),
(25, 3, 12, 'accepted', '2025-07-10', 2, '2025-03-05 00:15:00'),
(25, 6, 12, 'accepted', '2025-07-10', 2, '2025-03-05 00:15:00'),
(25, 10, 12, 'accepted', '2025-07-10', 2, '2025-03-05 00:15:00'),
(25, 14, 12, 'accepted', '2025-07-10', 2, '2025-03-05 00:15:00'),
(26, 2, 6, 'accepted', '2025-08-15', 1, '2025-04-01 02:30:00'),
(26, 8, 6, 'accepted', '2025-08-15', 1, '2025-04-01 02:30:00'),
(26, 13, 6, 'accepted', '2025-08-15', 1, '2025-04-01 02:30:00'),
(27, 9, 7, 'accepted', '2025-09-05', 3, '2025-03-20 07:45:00'),
(27, 11, 7, 'accepted', '2025-09-05', 3, '2025-03-20 07:45:00'),
(28, 4, 14, 'accepted', '2025-10-20', 5, '2025-02-25 03:00:00'),
(28, 7, 14, 'accepted', '2025-10-20', 5, '2025-02-25 03:00:00'),
(28, 15, 14, 'accepted', '2025-10-20', 5, '2025-02-25 03:00:00'),
(29, 1, 2, 'accepted', '2025-11-05', 2, '2025-03-15 01:20:00'),
(29, 10, 2, 'accepted', '2025-11-05', 2, '2025-03-15 01:20:00'),
(30, 5, 15, 'accepted', '2025-12-15', 4, '2025-04-10 05:30:00'),
(30, 12, 15, 'accepted', '2025-12-15', 4, '2025-04-10 05:30:00'),
(30, 14, 15, 'accepted', '2025-12-15', 4, '2025-04-10 05:30:00'),
(31, 2, 3, 'cancelled', '2025-03-05', 2, '2025-02-15 02:30:00'),
(31, 5, 3, 'cancelled', '2025-03-05', 2, '2025-02-15 02:30:00'),
(31, 8, 3, 'cancelled', '2025-03-05', 2, '2025-02-15 02:30:00'),
(32, 4, 7, 'cancelled', '2025-03-10', 3, '2025-02-16 06:20:00'),
(32, 10, 7, 'cancelled', '2025-03-10', 3, '2025-02-16 06:20:00'),
(33, 1, 5, 'cancelled', '2025-04-02', 1, '2025-02-17 01:45:00'),
(33, 3, 5, 'cancelled', '2025-04-02', 1, '2025-02-17 01:45:00'),
(33, 6, 5, 'cancelled', '2025-04-02', 1, '2025-02-17 01:45:00'),
(33, 9, 5, 'cancelled', '2025-04-02', 1, '2025-02-17 01:45:00'),
(34, 7, 10, 'cancelled', '2025-05-01', 4, '2025-02-18 00:10:00'),
(34, 12, 10, 'cancelled', '2025-05-01', 4, '2025-02-18 00:10:00'),
(35, 5, 2, 'cancelled', '2025-06-15', 2, '2025-02-18 03:30:00'),
(35, 11, 2, 'cancelled', '2025-06-15', 2, '2025-02-18 03:30:00'),
(35, 14, 2, 'cancelled', '2025-06-15', 2, '2025-02-18 03:30:00'),
(36, 6, 9, 'cancelled', '2025-07-20', 5, '2025-02-18 07:45:00'),
(36, 13, 9, 'cancelled', '2025-07-20', 5, '2025-02-18 07:45:00'),
(36, 15, 9, 'cancelled', '2025-07-20', 5, '2025-02-18 07:45:00'),
(37, 3, 12, 'cancelled', '2025-08-05', 1, '2025-02-19 04:00:00'),
(37, 8, 12, 'cancelled', '2025-08-05', 1, '2025-02-19 04:00:00'),
(37, 10, 12, 'cancelled', '2025-08-05', 1, '2025-02-19 04:00:00'),
(37, 14, 12, 'cancelled', '2025-08-05', 1, '2025-02-19 04:00:00'),
(38, 2, 6, 'cancelled', '2025-09-10', 3, '2025-02-20 06:50:00'),
(38, 9, 6, 'cancelled', '2025-09-10', 3, '2025-02-20 06:50:00'),
(38, 13, 6, 'cancelled', '2025-09-10', 3, '2025-02-20 06:50:00'),
(39, 1, 4, 'cancelled', '2025-10-01', 2, '2025-02-21 08:20:00'),
(39, 5, 4, 'cancelled', '2025-10-01', 2, '2025-02-21 08:20:00'),
(39, 11, 4, 'cancelled', '2025-10-01', 2, '2025-02-21 08:20:00'),
(39, 15, 4, 'cancelled', '2025-10-01', 2, '2025-02-21 08:20:00'),
(40, 7, 8, 'cancelled', '2025-11-20', 4, '2025-02-22 10:05:00'),
(40, 12, 8, 'cancelled', '2025-11-20', 4, '2025-02-22 10:05:00'),
(40, 14, 8, 'cancelled', '2025-11-20', 4, '2025-02-22 10:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `hashedpassword` varchar(255) NOT NULL,
  `contactnum` varchar(15) NOT NULL,
  `usertype` enum('trst','emp','mngr') NOT NULL DEFAULT 'trst',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `email` varchar(100) NOT NULL,
  `emailveriftoken` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `name`, `hashedpassword`, `contactnum`, `usertype`, `status`, `email`, `emailveriftoken`, `token_expiry`) VALUES
(1, 'test01', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071038', 'trst', 'active', 'test01@taal.tourism.com', '06d77ad2e80407c48ace230141a2fed7c3032a67793ced639db937bc800769ab', '2025-02-17 08:46:14'),
(2, 'test02', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'mngr', 'active', 'test02@taal.tourism.com', NULL, NULL),
(3, 'test03', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'trst', 'active', 'test03@taal.tourism.com', NULL, NULL),
(4, 'test04', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'emp', 'active', 'test04@taal.tourism.com', NULL, NULL),
(5, 'test05', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'mngr', 'active', 'test05@taal.tourism.com', NULL, NULL),
(6, 'test06', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'trst', 'active', 'test06@taal.tourism.com', NULL, NULL),
(7, 'test07', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'emp', 'active', 'test07@taal.tourism.com', NULL, NULL),
(8, 'test08', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'mngr', 'active', 'test08@taal.tourism.com', NULL, NULL),
(9, 'test09', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'trst', 'active', 'test09@taal.tourism.com', NULL, NULL),
(10, 'test10', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'emp', 'active', 'test10@taal.tourism.com', NULL, NULL),
(11, 'test11', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'mngr', 'active', 'test11@taal.tourism.com', NULL, NULL),
(12, 'test12', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'trst', 'active', 'test12@taal.tourism.com', NULL, NULL),
(13, 'test13', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'emp', 'active', 'test13@taal.tourism.com', NULL, NULL),
(14, 'test14', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'mngr', 'active', 'test14@taal.tourism.com', NULL, NULL),
(15, 'test15', 'test', '$2y$10$qNSxBcAAnbrQmJEyKgDUzOnouDuTl2fQp80AYxs0abmCfaO6YpA42', '09171071748', 'trst', 'active', 'test15@taal.tourism.com', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `userid_idx` (`userid`);

--
-- Indexes for table `rev`
--
ALTER TABLE `rev`
  ADD PRIMARY KEY (`revid`),
  ADD KEY `userid_idx` (`userid`),
  ADD KEY `siteid_idx` (`siteid`);

--
-- Indexes for table `sites`
--
ALTER TABLE `sites`
  ADD PRIMARY KEY (`siteid`);

--
-- Indexes for table `tour`
--
ALTER TABLE `tour`
  ADD PRIMARY KEY (`tourid`,`siteid`,`userid`),
  ADD KEY `fk_tour_siteid` (`siteid`),
  ADD KEY `fk_tour_userid` (`userid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rev`
--
ALTER TABLE `rev`
  MODIFY `revid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `tour`
--
ALTER TABLE `tour`
  MODIFY `tourid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `rev`
--
ALTER TABLE `rev`
  ADD CONSTRAINT `fk_rev_siteid` FOREIGN KEY (`siteid`) REFERENCES `sites` (`siteid`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rev_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `tour`
--
ALTER TABLE `tour`
  ADD CONSTRAINT `fk_tour_siteid` FOREIGN KEY (`siteid`) REFERENCES `sites` (`siteid`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tour_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
