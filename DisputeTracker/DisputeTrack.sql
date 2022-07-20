-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2017 at 10:25 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `disputetracktest`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesslevels`
--

CREATE TABLE IF NOT EXISTS `accesslevels` (
  `id` int(11) NOT NULL,
  `leveltext` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accesslevels`
--

INSERT INTO `accesslevels` (`id`, `leveltext`) VALUES
(1, 'audit'),
(3, 'csr'),
(5, 'ops'),
(7, 'opsADMIN'),
(9, 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `accounttype`
--

CREATE TABLE IF NOT EXISTS `accounttype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typetext` varchar(255) NOT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `accounttype`
--

INSERT INTO `accounttype` (`id`, `typetext`, `iddeleted`) VALUES
(1, 'DDA', 0),
(2, 'SAV', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cardpossession`
--

CREATE TABLE IF NOT EXISTS `cardpossession` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `possessiontext` varchar(255) DEFAULT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `cardpossession`
--

INSERT INTO `cardpossession` (`id`, `possessiontext`, `iddeleted`) VALUES
(1, 'In Possession', 0),
(2, 'Lost', 0),
(3, 'Stolen', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cardstatus`
--

CREATE TABLE IF NOT EXISTS `cardstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statustext` varchar(255) DEFAULT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `cardstatus`
--

INSERT INTO `cardstatus` (`id`, `statustext`, `iddeleted`) VALUES
(1, 'Active', 0),
(2, 'Restricted', 0),
(3, 'Hot', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cardtype`
--

CREATE TABLE IF NOT EXISTS `cardtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typetext` varchar(255) DEFAULT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `cardtype`
--

INSERT INTO `cardtype` (`id`, `typetext`, `iddeleted`) VALUES
(1, 'Check Card', 0),
(2, 'HSA', 0),
(3, 'ATM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `changelog`
--

CREATE TABLE IF NOT EXISTS `changelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `changedate` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ipaddress` varchar(255) NOT NULL,
  `logtype` int(1) NOT NULL,
  `caseid` int(11) NOT NULL,
  `changedetail` varchar(16384) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `changelog`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkaccountnumbers`
--

CREATE TABLE IF NOT EXISTS `checkaccountnumbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseid` int(11) NOT NULL,
  `accountnumber` bigint(12) NOT NULL,
  `accounttype` varchar(255) NOT NULL,
  `businessaccount` tinyint(1) NOT NULL,
  `comments` varchar(4096) NOT NULL,
  `accoountnew` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `checkaccountnumbers`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkattachments`
--

CREATE TABLE IF NOT EXISTS `checkattachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseid` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filelocation` varchar(255) NOT NULL,
  `comments` varchar(4096) NOT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `checkattachments`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkcardnumbers`
--

CREATE TABLE IF NOT EXISTS `checkcardnumbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseid` int(11) NOT NULL,
  `cardnumber` bigint(16) NOT NULL,
  `cardtype` int(11) NOT NULL,
  `cardstatus` int(11) NOT NULL,
  `cardpossession` int(11) NOT NULL,
  `cardmissingdate` varchar(255) NOT NULL,
  `chipcard` tinyint(1) NOT NULL,
  `comments` varchar(4096) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `checkcardnumbers`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkcases`
--

CREATE TABLE IF NOT EXISTS `checkcases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `casestartdate` varchar(255) NOT NULL,
  `custfname` varchar(255) NOT NULL,
  `custlname` varchar(255) NOT NULL,
  `custphone` varchar(255) NOT NULL,
  `custemail` varchar(255) NOT NULL,
  `custaddressone` varchar(255) NOT NULL,
  `custaddresstwo` varchar(255) NOT NULL,
  `custcityaddr` varchar(255) NOT NULL,
  `custstateaddr` varchar(255) NOT NULL,
  `custzipaddr` varchar(255) NOT NULL,
  `redflag` tinyint(1) NOT NULL,
  `sevlev` int(1) NOT NULL,
  `comments` varchar(4096) NOT NULL,
  `userstarted` varchar(255) NOT NULL,
  `casedoneinput` tinyint(1) NOT NULL,
  `customerstartmethod` int(1) NOT NULL,
  `pcletterprintflag` tinyint(1) NOT NULL,
  `caseclosed` tinyint(1) NOT NULL,
  `casedeleted` tinyint(1) NOT NULL,
  `archive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `checkcases`
--

-- --------------------------------------------------------

--
-- Table structure for table `checktransactions`
--

CREATE TABLE IF NOT EXISTS `checktransactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseid` int(11) NOT NULL,
  `cardid` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transactiondate` varchar(255) NOT NULL,
  `dateposted` varchar(255) NOT NULL,
  `disputereason` int(11) NOT NULL,
  `description` varchar(4096) NOT NULL,
  `merchantname` varchar(255) NOT NULL,
  `merchantcontacted` tinyint(1) NOT NULL,
  `merchantcontacteddate` varchar(255) NOT NULL,
  `merchantcontactdescription` varchar(4096) NOT NULL,
  `receiptstatus` tinyint(1) NOT NULL,
  `loss` tinyint(1) NOT NULL,
  `reversalerror` int(11) NOT NULL,
  `procreditgiven` varchar(255) DEFAULT NULL,
  `pcrescinded` varchar(255) DEFAULT NULL,
  `pclettersent` varchar(255) DEFAULT NULL,
  `pcreverselettersent` varchar(255) DEFAULT NULL,
  `cbinitiated` tinyint(1) NOT NULL,
  `cbaccepted` tinyint(1) NOT NULL,
  `comments` varchar(4096) NOT NULL,
  `compromiseid` int(11) NOT NULL,
  `compromisecomments` varchar(4096) NOT NULL,
  `transactiondeleted` tinyint(1) NOT NULL,
  `merchantphone` varchar(255) NOT NULL,
  `merchantnotes` varchar(4096) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `checktransactions`
--

-- --------------------------------------------------------

--
-- Table structure for table `compromise`
--

CREATE TABLE IF NOT EXISTS `compromise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alertnum` varchar(255) NOT NULL,
  `merchantid` varchar(255) NOT NULL,
  `activationdate` varchar(255) NOT NULL,
  `startdate` varchar(255) NOT NULL,
  `enddate` varchar(255) NOT NULL,
  `description` varchar(4096) NOT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `compromise`
--

INSERT INTO `compromise` (`id`, `alertnum`, `merchantid`, `activationdate`, `startdate`, `enddate`, `description`, `iddeleted`) VALUES
(0, 'Not Found', 'Compromise Not Yet in System', 'Not Found', 'Not Found', 'Not Found', 'Compromise Not yet in System', 0),
(1, 'TestAlert', 'TestMerch', 'TestAlert', 'TestAlert', 'TestAlert', 'TestDescrip', 0),
(2, 'TestAlert', 'TestMerch', 'TestAlert', 'TestAlert', 'TestAlert', 'TestDescrip', 0);

-- --------------------------------------------------------

--
-- Table structure for table `disputereasons`
--

CREATE TABLE IF NOT EXISTS `disputereasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `noticetext` varchar(255) NOT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `disputereasons`
--

INSERT INTO `disputereasons` (`id`, `reason`, `noticetext`, `iddeleted`) VALUES
(1, 'Transaction not made by customer, or person who they gave a card', 'Please Describe situation in the discription box', 0),
(2, 'Transaction Amount Differs', 'Please identify the amount differed in the discription box', 0),
(3, 'Non-receipt of Merchandise', 'Please identify if customer has contacted merchant', 0),
(4, 'Credit Slip was listed as a transaction (debit) to account', 'Please attach a copy of the credit slip below and comment "Credit Slip"', 0),
(5, 'Credit Slip has not posted to account', 'Please attach a copy of the credit slip below and comment "Credit Slip"', 0),
(6, 'A single Transaction posted multiple times', 'Please provide number of transations and note dates on which the transactions were posted', 0),
(7, 'Canceled Services/Returned Merchandise', 'Please provide date canceled/returned and cancellation/return number in the discription box', 0),
(8, 'Customer did engage in a transaction, but merchant charged for transactions customer did not engage in', 'Please provide a description in the description box', 0),
(9, 'Defective/Not as Described', 'Please provide return date', 0),
(10, 'Not Elsewhere Classified', 'Please provide details and attachments as needed or required, please provide merchant information as well', 0);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `locationname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1  AUTO_INCREMENT=16 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `locationname`) VALUES
(1, 'Main Campus'),
(2, 'IT Department'),
(3, 'Operations'),
(4, 'Blvd Branch'),
(5, 'Indian Hills'),
(6, 'Commerce'),
(7, 'Rossview'),
(8, 'Sango'),
(9, 'Hilldale'),
(10, 'Princeton'),
(11, 'Dawson Springs'),
(12, 'Providence'),
(13, 'Sebree'),
(14, 'Sturgis'),
(15, 'Sturgis-DT');

-- --------------------------------------------------------

--
-- Table structure for table `reversalerrors`
--

CREATE TABLE IF NOT EXISTS `reversalerrors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reversalerrortext` varchar(255) DEFAULT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `reversalerrors`
--

INSERT INTO `reversalerrors` (`id`, `reversalerrortext`, `iddeleted`) VALUES
(0, 'Not Reversed', 0),
(1, 'Dispute Reason Not enough --Test', 0),
(2, 'Another Test', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `accesslevel` int(1) NOT NULL,
  `locationid` varchar(255) NOT NULL,
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `users`
--

-- --------------------------------------------------------

--
-- Table structure for table `vendortable`
--

CREATE TABLE IF NOT EXISTS `vendortable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendorname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vendortable`
--

INSERT INTO `vendortable` (`id`, `vendorname`, `phone`) VALUES
(1, '270-555-5555', 'New Test Vendor'),
(2, '270-555-5555', 'Newer Test Vendor');

-- ---------------------------------------------------------

--
-- Table structure for table `cityslist`
--

CREATE TABLE `cityslist` (
  `id` int(11) NOT NULL,
  `cityname` varchar(255) NOT NULL,
  `iddeleted` tinyint(1) NOT NULL,  
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cityslist`
--

-- --------------------------------------------------------

--
-- Table structure for table `zipcodelist`
--

CREATE TABLE `zipcodelist` (
  `id` int(11) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `iddeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zipcodelist`
--

INSERT INTO `zipcodelist` (`id`, `zipcode`, `iddeleted`) VALUES
(1, '42240', 0),
(2, '37043', 0);

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounttype`
--
ALTER TABLE `accounttype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `cardpossession`
--
ALTER TABLE `cardpossession`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `cardstatus`
--
ALTER TABLE `cardstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `cardtype`
--
ALTER TABLE `cardtype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `changelog`
--
ALTER TABLE `changelog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `checkaccountnumbers`
--
ALTER TABLE `checkaccountnumbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `checkattachments`
--
ALTER TABLE `checkattachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `checkcardnumbers`
--
ALTER TABLE `checkcardnumbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `checkcases`
--
ALTER TABLE `checkcases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `checktransactions`
--
ALTER TABLE `checktransactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `cityslist`
--
ALTER TABLE `cityslist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `compromise`
--
ALTER TABLE `compromise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `disputereasons`
--
ALTER TABLE `disputereasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `reversalerrors`
--
ALTER TABLE `reversalerrors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `vendortable`
--
ALTER TABLE `vendortable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `zipcodelist`
--
ALTER TABLE `zipcodelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
