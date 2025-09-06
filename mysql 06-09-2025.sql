-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 06, 2025 at 06:12 PM
-- Server version: 8.0.41
-- PHP Version: 8.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vvehirec_inv_pharma`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `avatar` varchar(512) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `avatar`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Zaryab Uddin', 'mzaryabuddin@gmail.com', '03162394467', 'Plot # E180, Lucknow Society, Korangi, Karachi', 'http://localhost/inventory-pharma/uploads/b04aeaee55886a4468ce08bff29bf942.png', 1, '2025-09-06 18:03:12', '2025-09-06 18:03:19', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `id` int NOT NULL,
  `entry_date` datetime NOT NULL,
  `ref_type` varchar(50) NOT NULL,
  `ref_id` int NOT NULL,
  `party_type` enum('customer','supplier') NOT NULL,
  `party_id` int NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit` decimal(12,2) DEFAULT '0.00',
  `credit` decimal(12,2) DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `ref_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `remarks` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ledger`
--

INSERT INTO `ledger` (`id`, `entry_date`, `ref_type`, `ref_id`, `party_type`, `party_id`, `description`, `debit`, `credit`, `created_at`, `ref_no`, `remarks`) VALUES
(1, '2025-09-06 18:03:40', 'purchase', 0, 'supplier', 1, NULL, 30.00, 0.00, '2025-09-06 18:03:40', 'asdv22213', ''),
(2, '2025-09-06 18:04:56', 'purchase', 0, 'supplier', 1, NULL, 45.00, 0.00, '2025-09-06 18:04:56', 'asdv22213', ''),
(3, '2025-09-06 18:05:43', 'purchase_return', 0, 'supplier', 1, NULL, 0.00, 45.00, '2025-09-06 18:05:43', 'asdv22213', ''),
(4, '2025-09-06 18:06:23', 'sales', 0, 'customer', 1, NULL, 0.00, 100.00, '2025-09-06 18:06:23', 'as213', ''),
(5, '2025-09-06 18:07:11', 'sales_return', 0, 'customer', 1, NULL, 100.00, 0.00, '2025-09-06 18:07:11', 'asdv22213', ''),
(6, '2025-09-06 18:08:00', 'payment', 1, 'customer', 1, 'CASH Payment asdv22213', 0.00, 100.00, '2025-09-06 18:08:29', NULL, ''),
(7, '2025-09-06 18:09:00', 'payment', 2, 'supplier', 1, 'CASH Payment asdv22213', 22.00, 0.00, '2025-09-06 18:09:47', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `payment_date` datetime NOT NULL,
  `type` enum('customer','supplier') NOT NULL,
  `party_id` int NOT NULL,
  `mode` enum('cash','cheque') NOT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `note` text,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `ref_no`, `payment_date`, `type`, `party_id`, `mode`, `cheque_no`, `cheque_date`, `amount`, `note`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'asdv22213', '2025-09-06 18:08:00', 'customer', 1, 'cash', NULL, NULL, 100.00, 'asdsad', 3, '2025-09-06 18:08:29', '2025-09-06 18:08:29'),
(2, 'asdv22213', '2025-09-06 18:09:00', 'supplier', 1, 'cash', NULL, NULL, 22.00, 'sasd', 3, '2025-09-06 18:09:47', '2025-09-06 18:09:47');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `image` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `generic` varchar(255) NOT NULL,
  `prices` json NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `supplier_id` int NOT NULL,
  `purchase_date` datetime NOT NULL,
  `items` json NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `ref_no`, `supplier_id`, `purchase_date`, `items`, `total_amount`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'asdv22213', 1, '2025-09-06 18:03:00', '[{\"qty\": 1, \"price\": 30, \"batch_no\": \"A\", \"product_id\": 1}]', 30.00, '2025-09-06 18:03:39', '2025-09-06 18:03:39', 3),
(2, 'asdv22213', 1, '2025-09-06 18:04:00', '[{\"qty\": 1, \"price\": 45, \"batch_no\": \"a\", \"product_id\": 1}]', 45.00, '2025-09-06 18:04:56', '2025-09-06 18:04:56', 3);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` int NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `purchase_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `return_date` datetime NOT NULL,
  `items` json NOT NULL,
  `return_amount` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_returns`
--

INSERT INTO `purchase_returns` (`id`, `ref_no`, `purchase_id`, `supplier_id`, `return_date`, `items`, `return_amount`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'asdv22213', 1, 1, '2025-09-06 18:05:00', '[{\"qty\": 1, \"price\": 45, \"batch_no\": \"A\", \"product_id\": 1}]', 45.00, '2025-09-06 18:05:42', '2025-09-06 18:05:42', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `customer_id` int NOT NULL,
  `sale_date` datetime NOT NULL,
  `items` json NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_no`, `customer_id`, `sale_date`, `items`, `total_amount`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'as213', 1, '2025-09-06 18:06:00', '[{\"qty\": 1, \"price\": 100, \"batch_no\": \"A\", \"product_id\": 1}]', 100.00, '2025-09-06 18:06:22', '2025-09-06 18:06:22', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

CREATE TABLE `sales_returns` (
  `id` int NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `sale_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `return_date` datetime NOT NULL,
  `items` json NOT NULL,
  `return_amount` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales_returns`
--

INSERT INTO `sales_returns` (`id`, `ref_no`, `sale_id`, `customer_id`, `return_date`, `items`, `return_amount`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'asdv22213', 1, 1, '2025-09-06 18:07:00', '[{\"qty\": 1, \"price\": 100, \"batch_no\": \"A\", \"product_id\": 1}]', 100.00, '2025-09-06 18:07:11', '2025-09-06 18:07:11', 3);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `batch_no` varchar(100) NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `last_cost` decimal(12,2) DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `product_id`, `batch_no`, `qty`, `last_cost`, `updated_at`) VALUES
(1, 1, 'A', 1, 45.00, '2025-09-06 18:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `logo` varchar(512) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `address`, `logo`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Zaryab Uddin', 'mzaryabuddin@gmail.com', '03162394467', 'Plot # E180, Lucknow Society, Korangi, Karachi', 'http://localhost/inventory-pharma/uploads/58b8bc70eeab2ee31ff941baed815e0e.png', 1, '2025-09-06 18:02:48', '2025-09-06 18:02:58', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `phone` varchar(255) DEFAULT NULL,
  `expire_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `profile_picture`, `created_at`, `updated_at`, `status`, `phone`, `expire_at`) VALUES
(3, 'mzaryabuddin@gmail.com', '0192023a7bbd73250516f069df18b500', 'Zaryab', 'Uddin', 'http://localhost/inventory-pharma/uploads/f956e7834f904963736b08e9d7b0c0c4.png', '2025-08-31 18:25:54', '2025-09-06 06:30:30', 1, '03162394467', '2025-09-30 18:25:54'),
(4, 'mahnoorkhanmk125@gmail.com', 'cb7f79f789a6e82e2d5e5cdab003e749', 'Mahnoor', 'Khan', 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740&q=80', '2025-08-31 19:32:56', '2025-08-31 14:33:55', 1, '03162394467', '2025-09-30 19:32:56'),
(5, 'jawaidjhony37@gmail.com', 'ae61b8e1c2540340d958d85ac5f32aeb', 'Jawaid', 'Bhatti', 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740&q=80', '2025-09-01 11:59:13', '2025-09-01 06:59:13', 1, '03411296924', '2025-10-01 11:59:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_stock_product_batch` (`product_id`,`batch_no`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
