-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 10, 2023 at 04:04 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecomplaint`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

DROP TABLE IF EXISTS `complaint`;
CREATE TABLE IF NOT EXISTS `complaint` (
  `complaintID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `complaint_date` timestamp NOT NULL,
  `modify_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `categories` varchar(255) NOT NULL,
  PRIMARY KEY (`complaintID`),
  KEY `complaint_ibfk_1` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `complaint`
--

INSERT INTO `complaint` (`complaintID`, `userID`, `title`, `description`, `complaint_date`, `modify_date`, `status`, `remark`, `categories`) VALUES
(1, 2220407, 'test', 'yryr', '2023-01-08 14:50:50', '2023-01-09 09:30:13', 'Done', '', ''),
(2, 2220405, 'Toilet Water', 'leaking', '2023-01-08 14:50:50', '2023-01-09 09:30:13', 'New', '', ''),
(3, 2220405, 'Rubbish', 'kjhkhkj', '2023-01-08 17:54:56', '2023-01-09 09:30:13', 'KIV', 'Hahah', 'Executive-Cleaning'),
(4, 2220405, 'Cannot register', 'lkjlkjl', '2023-01-08 17:54:56', '2023-01-09 09:30:13', 'Done', '', 'Select a categories'),
(5, 2220405, 'Wifi problems', 'ksldkjKIV', '2023-01-08 18:08:31', '2023-01-09 09:30:13', 'New', '', ''),
(6, 2220405, 'Canteen Dirty', 'iyuiyhkjh', '2023-01-08 18:08:31', '2023-01-09 09:30:13', 'Active', '', 'Select a categories'),
(7, 2220407, 'Wifi unstable', 'jlkj', '2023-01-09 09:41:19', '2023-01-09 09:41:19', 'Pending', 'Nnoooon', 'Executive-IT'),
(8, 2220405, 'IT problems 2', 'jlkj', '2023-01-09 09:41:36', '2023-01-09 09:41:36', 'Pending', 'Test123', 'Executive-Registration'),
(9, 2220405, 'Register problems', 'jlkj', '2023-01-09 09:43:03', '2023-01-09 09:43:03', 'Done', '', ''),
(11, 2220405, 'Test 2', 'Hihi', '2023-01-09 15:06:08', '2023-01-09 15:06:08', 'Pending', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `fileID` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `complaintID` int(11) NOT NULL,
  PRIMARY KEY (`fileID`),
  KEY `files_ibfk_1` (`complaintID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileID`, `file_name`, `complaintID`) VALUES
(1, 'paip.jpg', 9),
(2, 'stand.jpg', 6),
(3, 'tank.jpg', 9);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role`, `description`) VALUES
('Admin', 'Able to create, manage and delete user'),
('Executive-Cleaning', NULL),
('Executive-IT', NULL),
('Executive-Registration', NULL),
('Executive-Student Affair', NULL),
('Helpdesk', ' able to assign complaint to any executive staff in charge of the complained matters.'),
('User', 'Able to lodge any complaint to the New Era Management by filling in the complaint form, attach the photos and videos related to the complaint.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` varchar(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `register_date` timestamp NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `password`, `username`, `email`, `gender`, `role`, `register_date`) VALUES
(2220001, '8ddd27c1dae1d222638babf67015fcf0', 'Helpdesk1', 'helpdesk@gmail.com', 'Male', 'Helpdesk', '2023-01-10 07:22:48'),
(2220405, '8ddd27c1dae1d222638babf67015fcf0', 'Chia', 'chia@gmail.com', 'Female', 'User', '2023-01-08 13:26:07'),
(2220407, '8ddd27c1dae1d222638babf67015fcf0', 'Chia Yeu Shyang', 'yeushyang020825@gmail.com', 'Male', 'Admin', '2023-01-07 11:16:58');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaint`
--
ALTER TABLE `complaint`
  ADD CONSTRAINT `complaint_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`complaintID`) REFERENCES `complaint` (`complaintID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `role` FOREIGN KEY (`role`) REFERENCES `roles` (`role`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
