-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 09:29 AM
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
-- Database: `food_order`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `restaurant_id`, `name`, `description`, `price`, `image`) VALUES
(1, 1, 'Classic Veg Burger', 'Fresh lettuce, tomato, and cheese with a crunchy veggie patty.', 120.00, 'menuphoto/Classic-Veg-Burger.webp'),
(2, 1, 'Double Cheese Burger', 'Loaded with double cheese and crispy veggies.', 150.00, 'menuphoto/Double-Cheese-Burger1.jpg'),
(3, 1, 'Crispy Paneer Burger', 'Soft bun with spicy paneer and sauce.', 170.00, 'menuphoto/Crispy-Paneer-Burger.jpg'),
(4, 2, 'Margherita Pizza', 'Classic mozzarella with tomato base.', 200.00, 'menuphoto/Margherita-Pizza.webp'),
(5, 2, 'Veg Supreme Pizza', 'Loaded with olives, corn, and peppers.', 250.00, 'menuphoto/Veg-Supreme-Pizza.jpg'),
(6, 2, 'Cheese Burst Pizza', 'Extra cheesy crust with Italian herbs.', 270.00, 'menuphoto/Cheese-Burst-Pizza.jpg'),
(10, 4, 'Veg Sub Sandwich', 'Soft bread with veggies and sauces.', 160.00, 'menuphoto/Veg-Sub-Sandwich.jpg'),
(11, 4, 'Paneer Sub', 'Grilled paneer and cheese-loaded sandwich.', 180.00, 'menuphoto/Paneer-Sub.jpeg'),
(12, 4, 'Club Sandwich', 'Triple-layer sandwich with mayo and veggies.', 170.00, 'menuphoto/Club-Sandwich.jpg'),
(13, 5, 'Veg Taco', 'Crispy tacos with spiced veggies and cheese.', 170.00, 'menuphoto/Veg-Taco.jpg'),
(14, 5, 'Paneer Taco', 'Paneer filling with salsa sauce.', 160.00, 'menuphoto/Paneer-Taco.webp'),
(15, 5, 'Cheesy Taco', 'Loaded with cheese and lettuce.', 140.00, 'menuphoto/Cheesy-Taco.jpg'),
(16, 6, 'Paneer Tikka', 'Grilled paneer cubes marinated with Indian spices.', 190.00, 'menuphoto/Paneer-Tikka.jpeg'),
(17, 6, 'Paneer Chilli', 'Crispy paneer tossed with capsicum and sauce.', 200.00, 'menuphoto/Paneer-Chilli.webp'),
(18, 6, 'Paneer Butter Masala', 'Creamy curry with paneer and butter.', 220.00, 'menuphoto/Paneer-Butter-Masala.webp'),
(19, 7, 'Veg Roll', 'Soft tortilla stuffed with veggies.', 120.00, 'menuphoto/Veg-Roll.webp'),
(20, 7, 'Paneer Wrap', 'Paneer filling with spicy mayo.', 150.00, 'menuphoto/Paneer-Wrap.jpg'),
(21, 7, 'Cheese Corn Wrap', 'Cheesy corn roll with tangy sauce.', 140.00, 'menuphoto/Cheese-Corn-Wrap.jpeg'),
(22, 8, 'Cheesy Corn Bowl', 'Hot corn with cheese and pepper.', 100.00, 'menuphoto/Cheesy-Corn-Bowl.jpeg'),
(23, 8, 'Butter Corn', 'Steamed corn with butter and salt.', 80.00, 'menuphoto/Butter-Corn.jpg'),
(24, 8, 'Corn Sandwich', 'Grilled sandwich with corn and mayo.', 130.00, 'menuphoto/Corn-Sandwich.avif'),
(25, 9, 'Veggie Delight Burger', 'Classic veg burger with spicy mayo.', 140.00, 'menuphoto/Veggie-Delight-Burger.webp'),
(26, 9, 'Aloo Tikki Burger', 'Crispy aloo patty with lettuce.', 120.00, 'menuphoto/Aloo-Tikki-Burger.webp'),
(27, 9, 'Paneer Burger', 'Paneer patty burger with cheese.', 150.00, 'menuphoto/Panner-Burger-1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_date`, `status`) VALUES
(1, 1, 290.00, '2025-11-27 19:45:53', 'Pending'),
(2, 2, 310.00, '2026-03-29 23:48:23', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Paneer Wrap', 150.00, 1, 150.00),
(2, 1, 'Cheese Corn Wrap', 140.00, 1, 140.00),
(3, 2, 'Cheesy Corn Bowl', 100.00, 1, 100.00),
(4, 2, 'Butter Corn', 80.00, 1, 80.00),
(5, 2, 'Corn Sandwich', 130.00, 1, 130.00);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cuisine` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `cuisine`, `image`) VALUES
(1, 'Veggie Burger House', 'Fast Food, Burgers', 'vegphoto/veggie_burger.jpg'),
(2, 'Green Slice Pizza', 'Italian, Pizza', 'vegphoto/green_pizza.jpg'),
(4, 'Sub Veg Café', 'Sandwiches, Fast Food', 'vegphoto/veg_sandwich.jpg'),
(5, 'Tasty Taco Veg', 'Mexican, Veg Tacos', 'vegphoto/veg_taco.jpg'),
(6, 'Paneer Point', 'Indian, Paneer Starters', 'vegphoto/paneer_tikka.jpg'),
(7, 'Veg Wrap Station', 'Wraps, Rolls', 'vegphoto/veg_wrap.jpg'),
(8, 'Cheesy Corn Café', 'Snacks, Corn Dishes', 'vegphoto/cheesy_corn.jpg'),
(9, 'Burger & Bites', 'Veg Burgers, Snacks', 'vegphoto/burger_bites.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `wallet_balance` decimal(10,2) DEFAULT 0.00,
  `otp` varchar(6) DEFAULT NULL,
  `otp_created_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `wallet_balance`, `otp`, `otp_created_at`, `is_verified`, `created_at`) VALUES
(2, 'jigato', 'jigatofood@gmail.com', '3030303030', 'indiaa', '$2y$10$RW0Ki6bJPhosanEckG3aJ.AlIbpWhW6R/p.wGrhAVBVbd6C/qhtqS', 500.00, '903481', '2026-03-29 15:44:43', 1, '2026-03-29 13:41:56');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_history`
--

CREATE TABLE `wallet_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('Add','Deduct') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_history`
--

INSERT INTO `wallet_history` (`id`, `user_id`, `type`, `amount`, `description`, `date`) VALUES
(1, 1, 'Add', 1000.00, 'Wallet Top-up (Added ₹1,000.00)', '2025-11-27 14:14:32'),
(2, 1, 'Deduct', 290.00, 'Order Payment (₹290)', '2025-11-27 14:15:53'),
(3, 1, 'Add', 14.00, '5', '2025-11-27 14:15:53'),
(4, 2, 'Add', 500.00, 'Wallet Top-up (Added ₹500.00)', '2026-03-29 18:19:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet_history`
--
ALTER TABLE `wallet_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_history`
--
ALTER TABLE `wallet_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
