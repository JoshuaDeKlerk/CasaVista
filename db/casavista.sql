-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2024 at 04:27 PM
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
-- Database: `casavista`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `property_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`id`, `user_email`, `property_id`, `created_at`) VALUES
(1, 'admin@casavista.com', 8, '2024-08-31 14:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_link`) VALUES
(36, 8, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(37, 8, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(38, 8, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(39, 8, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(40, 8, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(41, 9, 'https://images.pexels.com/photos/20733560/pexels-photo-20733560/free-photo-of-people-crossing-a-red-bridge.jpeg?auto=compress&cs=tinysrgb&w=600'),
(42, 9, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(43, 9, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(44, 9, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600'),
(45, 9, 'https://images.pexels.com/photos/15598923/pexels-photo-15598923/free-photo-of-man-is-holding-his-laptop.jpeg?auto=compress&cs=tinysrgb&w=600');

-- --------------------------------------------------------

--
-- Table structure for table `property_requests`
--

CREATE TABLE `property_requests` (
  `id` int(11) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_status` enum('For Sale','For Rent','Under Contract') NOT NULL,
  `property_description` text NOT NULL,
  `property_price` decimal(10,2) NOT NULL,
  `property_type` enum('Apartment','House','Condo','Townhouse','Villa') NOT NULL,
  `total_area` decimal(10,2) NOT NULL,
  `living_room_size` decimal(10,2) NOT NULL,
  `kitchen_size` decimal(10,2) NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `property_condition` enum('New','Recently Renovated','Needs Renovation') NOT NULL,
  `year_built` int(4) NOT NULL,
  `furnishing` enum('Furnished','Semi-Furnished','Unfurnished') NOT NULL,
  `map_location` text NOT NULL,
  `street_city` varchar(255) NOT NULL,
  `state_province` varchar(255) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `available_from` date NOT NULL,
  `pet_policy` enum('Pets Allowed','No Pets Allowed') NOT NULL,
  `utilities_included` text NOT NULL,
  `agent_email` varchar(255) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_requests`
--

INSERT INTO `property_requests` (`id`, `property_name`, `property_status`, `property_description`, `property_price`, `property_type`, `total_area`, `living_room_size`, `kitchen_size`, `bedrooms`, `bathrooms`, `property_condition`, `year_built`, `furnishing`, `map_location`, `street_city`, `state_province`, `postal_code`, `available_from`, `pet_policy`, `utilities_included`, `agent_email`, `approved`, `created_at`) VALUES
(8, 'Test', 'For Sale', 'ahihiadhahoiwhd', 2161644.00, 'Apartment', 25000.00, 250.00, 20.00, 3, 2, 'New', 2021, 'Furnished', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7180.639647997007!2d28.175370287346862!3d-25.85894917719521!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e9564491a8fb597%3A0x7b0f4c752e37fa5f!2sCenturion%20Station!5e0!3m2!1sen!2sza!4v1724969159201!5m2!1sen!2sza\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '121 Wicklow Road Bronberrick', 'Gauteng', '0157', '2024-08-15', 'Pets Allowed', 'Water', 'admin@casavista.com', 1, '2024-08-31 10:15:59'),
(9, 'Johnathan', 'For Sale', 'adjnwanhjfwa', 1000000.00, 'House', 250.00, 22.00, 21.00, 4, 2, 'New', 2021, 'Furnished', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7180.639647997007!2d28.175370287346862!3d-25.85894917719521!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e9564491a8fb597%3A0x7b0f4c752e37fa5f!2sCenturion%20Station!5e0!3m2!1sen!2sza!4v1724969159201!5m2!1sen!2sza\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '121 Wicklow Road Bronberrick', 'Gauteng', '0157', '2024-09-20', 'Pets Allowed', 'Water', 'John@gmail.com', 1, '2024-08-31 21:58:58');

-- --------------------------------------------------------

--
-- Table structure for table `property_reviews`
--

CREATE TABLE `property_reviews` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `review_text` text NOT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `agent_email` varchar(255) NOT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `review_text` text NOT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `agent_email`, `reviewer_name`, `review_text`, `review_date`) VALUES
(1, 'agent@gmail.com', 'Joshua', 'Great help', '2024-08-22 17:03:56'),
(3, 'admin@casavista.com', 'user@gmail.com', 'Very good service', '2024-08-31 13:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_type` enum('user','agent','admin') NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `full_name`, `email`, `user_type`, `date_of_birth`, `password`, `profile_picture`, `description`) VALUES
(2, 'Admin', 'admin@casavista.com', 'admin', '2004-04-05', '$2y$10$U90goNX4p66du7QrP1xe4.vF.La2Mbc1rWRk8ssewdlXDz8n9h7bK', '/uploads/3a10659427799ee4635795d2d3f4ce5d.png', ''),
(3, 'Joshua De Klerk', 'Joshua@gmail.com', 'user', '2006-08-11', '$2y$10$acXuO8YKQnxzaAwIhuMjdeBTg4EisnVh8YbCi9l4h5ox7hyAMdyIq', '../uploads/66d0baa66237d-Screenshot 2024-07-25 152933.png', NULL),
(15, 'User', 'user@gmail.com', 'user', '2003-08-21', '$2y$10$VY4182mEvwYgyRfNa06u3u0h7gw92gm4DhS.UFJs3N28PQnC8/eVq', '../uploads/66d0b7636b9c3-David2.png', ''),
(16, 'Agent', 'agent@gmail.com', 'agent', '2002-05-06', '$2y$10$D3aZ4/JpczEcK0lWLkZne.OsyiqQEZ8gHv/lBM1ie2YpWN38dvloO', '/uploads/5514759ceb3029e45d5b25ed6d474efe.png', ''),
(19, 'Johnathan', 'John@gmail.com', 'agent', '2005-08-30', '$2y$10$7WGMzxi9mZcEp99/r/wYuOOW991GgGGSvJcqJUzQT9ovIf9jpJFOK', NULL, NULL),
(20, 'Test', 'test@gmail.com', 'user', '2003-08-21', '$2y$10$/2IfyiXnFnzlSrMIQ2ML3ug62yDSaZdmDKDhWqXq/g.WWvcJXsiNS', NULL, NULL),
(21, 'Jaco Mostert', 'Jaco@gmail.com', 'user', '2006-08-15', '$2y$10$4E6tG4Uf1QqVAMaqlfXgGuKI85EtCOzvSAuUf.u2CD68W7l5qaFWy', '/uploads/21d7653869682fd499d398626092396c.png', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_requests`
--
ALTER TABLE `property_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_email` (`agent_email`);

--
-- Indexes for table `property_reviews`
--
ALTER TABLE `property_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `property_requests`
--
ALTER TABLE `property_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `property_reviews`
--
ALTER TABLE `property_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `property_requests` (`id`);

--
-- Constraints for table `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`),
  ADD CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `property_requests` (`id`);

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_requests`
--
ALTER TABLE `property_requests`
  ADD CONSTRAINT `property_requests_ibfk_1` FOREIGN KEY (`agent_email`) REFERENCES `user` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `property_reviews`
--
ALTER TABLE `property_reviews`
  ADD CONSTRAINT `property_reviews_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property_requests` (`id`),
  ADD CONSTRAINT `property_reviews_ibfk_2` FOREIGN KEY (`user_email`) REFERENCES `user` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
