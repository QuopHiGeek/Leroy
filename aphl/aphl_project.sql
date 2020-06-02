-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 08, 2020 at 05:25 PM
-- Server version: 8.0.13
-- PHP Version: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elab_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `aphl_mapping`
--

CREATE TABLE `aphl_mapping` (
  `id` int(11) NOT NULL,
  `vldmsid` int(11) NOT NULL,
  `tracker` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `maptype` enum('facility_id','picked_by','sample_type') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aphl_mapping`
--

INSERT INTO `aphl_mapping` (`id`, `vldmsid`, `tracker`, `maptype`) VALUES
(1, 232, 'WWBDN9jnCb8', 'facility_id'),
(2, 1, 'Plasma', 'sample_type'),
(3, 6, 'Whole Blood', 'sample_type'),
(4, 2, 'Ghana_Post', 'picked_by'),
(5, 1, 'Facility_Lab', 'picked_by');

-- --------------------------------------------------------

--
-- Table structure for table `aphl_tracker_requests`
--

CREATE TABLE `aphl_tracker_requests` (
  `eventuid` varchar(15) NOT NULL,
  `vl_sample_id` int(11) NOT NULL,
  `programid` varchar(15) NOT NULL,
  `programstageid` varchar(15) NOT NULL,
  `trackedentityid` varchar(15) NOT NULL,
  `orguitid` varchar(15) NOT NULL,
  `fetchdate` datetime NOT NULL,
  `result_sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `aphl_mapping`
--
ALTER TABLE `aphl_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aphl_tracker_requests`
--
ALTER TABLE `aphl_tracker_requests`
  ADD PRIMARY KEY (`eventuid`),
  ADD KEY `result_sent_date` (`result_sent_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aphl_mapping`
--
ALTER TABLE `aphl_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
