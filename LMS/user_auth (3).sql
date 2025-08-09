-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2025 at 09:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `announcement` text NOT NULL,
  `year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `faculty_id`, `announcement`, `year`, `created_at`) VALUES
(1, '20021', 'Y\'all should comefor lectures ASAP', 2021, '2025-01-31 21:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `matric_number`, `assignment_id`, `file_path`, `upload_date`) VALUES
(1, '100002', 2, '1738993045_bgdemo.jpg', '2025-02-08 05:37:25'),
(2, '100002', 3, '1738998463_CS.pdf', '2025-02-08 07:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `full_name`, `faculty_id`, `email`, `password`, `profile_picture`, `verification_code`, `is_verified`) VALUES
(1, 'Alex', '20021', 'solihetiko@gmail.com', '$2y$10$XHka4qp/o0OqkRhqQWVdS.xpecSRNo6K43.LxdPjfJeqAaEhAqRFW', '1739011253_JAGABAN2.jpg', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `file_path` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `faculty_id` varchar(100) DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matricnumber`
--

CREATE TABLE `matricnumber` (
  `id` int(11) NOT NULL,
  `matric_number` int(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matricnumber`
--

INSERT INTO `matricnumber` (`id`, `matric_number`, `name`) VALUES
(1, 1000001, 'Unkown');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `full_name`, `matric_number`, `email`, `password`, `year`, `profile_picture`, `verification_code`, `is_verified`) VALUES
(1, 'Etiko Solih', '100000', 'solihetiko@gmail.com', '$2y$10$z8Lxnu/5WIsU2rrLuk9Lr.abp59AvZ.JTkoXvH2elDP0cWOQ71BCK', 2020, 'uploads/679b5b03d07f3_42881E20-B83A-431D-B06B-D3BD98C749D6.jpg', NULL, 1),
(3, 'Ola Wale', '100002', 'etikosolih@gmail.com', '$2y$10$dd6AbL4UttqgO/TaeT5x8eK2e1QArZDdSRF4hMTa9C097n9eJObFW', 2021, '1738871791_Screenshot (52).png', NULL, 1),
(4, 'Opemipo', '100045', 'solihopemipo@gmail.com', '$2y$10$WF3bYQdKB4EWyx.NWfQtsuQsqYffg3e0wOvUBdZIh7Ivdiu1roxiy', 2021, '67a704121f0b7.jpg', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `subject_name` text NOT NULL,
  `year` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `subject_name`, `year`) VALUES
(1, 'Maths', 2021),
(2, 'English', 2021),
(3, 'OOP', 2021),
(4, 'Java', 2021),
(5, 'Further Math', 2023),
(6, 'Data Management', 2023),
(7, 'Networking', 2023);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `year`) VALUES
(1, 'Maths', 2021),
(2, 'English', 2021),
(3, 'OOP', 2021),
(4, 'Java', 2021);

-- --------------------------------------------------------

--
-- Table structure for table `upload_assignment`
--

CREATE TABLE `upload_assignment` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `due_date` date NOT NULL,
  `year` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload_assignment`
--

INSERT INTO `upload_assignment` (`id`, `faculty_id`, `subject_name`, `file_name`, `file_path`, `note`, `due_date`, `year`, `uploaded_at`) VALUES
(1, '20021', 'English', 'Ekta_Sem5_ses1_2_final_papers.pdf', 'uploads/Ekta_Sem5_ses1_2_final_papers.pdf', 'test', '2025-02-02', 2020, '2025-01-31 20:14:01'),
(2, '20021', 'OOP', 'Chapter_6_v8.2.pdf', 'uploads/Chapter_6_v8.2.pdf', 'Answer all answer correctly', '2025-02-08', 2021, '2025-02-06 22:28:51'),
(3, '20021', 'Java', 'CS.pdf', 'uploads/CS.pdf', 'Make sure you submit this before the deadline', '2025-02-09', 2021, '2025-02-08 07:04:29');

-- --------------------------------------------------------

--
-- Table structure for table `upload_materials`
--

CREATE TABLE `upload_materials` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `year` int(11) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload_materials`
--

INSERT INTO `upload_materials` (`id`, `faculty_id`, `subject`, `file_path`, `note`, `year`, `upload_date`) VALUES
(1, '20021', 'Maths', 'uploads/Ekta_Sem5_ses1_2_final_papers.pdf', 'dwew', 2025, '2025-01-31 16:32:47'),
(2, '20021', 'Maths', 'uploads/CS.pdf', 'effff', 2020, '2025-01-31 18:46:15'),
(3, '20021', 'Java', 'uploads/CAMPAIGN TABLE.pdf', '...........', 2021, '2025-02-08 09:54:42');

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

CREATE TABLE `years` (
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `years`
--

INSERT INTO `years` (`year`) VALUES
(2020),
(2021),
(2022),
(2023),
(2024),
(2025);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `year` (`year`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matric_number` (`matric_number`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_id` (`faculty_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matric_number` (`matric_number`);

--
-- Indexes for table `matricnumber`
--
ALTER TABLE `matricnumber`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matric_number` (`matric_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_student_year` (`year`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `year` (`year`);

--
-- Indexes for table `upload_assignment`
--
ALTER TABLE `upload_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `year` (`year`);

--
-- Indexes for table `upload_materials`
--
ALTER TABLE `upload_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `year` (`year`);

--
-- Indexes for table `years`
--
ALTER TABLE `years`
  ADD PRIMARY KEY (`year`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matricnumber`
--
ALTER TABLE `matricnumber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `upload_assignment`
--
ALTER TABLE `upload_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `upload_materials`
--
ALTER TABLE `upload_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`year`) REFERENCES `student` (`year`);

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`matric_number`) REFERENCES `student` (`matric_number`),
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `upload_assignment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`matric_number`) REFERENCES `student` (`matric_number`) ON DELETE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_student_year` FOREIGN KEY (`year`) REFERENCES `years` (`year`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`year`) REFERENCES `student` (`year`);

--
-- Constraints for table `upload_assignment`
--
ALTER TABLE `upload_assignment`
  ADD CONSTRAINT `upload_assignment_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `upload_assignment_ibfk_2` FOREIGN KEY (`year`) REFERENCES `student` (`year`);

--
-- Constraints for table `upload_materials`
--
ALTER TABLE `upload_materials`
  ADD CONSTRAINT `upload_materials_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `upload_materials_ibfk_2` FOREIGN KEY (`year`) REFERENCES `years` (`year`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
