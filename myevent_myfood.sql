-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 04:27 AM
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
-- Database: `myevent_myfood`
--

-- --------------------------------------------------------

--
-- Table structure for table `makanan`
--

CREATE TABLE `makanan` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `PRICE` decimal(10,2) NOT NULL,
  `PICTURE` text DEFAULT NULL,
  `DETAILS` text DEFAULT NULL,
  `QUANTITY` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `makanan`
--

INSERT INTO `makanan` (`ID`, `NAME`, `PRICE`, `PICTURE`, `DETAILS`, `QUANTITY`) VALUES
(443, 'Spaghetti Carbonara', 18.50, 'uploads/Spaghetti Carbonara.jpg', 'A classic Italian pasta dish made with creamy sauce, parmesan cheese, and crispy pancetta.', 15),
(445, 'Nasi Lemak Ayam', 14.00, 'uploads/nasi lemak.jpg', 'A Malaysian favorite, Nasi Lemak is fragrant coconut rice served with spicy sambal, crispy anchovies, roasted peanuts, cucumber slices, and a juicy fried chicken drumstick.', 10),
(446, 'Mee Goreng', 8.50, 'uploads/mee goreng.jpg', 'A popular stir-fried noodle dish, Mee Goreng is made with yellow noodles, tofu, eggs, vegetables, and a savory blend of soy sauce and spices for a perfect balance of flavors.', 15),
(447, 'Roti Canai', 3.00, 'uploads/roti canai.jpg', 'Flatbread served with curry sauce.', 20),
(448, 'Nasi Ayam', 10.50, 'uploads/nasi ayam.jpeg', 'A beloved Malaysian dish, Nasi Ayam features fragrant steamed rice paired with succulent roasted or steamed chicken, served with a flavorful soy-based sauce, chili sauce, and a side of chicken soup.', 50),
(450, 'Curry Laksa', 7.90, 'uploads/laksa.jpg', 'A creamy and spicy coconut milk-based noodle soup with tofu, chicken, and fish cakes.', 20),
(451, 'Nasi Kandar', 16.90, 'uploads/nasi kandar.jpg', 'A flavorful Malaysian dish, Nasi Kandar consists of steamed rice served with a variety of rich curries, fried chicken, okra, hard-boiled eggs, and a mix of aromatic spices.', 50),
(452, 'Satay', 9.00, 'uploads/satay.jpg', 'Grilled skewered meat served with peanut sauce, cucumber, and rice cakes.', 25),
(453, 'Ayam Goreng crispy', 5.00, 'uploads/ayam goreng.jpg', 'Crispy fried chicken with a golden, crunchy coating and juicy interior.', 30),
(454, 'Soto', 6.50, 'uploads/soto.jpg', 'A light and savory chicken broth soup with rice cakes, vermicelli, and herbs.', 14),
(455, 'Bubur Ayam', 4.50, 'uploads/bubur ayam.jpg', 'A comforting rice porridge topped with shredded chicken, soy sauce, and fried shallots.', 22),
(456, 'Mee Rebus', 6.00, 'uploads/mee rebus.jpg', 'Yellow noodles served in a thick, savory, and slightly sweet potato-based gravy.', 17),
(457, 'Rendang Daging', 15.00, 'uploads/rendang daging.jpg', 'Tender beef slow-cooked in rich, aromatic coconut milk and spices.', 11),
(458, 'Nasi Goreng', 7.00, 'uploads/nasi goreng.jpg', 'Fried rice cooked with vegetables, egg, and a choice of protein.', 19),
(459, 'Cendol', 4.50, 'uploads/cendol.jpg', 'A refreshing dessert with pandan jelly, coconut milk, and palm sugar syrup.', 27);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `EMAIL` varchar(255) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `NO_PHONE` varchar(15) NOT NULL,
  `PICTURE` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`EMAIL`, `NAME`, `PASSWORD`, `NO_PHONE`, `PICTURE`) VALUES
('arifhakimi0528@gmail.com', 'arif', '$2y$10$l8S8jg6J.OJofriziUVDeuKziGrlNsKiluWfnVuj.w1vyjJi2EwFG', '0109216136', 'uploads/PROFILE PICTURE.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `makanan`
--
ALTER TABLE `makanan`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`EMAIL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `makanan`
--
ALTER TABLE `makanan`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=465;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
