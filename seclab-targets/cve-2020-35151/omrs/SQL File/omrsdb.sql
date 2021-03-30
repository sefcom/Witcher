-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2020 at 02:00 PM
-- Server version: 10.3.15-MariaDB
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `omrsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` varchar(200) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Admin', 'admin', 1234567890, 'admin@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2020-04-28 05:26:03');

-- --------------------------------------------------------

--
-- Table structure for table `tblregistration`
--

CREATE TABLE `tblregistration` (
  `ID` int(10) NOT NULL,
  `RegistrationNumber` varchar(100) DEFAULT NULL,
  `UserID` int(10) DEFAULT NULL,
  `DateofMarriage` varchar(200) NOT NULL,
  `HusbandName` varchar(200) DEFAULT NULL,
  `HusImage` varchar(200) NOT NULL,
  `HusbandReligion` varchar(50) DEFAULT NULL,
  `Husbanddob` varchar(200) DEFAULT NULL,
  `HusbandSBM` varchar(50) DEFAULT NULL,
  `HusbandAdd` mediumtext DEFAULT NULL,
  `HusbandZipcode` int(10) DEFAULT NULL,
  `HusbandState` varchar(200) DEFAULT NULL,
  `HusbandAdharno` varchar(200) DEFAULT NULL,
  `WifeName` varchar(200) DEFAULT NULL,
  `WifeImage` varchar(200) NOT NULL,
  `WifeReligion` varchar(200) DEFAULT NULL,
  `Wifedob` varchar(200) DEFAULT NULL,
  `WifeSBM` varchar(50) DEFAULT NULL,
  `WifeAdd` mediumtext DEFAULT NULL,
  `WifeZipcode` int(10) DEFAULT NULL,
  `WifeState` varchar(200) DEFAULT NULL,
  `WifeAdharNo` varchar(200) DEFAULT NULL,
  `WitnessNamefirst` varchar(200) DEFAULT NULL,
  `WitnessAddressFirst` mediumtext DEFAULT NULL,
  `WitnessNamesec` varchar(200) DEFAULT NULL,
  `WitnessAddresssec` mediumtext DEFAULT NULL,
  `WitnessNamethird` varchar(200) DEFAULT NULL,
  `WitnessAddressthird` mediumtext DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Status` varchar(100) DEFAULT NULL,
  `Remark` varchar(120) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblregistration`
--

INSERT INTO `tblregistration` (`ID`, `RegistrationNumber`, `UserID`, `DateofMarriage`, `HusbandName`, `HusImage`, `HusbandReligion`, `Husbanddob`, `HusbandSBM`, `HusbandAdd`, `HusbandZipcode`, `HusbandState`, `HusbandAdharno`, `WifeName`, `WifeImage`, `WifeReligion`, `Wifedob`, `WifeSBM`, `WifeAdd`, `WifeZipcode`, `WifeState`, `WifeAdharNo`, `WitnessNamefirst`, `WitnessAddressFirst`, `WitnessNamesec`, `WitnessAddresssec`, `WitnessNamethird`, `WitnessAddressthird`, `RegDate`, `Status`, `Remark`, `UpdationDate`) VALUES
(1, '483974079', 1, '03/18/2020', 'Harish Kumar', 'b9fb9d37bdf15a699bc071ce49baea531588155948.jpg', 'Hindu', '04/21/1991', 'Bachelor', 'K-896 Kohlapur New Delhi', 110096, 'Delhi', '454477546654', 'Neelam Kumari', '1e6ae4ada992769567b71815f124fac51588155948.jpg', 'Hindu', '04/28/1992', 'Bachelor', 'K-896 Kohlapur New Delhi', 110096, 'Delhi', '256565656565', 'Rakesh Ohja', 'H-908 Ghaziabad', 'Manisha Ohja', 'H-908 Ghaziabad', 'Jaggi Singh', 'K-789 New Delhi', '2020-04-29 10:50:58', 'Verified', 'Your Application has been verified', '2020-04-29 10:50:58'),
(2, '782520546', 1, '04/28/2020', 'ddsf', '', 'fdsf', '04/01/2020', 'Bachelor', 'erfr', 113213, 'erewrewr', '321545445645', 'rewr', '', 'fery', '04/01/2020', 'Bachelor', 'esfdsfd', 313132, 'rewrtreyty', '464445', 'jhytj', 'jh', 'ytu', 'yttr', 'tytr', 'tytr', '2020-04-29 12:24:41', 'Rejected', 'rejected', '2020-04-29 12:24:41'),
(3, '290346708', 1, '04/09/2020', 'Santosh Jha', 'b9fb9d37bdf15a699bc071ce49baea531588155948.jpg', 'Hindu', '04/08/1991', 'Divorsee', 'K-126 Ragunath Nagar Kailash Colony, Varanasi', 221001, 'UP', '765478977979', 'Gayatri', '1e6ae4ada992769567b71815f124fac51588155948.jpg', 'Hindu', '05/02/1993', 'Bachelor', 'K-126 Ragunath Nagar Kailash Colony, Varanasi', 221001, 'UP', '798764987978', 'Kaushal Jja', 'U-910 Ravidrapuri Colony Bhelupura, Varanasi-221001', 'John', 'K-710 Bojubir Varanasi-221003', 'Janki Das Mishra', 'J-910 Lanka Varanasi-221009', '2020-04-29 10:26:55', NULL, NULL, '2020-04-29 10:26:55'),
(4, '535376446', 1, '04/18/2020', 'Mihir Mishra', 'b9fb9d37bdf15a699bc071ce49baea531588156505.jpg', 'Hindu', '03/01/1989', 'Bachelor', 'K-867 Mayur Vihar ph-2 Near Reliance Fresh Delhi', 110097, 'Delhi', '656465464654', 'Rakhi Ojha', '1e6ae4ada992769567b71815f124fac51588156505.jpg', 'Hindu', '02/09/1992', 'Bachelor', 'K-867 Mayur Vihar ph-2 Near Reliance Fresh Delhi', 110097, 'Delhi', '148974497898', 'Manish Kumar', 'S-867 Mayur Vihar ph-1 Near Reliance Fresh Delhi', 'Raagni Kumari', 'S-867 Mayur Vihar ph-1 Near Reliance Fresh Delhi', 'Lalit Jha', 'K-789 Grater Kailsah Delhi', '2020-04-29 10:37:03', NULL, NULL, '2020-04-29 10:37:03'),
(5, '575693756', 2, '03/11/2020', 'Rahul Singh', '7fdc1a630c238af0815181f9faa190f51588416555.jpg', 'Hindu', '01/22/1990', 'Bachelor', 'ABC 434 NEw Delhi', 110001, 'New Delhi', '123654788544', 'Garima Singh', '993aae75fc102f0885f6c2b6b5db93bd1588416555.jpg', 'Hindu', '1992/08/19', 'Bachelor', 'New Delhi', 110096, 'New Delhi', '101121454545', 'ABC', 'New Delhi', 'XYZ', 'Noida', 'ABC XYZ', 'New Delhi', '2020-05-02 10:50:41', 'Verified', 'Marriage Registered', '2020-05-02 10:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `ID` int(10) NOT NULL,
  `FirstName` varchar(150) DEFAULT NULL,
  `LastName` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Address` mediumtext DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`ID`, `FirstName`, `LastName`, `MobileNumber`, `Address`, `Password`, `RegDate`) VALUES
(1, 'Abir', 'Singh', 7979778979, 'ABC-909 hussain marg new delhi 110096', '202cb962ac59075b964b07152d234b70', '2020-04-28 06:12:34'),
(2, 'Anuj', 'Kumar', 1234567890, 'New Delhi India', 'f925916e2754e5e03f75dd58a5733251', '2020-05-02 10:46:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblregistration`
--
ALTER TABLE `tblregistration`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblregistration`
--
ALTER TABLE `tblregistration`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
