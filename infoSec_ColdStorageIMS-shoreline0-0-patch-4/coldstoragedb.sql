-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2025 at 01:59 PM
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
-- Database: `coldstoragedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `AlertID` int(11) NOT NULL,
  `AlertType` enum('Storage','Product') NOT NULL,
  `RelatedID` int(11) NOT NULL,
  `AlertName` varchar(255) NOT NULL,
  `AlertTime` datetime NOT NULL DEFAULT current_timestamp(),
  `Notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`AlertID`, `AlertType`, `RelatedID`, `AlertName`, `AlertTime`, `Notes`) VALUES
(1, 'Storage', 0, 'Expiration Warning - Vanilla Ice Cream', '2025-06-25 10:00:00', 'Vanilla Ice Cream nearing expiry date'),
(2, 'Storage', 0, 'Expiration Warning - Chocolate Mousse', '2024-12-10 10:00:00', 'Chocolate Mousse nearing expiry date'),
(3, 'Storage', 0, 'Expiration Warning - Strawberry Cheesecake', '2024-11-20 14:00:00', 'Strawberry Cheesecake nearing expiry date'),
(4, 'Storage', 0, 'Storage Warning - Tiramisu', '2024-10-01 08:00:00', 'Tiramisu should be stored properly to avoid spoilage'),
(5, 'Product', 1, 'Product \'Vanilla Ice Cream\' is near expiry.', '2025-02-25 20:24:15', NULL),
(6, 'Product', 2, 'Expiration warning - \'Chocolate Mousse\'', '2025-02-25 20:44:44', 'Product \'Chocolate Mousse\' is near expiry.'),
(7, 'Product', 19, 'Expiration warning - \'Frozen Yogurt\'', '2025-02-25 20:45:45', 'Product \'Frozen Yogurt\' is near expiry.'),
(8, 'Storage', 1, 'Storage warning - \'Storage A\'', '2025-02-25 20:45:45', 'Storage \'Storage A\' is almost full.');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `CurrentStock` int(11) NOT NULL DEFAULT 0,
  `ProductExpiryDate` date NOT NULL,
  `StorageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `ProductName`, `CurrentStock`, `ProductExpiryDate`, `StorageID`) VALUES
(1, 'Vanilla Ice Cream', 57, '2025-02-27', 2),
(2, 'Chocolate Mousse', 33, '2025-02-27', 5),
(3, 'Strawberry Cheesecake', 20, '2025-03-05', 4),
(4, 'Mango Sorbet', 40, '2025-07-10', 0),
(5, 'Tiramisu', 25, '2024-10-05', 0),
(7, 'Fruit Salad', 15, '2025-02-26', 0),
(8, 'Pastillas', 35, '0000-00-00', 0),
(9, 'Gelato', 25, '0000-00-00', 0),
(10, 'Ice', 100, '0000-00-00', 0),
(11, 'milk', 10, '0000-00-00', 0),
(12, 'Burger', 31, '0000-00-00', 0),
(13, 'IKEA Vanilla Ice Cream', 90, '0000-00-00', 0),
(14, 'Melon Juice', 10, '0000-00-00', 0),
(18, 'Soda', 123, '2025-02-28', 0),
(19, 'Frozen Yogurt', 423, '2025-02-27', 1),
(21, 'Banana', 12, '2025-02-27', 0),
(22, 'Chocolate Mousse', 31, '2025-02-27', 3),
(23, 'Jilly Juice', 2, '2025-12-25', 9);

-- --------------------------------------------------------

--
-- Table structure for table `storage`
--

CREATE TABLE `storage` (
  `StorageID` int(11) NOT NULL,
  `StorageName` varchar(255) NOT NULL,
  `StorageMaxCapacity` int(11) NOT NULL,
  `StorageUsedCapacity` int(11) NOT NULL,
  `StorageTemperature` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storage`
--

