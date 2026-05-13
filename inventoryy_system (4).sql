-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 03:54 PM
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
-- Database: `inventoryy_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('approved','archived') DEFAULT 'approved',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `category`, `quantity`, `description`, `status`, `updated_at`) VALUES
(1, 'banana', 'fruits', 7, NULL, 'approved', '2026-05-13 00:34:48'),
(2, 'leo shane fulgencio', '3', 6, NULL, '', '2026-05-13 00:32:30'),
(3, 'guava', 'leo', 3, NULL, 'archived', '2026-05-13 00:32:30'),
(4, 'guava', 'leo', 8, NULL, '', '2026-05-13 00:32:30'),
(5, 'ice cream', 'dessert', 8, NULL, '', '2026-05-13 00:32:30'),
(6, 'Apple', 'Fruits', 30, NULL, '', '2026-05-13 00:32:30'),
(7, 'capuccino', 'coffee', 3, NULL, '', '2026-05-13 00:32:30'),
(8, 'machiato', 'milktea', 10, NULL, 'archived', '2026-05-13 00:32:30'),
(9, 'matcha', 'milktea', 10, NULL, 'approved', '2026-05-13 00:32:30'),
(10, 'mocha', 'milktea', 4, NULL, 'archived', '2026-05-13 02:36:08'),
(11, 'mocha', 'milktea', 4, NULL, 'approved', '2026-05-13 03:07:37'),
(12, 'dark chocolate', 'flavor', 32, NULL, '', '2026-05-13 02:15:49'),
(13, 'dark chocolate', 'flavor', 32, NULL, 'approved', '2026-05-13 03:07:21'),
(14, 'latter', 'sweet', 30, NULL, 'approved', '2026-05-13 03:07:14'),
(15, 'chocolate', 'sweet', 30, NULL, '', '2026-05-13 02:56:10'),
(16, 'Apple', 'Fruits', 30, NULL, 'approved', '2026-05-13 03:02:14'),
(17, 'chocolate kisses', 'flavor', 25, NULL, 'approved', '2026-05-13 03:04:30'),
(18, 'frappe', 'coffee', 23, NULL, 'approved', '2026-05-13 03:12:41'),
(19, 'LEMONADE', 'FRUITY', 4, NULL, 'approved', '2026-05-13 13:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `item_requests`
--

CREATE TABLE `item_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_requests`
--

INSERT INTO `item_requests` (`id`, `name`, `category`, `quantity`, `user_id`, `description`, `status`) VALUES
(12, 'frappe', 'milktea', 23, 6, NULL, 'pending'),
(13, 'frappe', 'milktea', 23, 10, NULL, 'pending'),
(14, 'mocha', 'milktea', 23, 6, NULL, 'pending'),
(16, 'LEMONADE', 'FRUITY', 30, 6, NULL, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','user') DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `role`, `status`) VALUES
(1, 'Super Admin', 'admin', NULL, '$2y$10$RgPfAHdE0WFzwtfqYvZttOTQNtihbeiAzbdWJCT9gpGpgED0/7mH.', 'superadmin', 'active'),
(2, 'angelo', 'angelo', 'angelo@gmail.com', 'angelo1234', 'superadmin', 'active'),
(5, 'Mark', 'Mark', NULL, '$2y$10$aLiM7zEkKCqF2n0nC54mf.d6K9B3rY/kxv4YLVcKjC/ivEqE4iAhm', 'user', 'active'),
(6, 'Melanie S.', 'enchang1', NULL, '$2y$10$XnofXHghCE8fBwy0TTaE6.VC8gcB7Rgn1JLEUAhqITm8Ir3y3njTC', 'admin', 'active'),
(7, 'Melanie S. Ocampo', 'enchang2', NULL, '$2y$10$r1ZG9Vea06kKVentuMAw1upQj8ctxCGEo0f2mER7IsALAVmBMkoJy', 'user', 'active'),
(8, 'Melanie S. Ocampo', 'enchang1', NULL, '$2y$10$PYYacBOWcP.xeQJ5J0iYAueg4I7qvSByBIS1x5XsZI0eJx8TZIMB2', 'user', 'active'),
(9, 'enchang ocampo', 'enchang1', NULL, '$2y$10$WEtF/VkNryf8xOyuy3kUVeGfevgZMZwvim5C0pGgbtzWcNBicvslG', 'user', 'active'),
(10, 'Saif Mohamed', 'saif', NULL, '$2y$10$d79p4cquO0OOwcqftYmm3e0sp/rXG.I1F3enRltP2ZYvFGBBZCCFq', 'user', 'active'),
(11, 'Saif', 'saif2', NULL, '$2y$10$DFug/mNpUAlmnMaHYpZquuRtiY3dLkziluoCq9WpJlFpQrO7br8qu', 'user', 'active'),
(12, 'Melanie S. Ocampo', 'enchang1', NULL, '$2y$10$z4AA03IdLoZpAMyi5JYeXOZC4ulTwCv749CZNcDhl1M5FfKOvT48O', 'user', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_requests`
--
ALTER TABLE `item_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `item_requests`
--
ALTER TABLE `item_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
