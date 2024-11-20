-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 05:59 PM
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
-- Database: `dbtest`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(255) NOT NULL,
  `course_id` int(255) NOT NULL,
  `category_id` int(255) NOT NULL,
  `assignment_name` varchar(255) NOT NULL,
  `total_points` int(11) NOT NULL,
  `max_points` float NOT NULL,
  `due_date` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(254) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `instructor` varchar(255) NOT NULL,
  `credits` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `instructor`, `credits`) VALUES
(1, 'English_grd12', 'Samantha Lizarraga', '6'),
(2, 'math_grd12', '', '6'),
(3, 'spanish_grd12', '', '5');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `enrollment_id` int(255) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(255) NOT NULL,
  `semester` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`enrollment_id`, `class_name`, `user_id`, `course_id`, `semester`) VALUES
(0, 'Teacher', 0, 0, ''),
(11, 'Freshman 1', 0, 0, ''),
(12, 'Freshman 2', 0, 0, ''),
(21, 'Sophomore 1', 0, 0, ''),
(22, 'Sophomore 2', 0, 0, ''),
(31, 'Junior 1', 0, 0, ''),
(32, 'Junior 2', 0, 0, ''),
(41, 'Senior 1', 0, 0, ''),
(42, 'Senior 2', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `final_grades`
--

CREATE TABLE `final_grades` (
  `final_grade_id` int(4) NOT NULL,
  `user_id` int(4) NOT NULL,
  `course_id` int(1) NOT NULL,
  `final_grade` decimal(4,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gpa`
--

CREATE TABLE `gpa` (
  `gpa_id` int(4) NOT NULL,
  `user_id` int(4) NOT NULL,
  `semester` int(1) NOT NULL,
  `gpa` decimal(4,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(4) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total points` int(255) NOT NULL,
  `score` float NOT NULL,
  `date_submitted` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_categories`
--

CREATE TABLE `grade_categories` (
  `category_id` int(254) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_categories`
--

INSERT INTO `grade_categories` (`category_id`, `category_name`, `weight`) VALUES
(1, 'participation', '10'),
(2, 'quiz', '15'),
(3, 'assignment', '20'),
(4, 'test', '30'),
(5, 'exam', '25');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_type`) VALUES
(1, 'admin'),
(2, 'student'),
(3, 'teacher'),
(4, 'parent'),
(5, 'cao_admin');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT 2,
  `enrollment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `email`, `username`, `password`, `name`, `role_id`, `enrollment_id`) VALUES
(1, 'casey@gmail.com', 'casey', '$2y$10$Ir/ijSafL9E6crfVIh9Bk.bNCtBBbbHrGcozv37T2ESz4pW9mngYm', 'Casey Malyah', 4, 0),
(8, 'supacool@gmail.com', 'cm', '$2y$10$w6Xy24Z2nB/1DdNbhIvmtO9/lebWTLZobMheS7EUbfaQuKLYG9m4G', 'Johnson Chen', 3, 0),
(10, 'NOTIME4Ls@gmail.com', 'c', '$2y$10$/umpfmvMGeWQMsn2HRJRGeSNDIUfV/pWo2nxBXIpOnPBaYClkHmky', 'Cristiano Ronaldo', 1, 0),
(28, 'ashephard@belizehighschool.edu.bz', 'auruyannashephard', '$2y$10$de5Ec7MtnDkxC8tgXjON9.l3HkxQ1sbOhTB6VvPke/3j9FHv9Pjj6', 'Auruyanna Shepherd', 2, 12),
(29, 'azabaneh@belizehighschool.edu.bz', 'adenzabaneh', '$2y$10$5qzyaa5OkLIlNTXeT5.nAuMIxt7lT2mUqCPASZW2kHl7JA4vdumQu', 'Aden Zabaneh', 2, 32),
(30, 'kdomingo@belizehighschool.edu.bz', 'kariidomingo', '$2y$10$VeVzZ4NsQUuGxtS3wN48dOdng4kcWSLvedwn9OGefxYuWRpuoIO1O', 'Karii Domingo', 2, 32),
(31, 'cmelendez@belizehighschool.edu.bz', 'caesarmelendez', '$2y$10$Ke3LaaG6H/p3hs2kusx6reU4NeTt20LpWiiNu0HKGb8lDgWutZEv.', 'Caesar Melendez', 2, 41),
(32, 'mcasey@belizehighschool.edu.bz', 'maliyahcasey', '$2y$10$Rpphmw5lJ7yswCOwTY9SruCry3Ka8MWUyjDFk/cUhZMb/jPTpXXz2', 'Maliyah Casey', 2, 42),
(33, 'dsharp@belizehighschool.edu.bz', 'danielsharp', '$2y$10$.lCcyOmts/X7hVw32TkGUe9q6HOQlHaz79dCI5k4f7B074MVCBqX.', 'Daniel Sharp', 2, 12),
(35, 'slizarraga@belizehighschool.edu.bz', 'samanthalizarraga', '$2y$10$37IF0F3lU4EM4MRB5YjR5u1xUQjb7UCG.WkYCu.YEMbn/PqDzV.XG', 'Samantha Lizarraga', 3, 0),
(36, 'arhys@belizehighschool.edu.bz', 'amarerhys', '$2y$10$h2dkJStSLQBAljre.5ORouOGyVOhvZP1FfZqE2nRg09.ODQs5V8jq', 'Amare Rhys', 2, 31),
(37, 'jespat@belizehighschool.edu.bz', 'jacobespat', '$2y$10$aSF1jNMbulD7PtTknvygEOZtk75hYte2dhFWrpQ.ZNnEfDrv8dA6a', 'Jacob Espat', 2, 11),
(38, 'cnandwani@belizehighschool.edu.bz', 'chrisnandwani', '$2y$10$37sbg1M./d8pxwIZ/etR1ud5x0ebF6sGgnADo059dn9h3kAsEMG4y', 'Chris Nandwani', 2, 31),
(39, 'xhuang@belizehighschool.edu.bz', 'amyximinhuang', '$2y$10$D2Gm7PTlxQ0QN4PaLp7k2.Kd6ylCTFFM6Oz7pXIcGNXjD5eb55D06', 'Amy Huang', 2, 21),
(40, 'zli@belizehighschool.edu.bz', 'evieli', '$2y$10$tmSxQz/ywPh3mM3xe1yMcOO3GsWMZKDr6AmPIYZEgiR7pbemD1BPy', 'Evie Li', 2, 21),
(41, 'gtesucum@belizehighschool.edu.bz', 'givanyatesucum', '$2y$10$ZqOGlEEV60f1/n2UQ6JjUuIEJNOvkltuJSojJgQCIIa4TMNlBiG7m', 'Givanya Tesucum', 2, 22),
(42, 'lhunt@belizehighschool.edu.bz', 'liahunt', '$2y$10$WJ/AZlpmgQyOMd/LALBLKeFK9Zh7mJRMxnth8QuDMmT.oJprWu0r2', 'Lia Hunt', 2, 22),
(43, 'jchen@belizehighschool.edu.bz', 'jasonchen', '$2y$10$Q9tSVkjzMLYMRBsClanKZeFN/4CpxaHkf.jn4no0iJwdW4XIWqMly', 'Jason Chen', 2, 11);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `instructor` (`instructor`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `grade_categories`
--
ALTER TABLE `grade_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(254) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grade_categories`
--
ALTER TABLE `grade_categories`
  MODIFY `category_id` int(254) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `user_info_ibfk_2` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollment` (`enrollment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
