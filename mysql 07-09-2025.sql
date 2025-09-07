-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 07, 2025 at 01:23 PM
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
(1, 'Zaryab Uddin', 'mzaryabuddin@gmail.com', '03162394467', 'Plot # E180, Lucknow Society, Korangi, Karachi', 'http://localhost/inventory-pharma/uploads/b04aeaee55886a4468ce08bff29bf942.png', 1, '2025-09-06 18:03:12', '2025-09-06 18:03:19', 3),
(2, 'sadsadsa', 'sadsa@asd.aasd', '56465465', 'sahdjkashdjkashj', 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 1, '2025-09-06 20:29:54', '2025-09-06 20:29:54', 6),
(3, 'Kamil', 'kamilyk@gmail.com', '81273981273', 'asdasdasdas', 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 1, '2025-09-06 21:59:39', '2025-09-06 21:59:39', 6);

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
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `ref_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `remarks` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ledger`
--

INSERT INTO `ledger` (`id`, `entry_date`, `ref_type`, `ref_id`, `party_type`, `party_id`, `description`, `debit`, `credit`, `created_by`, `created_at`, `ref_no`, `remarks`) VALUES
(1, '2025-09-06 18:03:40', 'purchase', 0, 'supplier', 1, NULL, 30.00, 0.00, 0, '2025-09-06 18:03:40', 'asdv22213', ''),
(2, '2025-09-06 18:04:56', 'purchase', 0, 'supplier', 1, NULL, 45.00, 0.00, 0, '2025-09-06 18:04:56', 'asdv22213', ''),
(3, '2025-09-06 18:05:43', 'purchase_return', 0, 'supplier', 1, NULL, 0.00, 45.00, 0, '2025-09-06 18:05:43', 'asdv22213', ''),
(4, '2025-09-06 18:06:23', 'sales', 0, 'customer', 1, NULL, 0.00, 100.00, 0, '2025-09-06 18:06:23', 'as213', ''),
(5, '2025-09-06 18:07:11', 'sales_return', 0, 'customer', 1, NULL, 100.00, 0.00, 0, '2025-09-06 18:07:11', 'asdv22213', ''),
(6, '2025-09-06 18:08:00', 'payment', 1, 'customer', 1, 'CASH Payment asdv22213', 0.00, 100.00, 0, '2025-09-06 18:08:29', NULL, ''),
(7, '2025-09-06 18:09:00', 'payment', 2, 'supplier', 1, 'CASH Payment asdv22213', 22.00, 0.00, 0, '2025-09-06 18:09:47', NULL, ''),
(8, '2025-09-06 21:10:49', 'purchase', 0, 'supplier', 2, NULL, 123.00, 0.00, 0, '2025-09-06 21:10:49', '1231231', ''),
(9, '2025-09-06 21:20:49', 'purchase_return', 0, 'supplier', 1, NULL, 0.00, 120.00, 0, '2025-09-06 21:20:49', '1231231', ''),
(10, '2025-09-06 21:31:48', 'purchase', 0, 'supplier', 2, NULL, 30000.00, 0.00, 0, '2025-09-06 21:31:48', '1234', ''),
(11, '2025-09-06 21:32:38', 'purchase_return', 0, 'supplier', 2, NULL, 0.00, 12500.00, 0, '2025-09-06 21:32:38', '1234', ''),
(12, '2025-09-06 21:41:34', 'sales', 0, 'customer', 2, NULL, 0.00, 28000.00, 0, '2025-09-06 21:41:34', '1122333', ''),
(13, '2025-09-06 21:46:54', 'purchase_return', 0, 'supplier', 2, NULL, 0.00, 1230.00, 0, '2025-09-06 21:46:54', '1234', ''),
(14, '2025-09-06 21:54:01', 'sales_return', 0, 'customer', 2, NULL, 23000.00, 0.00, 0, '2025-09-06 21:54:01', '1122333', ''),
(15, '2025-09-06 21:56:18', 'sales_return', 0, 'customer', 2, NULL, 0.00, 23000.00, 0, '2025-09-06 21:56:18', '1122333', ''),
(16, '2025-09-06 21:56:19', 'sales_return', 0, 'customer', 2, NULL, 22770.00, 0.00, 0, '2025-09-06 21:56:19', '1122333', ''),
(17, '2025-09-06 22:00:38', 'purchase', 0, 'supplier', 3, NULL, 10500.00, 0.00, 0, '2025-09-06 22:00:38', '779988', ''),
(18, '2025-09-06 22:03:49', 'purchase', 0, 'supplier', 3, NULL, 0.00, 10500.00, 0, '2025-09-06 22:03:49', '779988', ''),
(19, '2025-09-06 22:03:50', 'purchase', 0, 'supplier', 3, NULL, 10000.00, 0.00, 0, '2025-09-06 22:03:50', '779988', ''),
(20, '2025-09-06 22:04:45', 'purchase_return', 0, 'supplier', 3, NULL, 0.00, 10000.00, 0, '2025-09-06 22:04:45', '779988', ''),
(21, '2025-09-06 22:07:11', 'purchase_return', 0, 'supplier', 3, NULL, 10000.00, 0.00, 0, '2025-09-06 22:07:11', '779988', ''),
(22, '2025-09-06 22:07:12', 'purchase_return', 0, 'supplier', 3, NULL, 0.00, 2500.00, 0, '2025-09-06 22:07:12', '779988', ''),
(23, '2025-09-06 22:11:30', 'sales', 0, 'customer', 3, NULL, 0.00, 6300.00, 0, '2025-09-06 22:11:30', '667722', ''),
(24, '2025-09-06 22:13:18', 'sales_return', 0, 'customer', 3, NULL, 1000.00, 0.00, 0, '2025-09-06 22:13:18', '779988', ''),
(25, '2025-09-06 22:14:39', 'sales_return', 0, 'customer', 3, NULL, 0.00, 1000.00, 0, '2025-09-06 22:14:39', '779988', ''),
(26, '2025-09-06 22:14:41', 'sales_return', 0, 'customer', 3, NULL, 0.00, 0.00, 0, '2025-09-06 22:14:41', '779988', ''),
(27, '2025-09-07 12:32:20', 'purchase', 0, 'supplier', 1, NULL, 1.00, 0.00, 3, '2025-09-07 12:32:20', 'asdv22213', '');

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
(1, 'asdv22213', '2025-09-06 18:08:00', 'customer', 1, 'cash', NULL, NULL, 100.00, 'asdsad', 6, '2025-09-06 18:08:29', '2025-09-06 18:08:29'),
(2, 'asdv22213', '2025-09-06 18:09:00', 'supplier', 1, 'cash', NULL, NULL, 22.00, 'sasd', 6, '2025-09-06 18:09:47', '2025-09-06 18:09:47');

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

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `image`, `product_name`, `generic`, `prices`, `created_at`, `updated_at`, `created_by`, `status`) VALUES
(1, 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 'Prduct', 'asd', '[{\"tp\": 1, \"mrp\": 200, \"dated\": \"2025-09-06 16:47:40\"}, {\"tp\": 1, \"mrp\": 100, \"dated\": \"2025-09-06 16:47:40\"}]', '2025-09-06 20:28:44', '2025-09-06 21:47:43', 6, 1),
(2, 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 'Panadol', 'Paracetamol', '[{\"tp\": 10, \"mrp\": 40, \"dated\": \"2025-09-06 16:58:55\"}]', '2025-09-06 21:58:57', '2025-09-06 21:58:57', 6, 1),
(3, 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 'Panadol', 'Paracetamol', '[{\"tp\": 11, \"mrp\": 11, \"dated\": \"2025-09-07 07:31:59\"}]', '2025-09-07 12:31:59', '2025-09-07 12:31:59', 3, 1);

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
(3, '1231231', 2, '2025-09-12 21:11:00', '[{\"qty\": 1, \"price\": 123, \"batch_no\": \"123\", \"product_id\": 1}]', 123.00, '2025-09-06 21:10:48', '2025-09-06 21:10:48', 6),
(4, '1234', 2, '2025-09-06 21:31:00', '[{\"qty\": 150, \"price\": 200, \"batch_no\": \"009\", \"product_id\": 1}]', 30000.00, '2025-09-06 21:31:46', '2025-09-06 21:31:46', 6),
(5, '779988', 3, '2025-09-06 21:59:00', '[{\"qty\": 110, \"price\": 50, \"batch_no\": \"A723\", \"product_id\": 2}, {\"qty\": 100, \"price\": 45, \"batch_no\": \"A724\", \"product_id\": 2}]', 10000.00, '2025-09-06 22:00:36', '2025-09-06 22:03:48', 6),
(6, 'asdv22213', 1, '2025-09-07 12:32:00', '[{\"qty\": 1, \"price\": 1, \"batch_no\": \"A\", \"product_id\": 3}]', 1.00, '2025-09-07 12:32:19', '2025-09-07 12:32:19', 3);

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
(1, 'asdv22213', 1, 1, '2025-09-06 18:05:00', '[{\"qty\": 1, \"price\": 45, \"batch_no\": \"A\", \"product_id\": 1}]', 45.00, '2025-09-06 18:05:42', '2025-09-06 18:05:42', 6),
(2, '1231231', 3, 1, '2025-09-20 21:21:00', '[{\"qty\": 1, \"price\": 120, \"batch_no\": \"123\", \"product_id\": 1}]', 120.00, '2025-09-06 21:20:48', '2025-09-06 21:20:48', 6),
(3, '1234', 4, 2, '2025-09-07 21:32:00', '[{\"qty\": 50, \"price\": 250, \"batch_no\": \"009\", \"product_id\": 1}]', 12500.00, '2025-09-06 21:32:37', '2025-09-06 21:32:37', 6),
(4, '1234', 4, 2, '2025-09-05 21:46:00', '[{\"qty\": 10, \"price\": 123, \"batch_no\": \"009\", \"product_id\": 1}]', 1230.00, '2025-09-06 21:46:52', '2025-09-06 21:46:52', 6),
(5, '779988', 5, 3, '2025-09-07 22:04:00', '[{\"qty\": 50, \"price\": 50, \"batch_no\": \"A723\", \"product_id\": 2}]', 2500.00, '2025-09-06 22:04:44', '2025-09-06 22:07:10', 6);

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
(1, 'as213', 1, '2025-09-06 18:06:00', '[{\"qty\": 1, \"price\": 100, \"batch_no\": \"A\", \"product_id\": 1}]', 100.00, '2025-09-06 18:06:22', '2025-09-06 18:06:22', 3),
(2, '1122333', 2, '2025-09-08 21:40:00', '[{\"qty\": 100, \"price\": 280, \"batch_no\": \"009\", \"product_id\": 1}]', 28000.00, '2025-09-06 21:41:33', '2025-09-06 21:41:33', 6),
(3, '667722', 3, '2025-09-13 22:11:00', '[{\"qty\": 70, \"price\": 90, \"batch_no\": \"A723\", \"product_id\": 2}]', 6300.00, '2025-09-06 22:11:29', '2025-09-06 22:11:29', 6);

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
(1, 'asdv22213', 1, 1, '2025-09-06 18:07:00', '[{\"qty\": 1, \"price\": 100, \"batch_no\": \"A\", \"product_id\": 1}]', 100.00, '2025-09-06 18:07:11', '2025-09-06 18:07:11', 3),
(2, '1122333', 2, 2, '2025-09-07 21:53:00', '[{\"qty\": 99, \"price\": 230, \"batch_no\": \"009\", \"product_id\": 1}]', 22770.00, '2025-09-06 21:53:59', '2025-09-06 21:56:17', 6),
(3, '779988', 3, 3, '2025-09-14 22:12:00', '[{\"qty\": 0, \"price\": 100, \"batch_no\": \"A723\", \"product_id\": 2}]', 0.00, '2025-09-06 22:13:17', '2025-09-06 22:14:38', 6);

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
  `created_by` int NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `product_id`, `batch_no`, `qty`, `last_cost`, `created_by`, `updated_at`) VALUES
