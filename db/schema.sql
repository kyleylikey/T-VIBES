-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 17, 2025 at 05:38 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `TaalTourismDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Logs`
--

CREATE TABLE `Logs` (
  `logid` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Rev`
--

CREATE TABLE `Rev` (
  `revid` int(11) NOT NULL,
  `review` varchar(45) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('submitted','displayed','archived') NOT NULL DEFAULT 'submitted',
  `userid` int(11) DEFAULT NULL,
  `siteid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE `Sites` (
  `siteid` int(11) NOT NULL,
  `sitename` varchar(100) NOT NULL,
  `siteimage` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `opdays` binary(7) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `status` enum('displayed','archived') NOT NULL DEFAULT 'displayed',
  `revid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tour`
--

CREATE TABLE `Tour` (
  `tourid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `status` enum('request','submitted','accepted','cancelled') NOT NULL DEFAULT 'request',
  `date` date NOT NULL,
  `companions` tinyint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
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
-- Indexes for dumped tables
--

--
-- Indexes for table `Logs`
--
ALTER TABLE `Logs`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `userid_idx` (`userid`);

--
-- Indexes for table `Rev`
--
ALTER TABLE `Rev`
  ADD PRIMARY KEY (`revid`),
  ADD KEY `userid_idx` (`userid`),
  ADD KEY `siteid_idx` (`siteid`);

--
-- Indexes for table `Sites`
--
ALTER TABLE `Sites`
  ADD PRIMARY KEY (`siteid`),
  ADD KEY `revid_idx` (`revid`);

--
-- Indexes for table `Tour`
--
ALTER TABLE `Tour`
  ADD PRIMARY KEY (`tourid`,`siteid`,`userid`),
  ADD KEY `fk_tour_siteid` (`siteid`),
  ADD KEY `fk_tour_userid` (`userid`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Rev`
--
ALTER TABLE `Rev`
  MODIFY `revid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Logs`
--
ALTER TABLE `Logs`
  ADD CONSTRAINT `fk_logs_userid` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `Rev`
--
ALTER TABLE `Rev`
  ADD CONSTRAINT `fk_rev_siteid` FOREIGN KEY (`siteid`) REFERENCES `Sites` (`siteid`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rev_userid` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `Sites`
--
ALTER TABLE `Sites`
  ADD CONSTRAINT `fk_sites_revid` FOREIGN KEY (`revid`) REFERENCES `Rev` (`revid`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `Tour`
--
ALTER TABLE `Tour`
  ADD CONSTRAINT `fk_tour_siteid` FOREIGN KEY (`siteid`) REFERENCES `Sites` (`siteid`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tour_userid` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
