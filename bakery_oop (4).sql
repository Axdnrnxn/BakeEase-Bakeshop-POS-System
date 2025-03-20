-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2025 at 02:09 PM
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
-- Database: `bakery_oop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `status` enum('pending','processing','Ready for pickup','Ready for delivery','Out for Delivery','Delivered','Cancelled') NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_type` enum('delivery','pickup') NOT NULL DEFAULT 'pickup',
  `delivery_address` text DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `pickup_time` datetime DEFAULT NULL,
  `final_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_email`, `product_id`, `quantity`, `total_price`, `payment_method`, `address`, `status`, `order_date`, `order_type`, `delivery_address`, `customer_name`, `pickup_time`, `final_total`) VALUES
(169, 43, 'Staff@gmail.com', 0, 0, 0.00, 'cod', 'N/A', 'Delivered', '2025-03-11 15:03:38', 'pickup', '', NULL, '2025-03-12 07:13:00', 600.00),
(170, 43, 'Staff@gmail.com', 0, 0, 0.00, 'cod', 'Zone 2 Lapaz 2 kolambog Lapasan, CDO', 'Delivered', '2025-03-11 15:13:03', 'delivery', 'Zone 2 Lapaz 2 kolambog Lapasan, CDO', NULL, '0000-00-00 00:00:00', 180.00),
(171, 43, 'Staff@gmail.com', 0, 0, 0.00, '240', 'N/A', 'Delivered', '2025-03-12 03:28:09', '', '', NULL, '0000-00-00 00:00:00', 240.00),
(172, 43, 'Staff@gmail.com', 0, 0, 0.00, '85', 'N/A', 'Delivered', '2025-03-13 15:34:30', '', '', NULL, '0000-00-00 00:00:00', 85.00),
(175, 45, 'jaylyn@gmail.com', 0, 0, 0.00, '60', 'N/A', 'pending', '2025-03-14 15:30:32', '', '', NULL, '0000-00-00 00:00:00', 60.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(144, 169, 163, 1),
(145, 169, 162, 1),
(146, 170, 161, 3),
(147, 171, 166, 2),
(148, 172, 159, 1),
(151, 175, 161, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `featured`, `quantity`) VALUES
(158, 'Soufflé Cake', 'a baked egg dish that\\\'s light and airy, and can be sweet or savory', 140.00, 'Souffle.jpg_1.jpg', NULL, 1, 0),
(159, 'Croissant', 'a buttery, flaky, crescent-shaped pastry that originated in Austria but is made using French yeast-leavened dough', 85.00, 'croissant.jpg_1.jpg', NULL, 0, 5),
(161, 'Soft Cake', 'A soft cake good for people who have problems swallowing food', 60.00, 'Soft.jpg_1.jpg', NULL, 0, 4),
(162, 'Brown Cake', 'Unbelievably tender brown butter cake made with brown sugar. This everyday cake is made in one bowl and with just a whisk.', 450.00, 'browncake.jpg_1.jpg', NULL, 0, 3),
(163, 'Macaron', 'a small round cake with a meringue-like consistency, made with egg whites, sugar, and powdered almonds and consisting of two halves sandwiching a creamy filling.', 150.00, 'macarons.jpg_1.jpg', NULL, 1, 3),
(164, 'Matcha Cheese Cake', 'This matcha japanese cheesecake is the perfect blend of creaminess and a light, earthy flavor from the matcha.', 340.00, 'matcha.jpg_2.jpg', NULL, 0, 4),
(166, 'Brown Cake', 'asd', 120.00, 'browncake.jpg_1.jpg_1.jpg', NULL, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `isAdmin` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `reset_token`, `isAdmin`) VALUES
(0, 'Ad min', 'admin@gmail.com', '$2y$10$f2cx43y8E/30O6hzst203eigkcfmyZ8ndhVC4fPgj/sAD9ft6vAnW', NULL, 0),
(43, 'Staff', 'Staff@gmail.com', '$2y$10$fnxVtvb1onoSkSwKu1mHpeGYSAvB3jAERxr1kflddvUDzE.Ye2/i.', NULL, 1),
(45, 'Jaylyn Rose Sta.Rita', 'jaylyn@gmail.com', '$2y$10$1iwLtqzT0AzIFge8/pYCeuFfyZi/h1bVGgH.oVcySFs8ZmmGNE0SW', NULL, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