INSERT INTO `storage` (`StorageID`, `StorageName`, `StorageMaxCapacity`, `StorageUsedCapacity`, `StorageTemperature`) VALUES
(1, 'Storage A', 500, 0, -18.00),
(2, 'Storage B', 300, 0, 4.00),
(3, 'Storage C', 200, 0, 2.00),
(4, 'Storage D', 400, 0, 0.00),
(5, 'Storage E', 250, 0, 8.00),
(9, 'Storage F', 100, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactionlog`
--

CREATE TABLE `transactionlog` (
  `TransactionID` int(11) NOT NULL,
  `TransactionType` text NOT NULL,
  `UserID` int(11) NOT NULL,
  `TransactionDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactionlog`
--

INSERT INTO `transactionlog` (`TransactionID`, `TransactionType`, `UserID`, `TransactionDate`, `Details`) VALUES
(1, 'Added product', 1, '2025-02-25 11:40:33', NULL),
(2, 'Added product: (ID: ', 1, '2025-02-25 11:42:54', NULL),
(3, 'Added product: (ID: ', 1, '2025-02-25 11:45:02', NULL),
(4, 'Added product: (ID: ', 1, '2025-02-25 11:46:07', NULL),
(5, 'Added product: (ID: ', 1, '2025-02-25 11:54:50', NULL),
(6, 'Added product: 12)', 1, '2025-02-25 11:56:34', NULL),
(7, 'Added product: (ID: ', 1, '2025-02-25 12:03:32', NULL),
(8, 'Added product: \n (ID', 1, '2025-02-25 12:04:53', NULL),
(9, 'Added product.', 1, '2025-02-25 12:13:38', 'ID: 18 - Soda(Quantity: 123)'),
(10, 'Added storage', 1, '2025-02-25 12:28:02', 'ID: 9 - Storage F (Capacity: 100 , Temp 1)'),
(11, 'Added user', 1, '2025-02-25 12:42:01', 'ID: 3 - Marco Polo'),
(12, 'Added product', 3, '2025-02-25 12:43:23', NULL),
(13, 'Deleted storage', 1, '2025-02-25 12:50:14', 'ID: 6 - Storage F (Capacity: 100 , Temp 1.00)'),
(14, 'Deleted storage', 1, '2025-02-25 12:50:29', 'ID: 7 - Storage F (Capacity: 100 , Temp 1.00)'),
(15, 'Deleted storage', 1, '2025-02-25 12:50:31', 'ID: 8 - Storage F (Capacity: 100 , Temp 1.00)'),
(16, 'Added product', 1, '2025-02-25 12:51:54', 'ID: 17 - Soda (Quantity: 123)'),
(17, 'Deleted product', 1, '2025-02-25 12:52:17', 'ID: 15 - Soda (Quantity: 123)'),
(18, 'Updated product', 1, '2025-02-25 12:57:08', 'ID:  - Frozen Yogurt (Quantity: 12)'),
(19, 'Updated product', 1, '2025-02-25 12:57:15', 'ID:  - Frozen Yogurt (Quantity: 12)'),
(20, 'Updated storage', 1, '2025-02-25 14:07:03', 'ID: 0 - Storage F (Capacity: 100 , Temp )'),
(21, 'Updated product', 1, '2025-02-25 14:23:01', 'ID:  - Vanilla Ice Cream (Quantity: 50)'),
(22, 'Updated product', 1, '2025-02-25 14:23:05', 'ID:  - Vanilla Ice Cream (Quantity: 52)'),
(23, 'Updated product', 1, '2025-02-25 14:34:39', 'ID:  - Vanilla Ice Cream (Quantity: 502)'),
(24, 'Updated product', 1, '2025-02-25 14:37:58', 'ID:  - Vanilla Ice Cream (Quantity: 502)'),
(25, 'Added product', 1, '2025-02-25 14:39:10', 'ID: 21 - Banana (Quantity: 12)'),
(26, 'Updated product', 1, '2025-02-25 14:40:03', 'ID:  - Vanilla Ice Cream (Quantity: 501)'),
(27, 'Updated product', 1, '2025-02-25 14:48:34', 'ID: 1 - Vanilla Ice Cream (Quantity: 51)'),
(28, 'Updated product', 1, '2025-02-25 14:49:01', 'ID: 1 - Vanilla Ice Cream (Quantity: 51)'),
(29, 'Updated product', 1, '2025-02-25 14:50:18', 'ID: 2 - Chocolate Mousse (Quantity: 31)'),
(30, 'Deleted product', 1, '2025-02-25 14:50:25', 'ID: 16 - Soda (Quantity: 12)'),
(31, 'Updated storage', 1, '2025-02-25 14:59:35', 'ID: 4 - Storage D (Capacity: 400 , Temp 0)'),
(32, 'Updated user', 1, '2025-02-25 15:03:47', 'ID: 0 - Marco Polo'),
(33, 'Updated product', 1, '2025-02-25 15:05:53', 'ID: 1 - Vanilla Ice Cream (Quantity: 49)'),
(34, 'Updated storage', 1, '2025-02-25 15:06:07', 'ID: 9 - Storage F (Capacity: 100 , Temp 0)'),
(35, 'Updated product', 1, '2025-02-25 15:19:38', 'ID: 21 - Banana (Quantity: 12)'),
(36, 'Updated product', 1, '2025-02-25 15:27:35', 'ID: 18 - Soda (Quantity: 123)'),
(37, 'Updated user', 1, '2025-02-25 16:13:14', 'ID: 0 - Marco Polo'),
(38, 'Added user', 1, '2025-02-25 16:24:57', 'ID: 4 - John Lloyd'),
(39, 'Updated user', 1, '2025-02-25 16:25:04', 'ID: 0 - John Lloyd'),
(40, 'Updated user', 2, '2025-02-25 16:29:25', 'ID: 0 - John Lloyd'),
(41, 'Updated product', 1, '2025-02-25 16:47:40', 'ID: 21 - Banana (Quantity: 12)'),
(42, 'Updated product', 1, '2025-02-25 16:48:37', 'ID:  - Frozen Yogurt (Quantity: 123)'),
(43, 'Updated product', 1, '2025-02-25 16:51:36', 'ID:  - Banana (Quantity: 12)'),
(44, 'Deleted product', 1, '2025-02-25 16:51:56', 'ID: 6 - Fruit Salad (Quantity: 15)'),
(45, 'Updated product', 1, '2025-02-25 16:52:06', 'ID: 7 - Fruit Salad (Quantity: 15)'),
(46, 'Updated product', 1, '2025-02-25 16:53:39', 'ID: 1 - Vanilla Ice Cream (Quantity: 49)'),
(47, 'Updated product', 1, '2025-02-25 16:57:02', 'ID: 1 - Vanilla Ice Cream (Quantity: 49)'),
(48, 'Updated product', 1, '2025-02-25 16:57:10', 'ID:  - Chocolate Mousse (Quantity: 31)'),
(49, 'Updated product', 1, '2025-02-25 16:57:16', 'ID: 2 - Chocolate Mousse (Quantity: 31)'),
(50, 'Updated product', 1, '2025-02-25 16:57:23', 'ID: 3 - Strawberry Cheesecake (Quantity: 20)'),
(51, 'Added product', 1, '2025-02-25 16:58:10', 'ID: 22 - Chocolate Mousse (Quantity: 31)'),
(52, 'Added product', 1, '2025-02-25 16:58:38', 'ID: 23 - Jilly Juice (Quantity: 2)'),
(53, 'Updated product', 1, '2025-02-25 17:08:43', 'ID:  - Vanilla Ice Cream (Quantity: 59)'),
(54, 'Updated product', 1, '2025-02-25 17:08:54', 'ID: 1 - Vanilla Ice Cream (Quantity: 60)'),
(55, 'Updated product', 1, '2025-02-25 17:09:13', 'ID:  - Vanilla Ice Cream (Quantity: 60)'),
(56, 'Updated product', 1, '2025-02-25 17:09:19', 'ID: 1 - Vanilla Ice Cream (Quantity: 62)'),
(57, 'Updated product', 1, '2025-02-25 17:09:32', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(58, 'Updated product', 1, '2025-02-25 17:17:07', 'ID:  - Burger (Quantity: 31)'),
(59, 'Updated product', 1, '2025-02-25 19:06:29', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(60, 'Updated product', 1, '2025-02-25 19:09:36', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(61, 'Updated product', 1, '2025-02-25 19:10:06', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(62, 'Updated product', 1, '2025-02-25 20:17:40', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(63, 'Updated product', 1, '2025-02-25 20:21:26', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(64, 'Updated product', 1, '2025-02-25 20:22:30', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(65, 'Updated product', 1, '2025-02-25 20:22:55', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(66, 'Updated product', 1, '2025-02-25 20:24:15', 'ID: 1 - Vanilla Ice Cream (Quantity: 57)'),
(67, 'Updated product', 1, '2025-02-25 20:33:59', 'ID: 2 - Chocolate Mousse (Quantity: 31)'),
(68, 'Updated product', 1, '2025-02-25 20:40:19', 'ID: 2 - Chocolate Mousse (Quantity: 33)'),
(69, 'Updated product', 1, '2025-02-25 20:44:44', 'ID: 2 - Chocolate Mousse (Quantity: 33)'),
(70, 'Updated product', 1, '2025-02-25 20:45:45', 'ID: 19 - Frozen Yogurt (Quantity: 423)');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `Role` enum('Admin','Super Admin') NOT NULL DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Email`, `Password`, `Status`, `Role`) VALUES
(1, 'John', 'Doe', 'admin@example.com', 'e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7', 'Active', 'Admin'),
(2, 'Jane', 'Smith', 'superadmin@example.com', 'd3535b78e24867f3c850fec1c8591b7a469b8f8683be64d835057cb9fd204aa1', 'Active', 'Super Admin'),
(3, 'Marco', 'Polo', 'marco@gmail.com', 'c7c8168740b222ed25d5d14346239a01ea20b61af2dd531b53b7d523b567c46b', 'Active', 'Admin'),
(4, 'John', 'Lloyd', 'jampol@gmail.com', '542ce0bd98f3cd171f1d4b72ffd7c0d9b0430af8e70a90ec7f2becad819702f7', 'Active', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`AlertID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `StorageID` (`StorageID`);

--
-- Indexes for table `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`StorageID`);

--
-- Indexes for table `transactionlog`
--
ALTER TABLE `transactionlog`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `AlertID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `storage`
--
ALTER TABLE `storage`
  MODIFY `StorageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transactionlog`
--
ALTER TABLE `transactionlog`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactionlog`
--
ALTER TABLE `transactionlog`
  ADD CONSTRAINT `transactionlog_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
