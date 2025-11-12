-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 16, 2025 at 07:54 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `college_schedule`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int NOT NULL,
  `year_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year_name`) VALUES
(1, '2025'),
(3, '2026');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'admin', '$2y$10$FXagzINNy12r6Pxl3b344enlAyLqQlKI3o9jBe6LveopGCB6IInM6');

-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

CREATE TABLE `classroom` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `head_teacher` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `classroom`
--

INSERT INTO `classroom` (`id`, `name`, `location`, `head_teacher`) VALUES
(1, '1', '3-й поверх ', 1),
(3, '11', '2-й поверх', 2),
(4, '26', '3-й поверх (по сходам біля вахти)', 3),
(5, '28', '3-й поверх (по сходам біля вахти)', 4),
(6, '23', '2-й поверх (по сходам біля вахти)\r\n', 6);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `course_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`) VALUES
(6, 'EM-1'),
(5, 'IM-3'),
(12, 'ІЕМ-1'),
(13, 'ІЕМ-2'),
(14, 'ІЕМ-3'),
(15, 'ІЕМ-4'),
(17, 'ОФ-1'),
(18, 'ОФ-2'),
(19, 'ОФ-3'),
(20, 'ОФ-4'),
(1, 'ПМ-1'),
(2, 'ПМ-2'),
(3, 'ПМ-3'),
(4, 'ПМ-4'),
(8, 'ХБ-1'),
(9, 'ХБ-2'),
(10, 'ХБ-3'),
(11, 'ХБ-4');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_number`
--

CREATE TABLE `lesson_number` (
  `id` int NOT NULL,
  `lesson_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lesson_number`
--

INSERT INTO `lesson_number` (`id`, `lesson_number`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(6, 5),
(7, 6);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int NOT NULL,
  `academic_year_id` int DEFAULT NULL,
  `group_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  `classroom_id` int DEFAULT NULL,
  `lesson_number_id` int DEFAULT NULL,
  `day_of_week` enum('Понеділок','Вівторок','Середа','Четвер','П’ятниця','Субота') DEFAULT NULL,
  `lesson_type` enum('Повна','Підгрупа','Чисельник','Знаменник') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `academic_year_id`, `group_id`, `subject_id`, `teacher_id`, `classroom_id`, `lesson_number_id`, `day_of_week`, `lesson_type`) VALUES
(4, 1, 4, 2, 3, 4, 4, 'Вівторок', 'Повна'),
(5, 1, 3, 3, 1, 1, 1, 'Понеділок', 'Повна'),
(6, 1, 3, 7, 4, 5, 3, 'Вівторок', 'Повна'),
(7, 1, 4, 10, 5, 3, 3, 'Середа', 'Повна'),
(8, 1, 1, 11, 6, 6, 2, 'П’ятниця', 'Повна'),
(9, 1, 3, 12, 14, 6, 1, 'Вівторок', 'Повна'),
(10, 1, 3, 12, 14, 6, 1, 'Вівторок', 'Повна'),
(11, 1, 3, 12, 14, 6, 2, 'Понеділок', 'Повна'),
(12, 1, 3, 7, 4, 5, 1, 'П’ятниця', 'Повна'),
(13, 1, 3, 7, 4, 5, 1, 'П’ятниця', 'Повна'),
(14, 1, 3, 7, 4, 5, 1, 'П’ятниця', 'Повна'),
(15, 1, 3, 7, 4, 5, 1, 'П’ятниця', 'Повна'),
(16, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(17, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(18, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(19, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(20, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(21, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(22, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна'),
(23, 1, 1, 11, 6, 6, 2, 'Четвер', 'Повна');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacher_id` int DEFAULT NULL,
  `classroom_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `name`, `teacher_id`, `classroom_id`) VALUES
(2, 'Політологія', 3, 4),
(3, 'ООП', 1, 1),
(7, 'Основи філософських знань', 4, 5),
(10, 'Web дизайн', 5, 3),
(11, 'Математика', 6, 6),
(12, 'Дискретна Математика', 14, 6);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `full_name`) VALUES
(4, 'Болєлова Ірина Анатоліївна'),
(14, 'Коренець Ганна Юріївна'),
(1, 'Огійчук Віктор Олександрович'),
(7, 'Осадча Лариса Костянтинівна'),
(6, 'Петруніна Тетяна Йосипівна'),
(2, 'Роман Олександрович Довніч'),
(15, 'Терешкович Дмитро Володимирович'),
(5, 'Шагоферов Сергій '),
(3, 'Шваєнко Алла Юріївна');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, '123', '$2y$10$8NOAWF7WBK.kf8SvR2iyXO/tdLPerwWs1OixTtEr7GEpfCDCzWnLq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year_name` (`year_name`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_head_teacher` (`head_teacher`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_number` (`course_number`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `lesson_number`
--
ALTER TABLE `lesson_number`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_number` (`lesson_number`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `classroom_id` (`classroom_id`),
  ADD KEY `lesson_number_id` (`lesson_number_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `classrooms_id` (`classroom_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `full_name` (`full_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `classroom`
--
ALTER TABLE `classroom`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `lesson_number`
--
ALTER TABLE `lesson_number`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classroom`
--
ALTER TABLE `classroom`
  ADD CONSTRAINT `fk_head_teacher` FOREIGN KEY (`head_teacher`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `schedule_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`),
  ADD CONSTRAINT `schedule_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `schedule_ibfk_5` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `schedule_ibfk_6` FOREIGN KEY (`lesson_number_id`) REFERENCES `lesson_number` (`id`);

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `subject_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
