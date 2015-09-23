/*
SQLyog Community v8.3 
MySQL - 5.1.50-community : Database - nmesh
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `cacheID` varchar(32) NOT NULL,
  `networkID` int(5) unsigned NOT NULL,
  `cacheContent` longtext NOT NULL,
  `cacheDate` datetime NOT NULL,
  PRIMARY KEY (`cacheID`),
  KEY `networkID` (`networkID`),
  CONSTRAINT `networkID_fk` FOREIGN KEY (`networkID`) REFERENCES `networks` (`networkID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cache` */

insert  into `cache`(`cacheID`,`networkID`,`cacheContent`,`cacheDate`) values ('55003385331be9ce65950b8eaf2a6bc0',5,'<ul></ul>','2011-04-06 23:16:27');

/*Table structure for table `epochs` */

DROP TABLE IF EXISTS `epochs`;

CREATE TABLE `epochs` (
  `epochID` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `networkID` int(5) unsigned NOT NULL,
  `iterations` int(6) unsigned NOT NULL,
  `startMSE` decimal(10,9) unsigned NOT NULL,
  `endMSE` decimal(10,9) unsigned NOT NULL,
  `epochDate` datetime NOT NULL,
  `execTime` decimal(15,8) unsigned NOT NULL DEFAULT '0.00000000',
  `trainsetID` int(5) DEFAULT NULL,
  PRIMARY KEY (`epochID`),
  KEY `networkID` (`networkID`),
  KEY `trainsetID` (`trainsetID`),
  CONSTRAINT `epochs_ibfk_1` FOREIGN KEY (`networkID`) REFERENCES `networks` (`networkID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `epochs` */

/*Table structure for table `networks` */

DROP TABLE IF EXISTS `networks`;

CREATE TABLE `networks` (
  `networkID` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `networkName` varchar(50) NOT NULL,
  `snapshot` longtext,
  `authkey` varchar(40) NOT NULL,
  `networkType` enum('unmanaged','managed') NOT NULL DEFAULT 'unmanaged',
  `momentumrate` decimal(5,4) NOT NULL DEFAULT '0.5000',
  `learningrate` decimal(5,4) unsigned NOT NULL DEFAULT '1.0000',
  `targetmse` decimal(5,4) unsigned DEFAULT '0.0020',
  `epochmax` int(5) unsigned DEFAULT '10000',
  `createdDate` datetime DEFAULT NULL,
  PRIMARY KEY (`networkID`,`networkType`),
  UNIQUE KEY `authkey` (`authkey`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `networks` */

insert  into `networks`(`networkID`,`networkName`,`snapshot`,`authkey`,`networkType`,`momentumrate`,`learningrate`,`targetmse`,`epochmax`,`createdDate`) values (1,'123',NULL,'85dd7a0336894ce2acc51135b38e703ae350d163','managed','0.5000','1.0000','0.0500',2000,'2011-04-06 23:05:05'),(2,'abc',NULL,'e298c5baba10fb2535efd49a606d79407aa27330','managed','0.5000','1.0000','0.0500',2000,'2011-04-06 23:06:26'),(3,'abcd',NULL,'ad2ed69134485c41e9ba9ca69de7cf3e684ba6ca','managed','0.5000','1.0000','0.0500',2000,'2011-04-06 23:06:33'),(4,'123',NULL,'8724e3f7b4a69944c385cd84a4380893bb061ec8','managed','0.5000','1.0000','0.0500',2000,'2011-04-06 23:06:38'),(5,'123',NULL,'8ae2553f3f8e5a3bb3760162c68354a0b5e83607','managed','0.5000','1.0000','0.0500',2000,'2011-04-06 23:15:15');

/*Table structure for table `patterns` */

DROP TABLE IF EXISTS `patterns`;

CREATE TABLE `patterns` (
  `patternID` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `trainsetID` int(5) unsigned NOT NULL,
  `pattern` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `output` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`patternID`),
  KEY `train` (`trainsetID`),
  CONSTRAINT `patterns_ibfk_1` FOREIGN KEY (`trainsetID`) REFERENCES `trainsets` (`trainsetID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `patterns` */

/*Table structure for table `trainsets` */

DROP TABLE IF EXISTS `trainsets`;

CREATE TABLE `trainsets` (
  `trainsetID` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `networkID` int(5) unsigned NOT NULL,
  `label` varchar(20) NOT NULL,
  PRIMARY KEY (`trainsetID`),
  KEY `network` (`networkID`),
  CONSTRAINT `trainsets_ibfk_1` FOREIGN KEY (`networkID`) REFERENCES `networks` (`networkID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `trainsets` */

/*Table structure for table `usernetwork` */

DROP TABLE IF EXISTS `usernetwork`;

CREATE TABLE `usernetwork` (
  `userID` int(5) NOT NULL,
  `networkID` int(5) unsigned NOT NULL,
  PRIMARY KEY (`userID`,`networkID`),
  KEY `networkID` (`networkID`),
  CONSTRAINT `usernetwork_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `usernetwork_ibfk_2` FOREIGN KEY (`networkID`) REFERENCES `networks` (`networkID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `usernetwork` */

insert  into `usernetwork`(`userID`,`networkID`) values (1,5);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userID` int(5) NOT NULL AUTO_INCREMENT,
  `userName` varchar(50) NOT NULL,
  `userPass` varchar(41) NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `userName` (`userName`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`userID`,`userName`,`userPass`) values (1,'admin','*4ACFE3202A5FF5CF467898FC58AAB1D615029441');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
