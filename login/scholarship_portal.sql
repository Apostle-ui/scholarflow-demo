-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 02:40 PM
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
-- Database: `scholarship_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminaccounts_tbl`
--

CREATE TABLE `adminaccounts_tbl` (
  `adminaccounts_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminaccounts_tbl`
--

INSERT INTO `adminaccounts_tbl` (`adminaccounts_id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applicant_demographic`
--

CREATE TABLE `applicant_demographic` (
  `applicant_id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `mother_firstname` varchar(255) DEFAULT NULL,
  `mother_middlename` varchar(255) DEFAULT NULL,
  `mother_lastname` varchar(255) DEFAULT NULL,
  `mother_contact_number` varchar(15) DEFAULT NULL,
  `mother_birthdate` date DEFAULT NULL,
  `father_firstname` varchar(255) DEFAULT NULL,
  `father_middlename` varchar(255) DEFAULT NULL,
  `father_lastname` varchar(255) DEFAULT NULL,
  `father_contact_number` varchar(15) DEFAULT NULL,
  `father_birthdate` date DEFAULT NULL,
  `school_level` varchar(50) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_demographic`
--

INSERT INTO `applicant_demographic` (`applicant_id`, `firstname`, `middlename`, `lastname`, `gender`, `birthdate`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `mother_contact_number`, `mother_birthdate`, `father_firstname`, `father_middlename`, `father_lastname`, `father_contact_number`, `father_birthdate`, `school_level`, `year_level`, `school_name`, `contact_number`, `province`, `city`, `barangay`, `street`, `status`) VALUES
(12, 'Kentt', 'pilarta', 'Pilarta', 'Female', '2012-11-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '09636462682', 'Metro Manila', 'Muntinlupa', 'Alabang', 'Purok 4 #123 Balbanero Compound', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `applicant_parent`
--

CREATE TABLE `applicant_parent` (
  `applicant_parent_id` int(10) UNSIGNED NOT NULL,
  `applicant_id` int(10) UNSIGNED DEFAULT NULL,
  `mother_firstname` varchar(50) DEFAULT NULL,
  `mother_middlename` varchar(50) DEFAULT NULL,
  `mother_lastname` varchar(50) DEFAULT NULL,
  `mother_contact_number` varchar(15) DEFAULT NULL,
  `mother_birthdate` date DEFAULT NULL,
  `father_firstname` varchar(50) DEFAULT NULL,
  `father_middlename` varchar(50) DEFAULT NULL,
  `father_lastname` varchar(50) DEFAULT NULL,
  `father_contact_number` varchar(15) DEFAULT NULL,
  `father_birthdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_parent`
--

INSERT INTO `applicant_parent` (`applicant_parent_id`, `applicant_id`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `mother_contact_number`, `mother_birthdate`, `father_firstname`, `father_middlename`, `father_lastname`, `father_contact_number`, `father_birthdate`) VALUES
(12, NULL, 'Myraflor', 'Taup', 'Neri', '09636462682', '2006-11-16', 'Remigio', 'Vina', 'Neri', '09636462682', '2006-11-05');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_school_file`
--

CREATE TABLE `applicant_school_file` (
  `applicant_school_file_id` int(10) UNSIGNED NOT NULL,
  `applicant_id` int(10) UNSIGNED DEFAULT NULL,
  `school_level` varchar(50) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `school_name` varchar(100) DEFAULT NULL,
  `certificate_registration` blob DEFAULT NULL,
  `school_identification` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_school_file`
--

INSERT INTO `applicant_school_file` (`applicant_school_file_id`, `applicant_id`, `school_level`, `year_level`, `school_name`, `certificate_registration`, `school_identification`) VALUES
(12, NULL, 'senior-high-school', 'grade-11', 'PLMUN', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `eligible_applicants_tbl`
--

CREATE TABLE `eligible_applicants_tbl` (
  `eligible_applicant_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `birthdate` varchar(255) NOT NULL,
  `mother_middlename` varchar(255) DEFAULT NULL,
  `mother_lastname` varchar(255) DEFAULT NULL,
  `mother_contact_number` varchar(15) DEFAULT NULL,
  `mother_birthdate` date DEFAULT NULL,
  `father_firstname` varchar(255) DEFAULT NULL,
  `father_middlename` varchar(255) DEFAULT NULL,
  `father_lastname` varchar(255) DEFAULT NULL,
  `father_contact_number` varchar(15) DEFAULT NULL,
  `father_birthdate` date DEFAULT NULL,
  `mother_firstname` varchar(255) DEFAULT NULL,
  `school_level` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligible_applicants_tbl`
--

INSERT INTO `eligible_applicants_tbl` (`eligible_applicant_id`, `firstname`, `middlename`, `lastname`, `gender`, `birthdate`, `mother_middlename`, `mother_lastname`, `mother_contact_number`, `mother_birthdate`, `father_firstname`, `father_middlename`, `father_lastname`, `father_contact_number`, `father_birthdate`, `mother_firstname`, `school_level`, `year_level`, `school_name`) VALUES
(3, 'Ivan Carl', 'Taup', 'Neri', 'Female', '2012-11-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `organizeraccounts_tbl`
--

CREATE TABLE `organizeraccounts_tbl` (
  `organizeraccounts_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizeraccounts_tbl`
--

INSERT INTO `organizeraccounts_tbl` (`organizeraccounts_id`, `username`, `password`, `name`, `email`, `contact_number`) VALUES
(1, 'organizer', 'organizer', 'Test Organizer #1', 'organizer1@gmail.com', '09387128177'),
(2, 'organizer2', 'organizer2', 'Text Organizer #2 ', 'organizer2@gmail.com', '09636462682');

-- --------------------------------------------------------

--
-- Table structure for table `rejected_applicants_tbl`
--

CREATE TABLE `rejected_applicants_tbl` (
  `rejected_applicant_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `birthdate` varchar(255) NOT NULL,
  `mother_firstname` varchar(255) DEFAULT NULL,
  `mother_middlename` varchar(255) DEFAULT NULL,
  `mother_lastname` varchar(255) DEFAULT NULL,
  `mother_contact_number` varchar(15) DEFAULT NULL,
  `mother_birthdate` date DEFAULT NULL,
  `father_firstname` varchar(255) DEFAULT NULL,
  `father_middlename` varchar(255) DEFAULT NULL,
  `father_lastname` varchar(255) DEFAULT NULL,
  `father_contact_number` varchar(15) DEFAULT NULL,
  `father_birthdate` date DEFAULT NULL,
  `school_level` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rejected_applicants_tbl`
--

INSERT INTO `rejected_applicants_tbl` (`rejected_applicant_id`, `firstname`, `middlename`, `lastname`, `gender`, `birthdate`, `mother_firstname`, `mother_middlename`, `mother_lastname`, `mother_contact_number`, `mother_birthdate`, `father_firstname`, `father_middlename`, `father_lastname`, `father_contact_number`, `father_birthdate`, `school_level`, `year_level`, `school_name`) VALUES
(3, 'Nicole', '', 'Primero', 'Female', '2012-11-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `useraccounts_tbl`
--

CREATE TABLE `useraccounts_tbl` (
  `useraccounts_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useraccounts_tbl`
--

INSERT INTO `useraccounts_tbl` (`useraccounts_id`, `username`, `email`, `password`) VALUES
(1, 'ivancarl', 'nerivancarl@gmail.com', 'ivan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminaccounts_tbl`
--
ALTER TABLE `adminaccounts_tbl`
  ADD PRIMARY KEY (`adminaccounts_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applicant_demographic`
--
ALTER TABLE `applicant_demographic`
  ADD PRIMARY KEY (`applicant_id`);

--
-- Indexes for table `applicant_parent`
--
ALTER TABLE `applicant_parent`
  ADD PRIMARY KEY (`applicant_parent_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `applicant_school_file`
--
ALTER TABLE `applicant_school_file`
  ADD PRIMARY KEY (`applicant_school_file_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `eligible_applicants_tbl`
--
ALTER TABLE `eligible_applicants_tbl`
  ADD PRIMARY KEY (`eligible_applicant_id`);

--
-- Indexes for table `organizeraccounts_tbl`
--
ALTER TABLE `organizeraccounts_tbl`
  ADD PRIMARY KEY (`organizeraccounts_id`);

--
-- Indexes for table `rejected_applicants_tbl`
--
ALTER TABLE `rejected_applicants_tbl`
  ADD PRIMARY KEY (`rejected_applicant_id`);

--
-- Indexes for table `useraccounts_tbl`
--
ALTER TABLE `useraccounts_tbl`
  ADD PRIMARY KEY (`useraccounts_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminaccounts_tbl`
--
ALTER TABLE `adminaccounts_tbl`
  MODIFY `adminaccounts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applicant_demographic`
--
ALTER TABLE `applicant_demographic`
  MODIFY `applicant_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `applicant_parent`
--
ALTER TABLE `applicant_parent`
  MODIFY `applicant_parent_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `applicant_school_file`
--
ALTER TABLE `applicant_school_file`
  MODIFY `applicant_school_file_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `eligible_applicants_tbl`
--
ALTER TABLE `eligible_applicants_tbl`
  MODIFY `eligible_applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `organizeraccounts_tbl`
--
ALTER TABLE `organizeraccounts_tbl`
  MODIFY `organizeraccounts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rejected_applicants_tbl`
--
ALTER TABLE `rejected_applicants_tbl`
  MODIFY `rejected_applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useraccounts_tbl`
--
ALTER TABLE `useraccounts_tbl`
  MODIFY `useraccounts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicant_parent`
--
ALTER TABLE `applicant_parent`
  ADD CONSTRAINT `applicant_parent_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicant_demographic` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_applicant` FOREIGN KEY (`applicant_parent_id`) REFERENCES `applicant_demographic` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_applicant_parent` FOREIGN KEY (`applicant_parent_id`) REFERENCES `applicant_demographic` (`applicant_id`) ON DELETE CASCADE;

--
-- Constraints for table `applicant_school_file`
--
ALTER TABLE `applicant_school_file`
  ADD CONSTRAINT `applicant_school_file_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicant_demographic` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_applicant_school_file` FOREIGN KEY (`applicant_school_file_id`) REFERENCES `applicant_demographic` (`applicant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
