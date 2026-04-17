-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2026 at 10:23 AM
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
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `book_no` varchar(50) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `available` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `book_no`, `genre`, `title`, `author`, `isbn`, `category`, `quantity`, `available`, `created_at`) VALUES
(1, 'Book no 1', 'History', 'Nahuli meh tanghene', 'Jose RIZAL', NULL, NULL, 100, 102, '2026-04-12 07:14:38'),
(2, '38912674', 'Mystery', 'Aklat ng Sabika', 'Jose RIZAL', NULL, NULL, 22, 23, '2026-04-12 07:50:21'),
(3, '1234324', 'Science', 'Science and Techology', 'Jergens', NULL, NULL, 12, 13, '2026-04-12 17:34:28');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('borrowed','returned','overdue') DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`id`, `user_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`) VALUES
(1, 3, 1, '2026-04-12', '2026-04-26', '2026-04-12', 'returned'),
(2, 3, 1, '2026-04-12', '2026-04-26', '2026-04-12', 'returned'),
(3, 3, 1, '2026-04-12', '2026-04-26', '2026-04-13', 'returned'),
(4, 3, 2, '2026-04-12', '2026-04-26', '2026-04-13', 'returned'),
(5, 3, 3, '2026-04-13', '2026-04-27', '2026-04-13', 'returned'),
(6, 3, 3, '2026-04-13', '2026-04-27', '2026-04-13', 'returned');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `book_no` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `borrow_date` date NOT NULL,
  `return_date` date NOT NULL,
  `agreed` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` varchar(255) DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`id`, `user_id`, `book_id`, `book_no`, `purpose`, `borrow_date`, `return_date`, `agreed`, `status`, `admin_note`, `requested_at`) VALUES
(1, 3, 1, 'Book no 1', 'Study', '2026-04-12', '2026-04-26', 1, 'approved', '', '2026-04-12 07:33:47'),
(2, 3, 1, 'Book no 1', 'Study', '2026-04-12', '2026-04-26', 1, 'approved', '', '2026-04-12 07:47:45'),
(3, 3, 3, '1234324', 'Study', '2026-04-13', '2026-04-27', 1, 'approved', 'Tarunga og Uli ang Libro', '2026-04-12 17:35:55'),
(4, 3, 3, '1234324', 'Study', '2026-04-13', '2026-04-27', 1, 'approved', 'Section B. Taas og buhok na bata', '2026-04-12 18:35:52'),
(5, 8, 2, '38912674', 'syidy', '2026-09-17', '2026-10-01', 1, 'pending', NULL, '2026-04-17 07:28:17');

-- --------------------------------------------------------

--
-- Table structure for table `return_requests`
--

CREATE TABLE `return_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_record_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` varchar(255) DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_requests`
--

INSERT INTO `return_requests` (`id`, `user_id`, `book_id`, `borrow_record_id`, `reason`, `status`, `admin_note`, `requested_at`) VALUES
(1, 3, 2, 4, 'Good', 'approved', '', '2026-04-12 07:55:41'),
(2, 3, 1, 3, 'Guba na', 'approved', '', '2026-04-12 07:55:48'),
(3, 3, 3, 6, 'nabasa na nako', 'approved', '', '2026-04-12 18:38:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `reset_token`, `reset_expiry`) VALUES
(1, 'Administrator', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-04-12 03:03:44', NULL, NULL),
(2, 'chen', 'ChenGarcia@gmail.com', '$2y$10$SLzcAO9a8R6mcvwg/vSwS.U4qP4BtFVGqMJRqweI7fXFylooDQeOW', 'user', '2026-04-12 03:38:35', NULL, NULL),
(3, 'Lorenze kevin A. Tinggoy', 'renzezoiizitro08@gmail.com', '$2y$10$lDVcOamOX9vwhYpcPLWzQ.H5Z8z3Qgyoy8HunSU5iV5dyon21jd4G', 'user', '2026-04-12 07:08:14', NULL, NULL),
(4, 'Nick Aaron Morales', 'nickaronmorales@gmail.com', 'qwerty123', 'user', '2026-04-16 08:09:08', NULL, NULL),
(5, 'Chico Banguit', 'chicobanguit@gmail.com', '1234', 'user', '2026-04-16 08:11:09', NULL, NULL),
(6, 'Chicco Banguit', 'banguitchicco4@gmail.com', '$2y$10$8nFLDw1FTM4NDqqmnaG06uGSGRvli5hvBgWIhr08oSXJylqVGzID6', 'user', '2026-04-17 06:29:19', '997979c0b779af2477254459b6db490d2036196260303b5e2c920a151f3bf87456f6f8638df23c9a73b8f004f7c70d97dba9', '2026-04-17 11:00:29'),
(7, 'MARTON', 'marton@gmail.com', '$2y$10$S1quSW6fNl1zHzzgoyD7jeDhfu8T0.UwgBkJ8dIxgL./KJhKt193u', 'user', '2026-04-17 07:22:47', NULL, NULL),
(8, 'marton bernaldezzz', 'martonn@gmail.com', '$2y$10$/u0zXHQyMS6Y4oGjMtevS.CnjWwfHTH8.BljKlt3.6Gc1UAc/fcGG', 'user', '2026-04-17 07:25:31', 'a55223bee5283b74ed34a4122e515cb6aab9db65db12128a320664dc304d8846d7b0ad6076eecc0ddac6770d171563b1d7d6', '2026-04-17 10:56:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_no` (`book_no`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `borrow_record_id` (`borrow_record_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrow_records_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrow_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD CONSTRAINT `return_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `return_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `return_requests_ibfk_3` FOREIGN KEY (`borrow_record_id`) REFERENCES `borrow_records` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