(1, 1, 'A', 1, 45.00, 0, '2025-09-06 18:07:11'),
(2, 1, '123', 0, 123.00, 0, '2025-09-06 21:20:48'),
(3, 1, '009', 89, 200.00, 0, '2025-09-06 21:56:18'),
(4, 2, 'A723', -10, 50.00, 0, '2025-09-06 22:14:41'),
(5, 2, 'A724', 100, 45.00, 0, '2025-09-06 22:03:50'),
(6, 3, 'A', 1, 1.00, 3, '2025-09-07 12:32:20');

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
(1, 'Zaryab Uddin', 'mzaryabuddin@gmail.com', '03162394467', 'Plot # E180, Lucknow Society, Korangi, Karachi', 'http://localhost/inventory-pharma/uploads/58b8bc70eeab2ee31ff941baed815e0e.png', 1, '2025-09-06 18:02:48', '2025-09-06 18:02:58', 3),
(2, 'hjksahdjksa', 'asjkhdjksa@hj.cc', '5456456464', 'sadsada', 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 1, '2025-09-06 20:29:32', '2025-09-06 20:29:32', 6),
(3, 'Zafar', 'zafar@gmail.com', '109238912380', 'asdhkjasdhjkasdhksja', 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg', 1, '2025-09-06 21:59:20', '2025-09-06 21:59:20', 6);

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
(5, 'jawaidjhony37@gmail.com', 'ae61b8e1c2540340d958d85ac5f32aeb', 'Jawaid', 'Bhatti', 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740&q=80', '2025-09-01 11:59:13', '2025-09-01 06:59:13', 1, '03411296924', '2025-10-01 11:59:13'),
(6, 'm.ziaroshan7@gmail.com', 'ccb748c3e4265ad4077232b8bfdd1557', 'Zia', 'Roshan', 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740&q=80', '2025-09-06 20:23:20', '2025-09-06 15:23:20', 1, '03208750853', '2025-10-06 20:23:20');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ledger_created_by` (`created_by`);

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
  ADD UNIQUE KEY `uq_stock_product_batch` (`product_id`,`batch_no`),
  ADD UNIQUE KEY `uniq_stock_tenant_batch` (`product_id`,`batch_no`,`created_by`),
  ADD KEY `idx_stock_created_by` (`created_by`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
