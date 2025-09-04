-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 21, 2025 at 03:01 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `instructor_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_id`, `course_name`, `instructor_name`) VALUES
(1, 'html-1', 'Introduction Level Html', 'Sedat Karateke'),
(2, 'css-1', 'Introduction Level Css', 'Fatih Senol'),
(4, 'c++-1', 'Advanced Level C++', 'Mehmet Ozkaya'),
(17, 'c#-1', 'C# For Beginners', 'Mirac Akbulut'),
(18, 'react-1', 'Advanced Level React', 'Eyup Serkan');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `attendance` decimal(5,2) DEFAULT '100.00',
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `surname`, `date_of_birth`, `attendance`, `phone_number`, `email`, `created_at`, `updated_at`) VALUES
(10, '132', 'Ali', 'Mustafa', '2005-03-26', 65.00, '05381234567', 'AliMustafa@example.com', '2025-01-21 13:11:04', '2025-01-21 13:11:04'),
(11, '536', 'Burak', 'Ozkaya', '2002-02-26', 14.00, '05361231243', 'BurakOzkaya@example.com', '2025-01-21 13:12:00', '2025-01-21 13:12:00'),
(13, '923', 'Deniz', 'Karatas', '2000-09-14', 83.00, '05341235636', 'DenizKaratas@example.com', '2025-01-21 13:15:33', '2025-01-21 13:15:33'),
(14, '528', 'Yusuf', 'Bas', '1999-10-17', 95.00, '05351236385', 'YusufBas@example.com', '2025-01-21 13:16:47', '2025-01-21 13:16:47'),
(15, '712', 'Dilek ', 'Aygun', '2005-04-20', 75.00, '05362346637', 'DilekAygun@example.com', '2025-01-21 13:21:32', '2025-01-21 13:21:32'),
(16, '734', 'Ebru', 'Ayaz', '1998-11-17', 72.00, '05394326375', 'EbruAyaz@example.com', '2025-01-21 14:16:05', '2025-01-21 14:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `students_courses_info`
--

DROP TABLE IF EXISTS `students_courses_info`;
CREATE TABLE IF NOT EXISTS `students_courses_info` (
  `student_id` int NOT NULL,
  `course_id` int NOT NULL,
  PRIMARY KEY (`student_id`,`course_id`),
  KEY `fk_course_id` (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_courses_info`
--

INSERT INTO `students_courses_info` (`student_id`, `course_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 4),
(4, 1),
(10, 1),
(11, 1),
(11, 2),
(11, 4),
(13, 1),
(13, 4),
(14, 1),
(15, 1),
(15, 17),
(15, 18),
(16, 1),
(16, 2),
(16, 4),
(16, 17),
(16, 18);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_password_change` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_date`, `last_password_change`) VALUES
(1, 'Ahmet', '$2y$10$Xsjn0SaYkiDe4IkDS2nzfeauXlXYlZxGo3eFmgCeQc0vHvXcD/zlG', '2025-01-14 14:28:12', '2025-01-14 14:28:19'),
(2, 'Abdulwahab', '$2y$10$qdHyE7gpWvVEFsY48nEEk.fAP7OnErgyXCe9YYuLIYf9uV3a8lvq2', '2025-01-14 14:28:12', '2025-01-14 14:28:19'),
(3, 'Emin', '$2y$10$5aT5dLDtB5q49BnrAalUOOaeGf//GdvdtajqBYyzzU6lms79ZlOzu', '2025-01-14 14:28:12', '2025-01-14 18:46:17'),
(4, 'Muhammed', '$2y$10$0vrxefi74RpuY1Wuy88eGuL8eWHw2p8eWgRJQg4Vz98eG0O/.zC0i', '2025-01-14 16:02:37', '2025-01-14 19:08:54'),
(5, 'Omar', '$2y$10$abU6OG7xZWV4COP5l2Slu.Hkjyro1gEDwgWd1Q9vUr5eVHtgnpK/G', '2025-01-15 06:48:14', '2025-01-15 09:48:14'),
(6, 'Muhammedhroub', '$2y$10$MszBm/Qv/kpf/wcZevQ8OeemFymmjZcvt19CkNUw.azqDwta6l0xu', '2025-01-17 16:49:43', '2025-01-17 19:55:01'),
(7, 'MuhammedAli', '$2y$10$OENma.aDu0JkqOi0NUgl8OnCkm6VloQ7fvLMyPNpiZWN1n1jQH892', '2025-01-21 11:49:38', '2025-01-21 14:49:38');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
