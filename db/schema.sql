-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2025 at 04:45 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `rev`
--

CREATE TABLE `rev` (
  `revid` int(11) NOT NULL,
  `review` varchar(45) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('submitted','displayed','archived') NOT NULL DEFAULT 'submitted',
  `userid` int(11) DEFAULT NULL,
  `siteid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `name`, `hashedpassword`, `contactnum`, `usertype`, `status`, `email`) VALUES
(2, 'kyle', 'Kyle Baldoza', '$2y$10$i5G18fFqaGGoJLQv1FlvzOLOTHPzT/QFLTLkY3Rw3MWWiQhiTEQTq', '09171099438', 'trst', 'active', 'kyleashleigh.baldoza.cics@ust.edu.ph');

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
-- AUTO_INCREMENT for table `rev`
--
ALTER TABLE `rev`
  MODIFY `revid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
