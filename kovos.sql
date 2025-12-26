-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 08:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kovos`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`name`, `password`) VALUES
('maca', '2901');

-- --------------------------------------------------------

--
-- Table structure for table `scan_history`
--

CREATE TABLE `scan_history` (
  `id` int(11) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `scan_time` datetime NOT NULL,
  `status` enum('found','not_found') NOT NULL,
  `user_name` varchar(255) DEFAULT 'N/A',
  `user_ic` varchar(255) DEFAULT 'N/A',
  `vehicle` varchar(255) DEFAULT 'N/A',
  `user_status` varchar(255) DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `nama` varchar(100) NOT NULL,
  `no_kp` varchar(100) NOT NULL,
  `no_plat` varchar(100) NOT NULL,
  `kenderaan` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `barcode_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`nama`, `no_kp`, `no_plat`, `kenderaan`, `status`, `barcode_img`) VALUES
('NAZZATUL SOFEA BINTI ZAKI', '080208010340', 'BHN64', 'car', 'parent', 'admin/image/barcodes/barcode_BHN64_1749457686.png'),
('SITI MASRYNA BINTI SHAFIE', '73783898911', 'LOL1234', 'car', 'parent', 'admin/image/barcodes/barcode_LOL1234_1749304295.png'),
('MARSYA ASSYIFA BINTI MOHAMMAD ATTIRMIZI', '080129140390', 'QUYT0987', 'motorcycle', 'student', 'admin/image/barcodes/barcode_QUYT0987_1749303326.png'),
('SAUFI MUKHLIS BIN MOHAMMAD ATTIRMIZI', '09087654321', 'QWER 9712', 'car', 'parent', 'admin/image/barcodes/barcode_QWER_9712_1749303299.png'),
('NUR RAUDHAH MUNAWWARAH BINTI JEFRI', '080808040658', 'RM6785', 'car', 'parent', 'admin/image/barcodes/barcode_RM6785_1749457298.png'),
('MUZAFFAR AMMAR BIN MOHAMMAD ATTIRMIZI', '010221109876', 'SWQ 7654', 'van', 'staff', 'admin/image/barcodes/barcode_SWQ_7654_1749303834.png'),
('AN-RAIHAH DANIA UMAIRAH BINTI MOHD SUFIAN', '080520160212', 'WXW1282', 'motorcycle', 'student', 'admin/image/barcodes/barcode_WXW1282_1749457219.png');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `date_of_visit` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `purpose_of_visit` varchar(255) DEFAULT NULL,
  `person_to_meet` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`id`, `visitor_name`, `date_of_visit`, `time_in`, `time_out`, `purpose_of_visit`, `person_to_meet`, `remarks`) VALUES
(2, 'Ahmad bin Ali', '2025-06-03', '10:00:00', '11:30:00', 'Campus Tour', 'Mr. Rajan', 'First-time visitor'),
(3, 'Emily Tan', '2025-06-03', '14:00:00', '15:00:00', 'Admission Inquiry', 'Ms. Aida', 'Brought application documents'),
(4, 'John Lim', '2025-06-04', '09:30:00', '10:15:00', 'Business Meeting', 'Dr. Karim', 'Discussed partnership');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `scan_history`
--
ALTER TABLE `scan_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_scan_time` (`scan_time`),
  ADD KEY `idx_barcode` (`barcode`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`no_plat`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `scan_history`
--
ALTER TABLE `scan_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
