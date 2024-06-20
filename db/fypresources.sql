-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2024 at 10:57 AM
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
-- Database: `fypresources`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `faculty_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_name`) VALUES
(1, 'FCI'),
(2, 'FCM'),
(3, 'FOE'),
(4, 'FOM'),
(5, 'FCA'),
(6, 'LIFE');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `faq_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer_text` text NOT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `feedback_text` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `feedback_text`, `submitted_at`) VALUES
(6, 2, 'hi', '2024-06-13 18:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchasedresources`
--

CREATE TABLE `purchasedresources` (
  `purchase_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchasedresources`
--

INSERT INTO `purchasedresources` (`purchase_id`, `user_id`, `resource_id`, `purchase_date`) VALUES
(1, 2, 1, '2024-06-13 15:15:23'),
(2, 2, 5, '2024-06-13 15:33:27'),
(3, 2, 1, '2024-06-13 15:38:53'),
(4, 2, 3, '2024-06-13 15:38:53'),
(5, 2, 5, '2024-06-13 15:43:19'),
(6, 2, 1, '2024-06-13 15:46:28'),
(7, 2, 5, '2024-06-13 15:52:20'),
(8, 2, 1, '2024-06-13 23:32:01'),
(10, 11, 1, '2024-06-14 07:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `pending_acceptance` tinyint(1) DEFAULT 1,
  `faculty_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cover_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `user_id`, `title`, `description`, `file_path`, `pending_acceptance`, `faculty_id`, `created_at`, `price`, `cover_picture`) VALUES
(1, 2, 'TEST', 'TEST DIC', 'profilePicture\\Blank diagram.png', 0, 1, '2024-06-13 07:26:44', 75.70, 'profilePicture\\Blank diagram.png'),
(2, 2, 'TEST2', 'TEST DIC2', 'profilePicture\\Blank diagram.png', 0, 1, '2024-06-13 07:26:44', 50.00, 'profilePicture\\Blank diagram.png'),
(3, 2, 'TEST POST', 'TEST POST DECR', 'resources/Assignment.docx', 0, 1, '2024-06-13 09:06:00', 100.00, 'coverPictures/Blank diagram (1).png'),
(5, 2, 'gabaaaaaaaaaaaaar', 'gabaaaaaaaaaaaaar', 'resources/1221302092_CCS6214_tutorialAssignment_04.pdf', 0, 1, '2024-06-13 13:11:09', 333.00, 'coverPictures/Blank diagram (1).png'),
(7, 2, 'Test ', 'Discord', 'resources/Wk4_OpenSSL_Lab1.docx', 1, 5, '2024-06-13 23:21:49', 622.00, 'coverPictures/Blank diagram (1).png');

-- --------------------------------------------------------

--
-- Table structure for table `userquestions`
--

CREATE TABLE `userquestions` (
  `question_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `asked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `userquestions`
--

INSERT INTO `userquestions` (`question_id`, `user_id`, `question_text`, `asked_at`) VALUES
(1, 2, 'heelo', '2024-06-13 18:39:08'),
(2, 2, 'aha', '2024-06-13 18:42:05'),
(3, 2, 'aha', '2024-06-13 22:10:35'),
(4, 2, 'hello \r\n', '2024-06-13 22:10:44'),
(5, 2, 'hello bro ', '2024-06-13 22:11:30'),
(6, 2, 'Test ', '2024-06-13 22:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `profile_picture`, `is_admin`, `created_at`) VALUES
(1, 'youssef', '$2y$10$eLGL.7/Boz8BqL1jSxMgEuRfoPZSFxR3GggSDudnR2.HZ4zJo878S', 'yousseffathyalsakar@gmail.com', 'profilePicture/App Wireframing - FAQ page.jpg', 1, '2024-06-13 06:17:27'),
(2, 'thabit', '$2y$10$gYvpC.cdzvLbeQt4WDVyHOw/0z/DzP7FBowj/GKcPKDYL0uGbuUJW', '1221302092@student.mmu.edu.my', 'profilePicture/Blank diagram (1).png', 0, '2024-06-13 06:23:14'),
(11, 'baraa', '$2y$10$zZWstE7vI.C9cq4u3VYut.Be75wbUohcsokWcK1qKEx1/rXu5JE9u', 'baraa@gmail.com', 'profilePicture/super/default.png', 0, '2024-06-14 06:57:19'),
(12, 'kawsar', '$2y$10$NUMbX1wwxsDIzb41rkeglORxsejg4iiyQGlyqeOPGwgRtLS1hn2kS', '1221302092@student.mmu.edu.mo', 'profilePicture/super/default.png', 0, '2024-06-14 08:22:00'),
(13, 'kawsar1', '$2y$10$/s.XJNHoX3gezqTTnu8U6.CyXGZMr3KkVCD4WhJtk55VHJRnnYxoW', '1221302092@student.mmu.edu', 'profilePicture/Blank diagram (1).png', 0, '2024-06-14 08:23:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `idx_cart_user_id` (`user_id`),
  ADD KEY `idx_cart_resource_id` (`resource_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`faq_id`),
  ADD KEY `idx_faq_question_id` (`question_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `idx_feedback_user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_notifications_user_id` (`user_id`),
  ADD KEY `notifications_ibfk_2` (`resource_id`);

--
-- Indexes for table `purchasedresources`
--
ALTER TABLE `purchasedresources`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `idx_purchased_resources_user_id` (`user_id`),
  ADD KEY `idx_purchased_resources_resource_id` (`resource_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `idx_resources_user_id` (`user_id`),
  ADD KEY `idx_resources_faculty_id` (`faculty_id`);

--
-- Indexes for table `userquestions`
--
ALTER TABLE `userquestions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `idx_user_questions_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchasedresources`
--
ALTER TABLE `purchasedresources`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `userquestions`
--
ALTER TABLE `userquestions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE;

--
-- Constraints for table `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `userquestions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchasedresources`
--
ALTER TABLE `purchasedresources`
  ADD CONSTRAINT `purchasedresources_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchasedresources_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `userquestions`
--
ALTER TABLE `userquestions`
  ADD CONSTRAINT `userquestions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
