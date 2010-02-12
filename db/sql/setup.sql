-- MySQL dump 10.9
--
-- Host: localhost    Database: empatix
-- ------------------------------------------------------
-- Server version	4.1.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `setup`
--


/*!40000 ALTER TABLE `setup` DISABLE KEYS */;
LOCK TABLES `setup` WRITE;
INSERT INTO `setup` VALUES (1,'kasseinn','1900',NULL,'2005-03-05 17:23:23','','',0,0,'0000-00-00 00:00:00',0,0),(2,'kasseut','1900',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(3,'bankinn','1920',NULL,'2004-10-07 16:54:55','','',0,0,'0000-00-00 00:00:00',0,0),(4,'bankut','1920',NULL,'2006-11-27 21:53:41','','',0,0,'0000-00-00 00:00:00',0,0),(5,'fakturakontantut','1910',NULL,'2004-03-23 09:58:16','','',0,0,'0000-00-00 00:00:00',0,0),(6,'buycashut','1900',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(7,'buycashreskontro','900000',NULL,'2008-04-23 15:34:45','','',0,0,'0000-00-00 00:00:00',0,0),(8,'buycashutgift','4000',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(9,'buycreditreskontro','900000',NULL,'2008-04-23 15:34:45','','',0,0,'0000-00-00 00:00:00',0,0),(10,'buycreditutgift','4000',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(11,'buynotacashinn','1900',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(12,'buynotacashreskontro','50000',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(13,'buynotacashutgift','4000',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(14,'buynotacreditreskontro','50000',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(15,'buynotacreditutgift','4000',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(16,'salecashut','1900',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(17,'salecashreskontro','10000',NULL,'2004-09-13 13:49:05','','',0,0,'0000-00-00 00:00:00',0,0),(18,'salecashinntekt','3000',NULL,'2004-04-16 15:27:36','','',0,0,'0000-00-00 00:00:00',0,0),(19,'salecreditreskontro','10000',NULL,'2004-09-13 13:49:05','','',0,0,'0000-00-00 00:00:00',0,0),(20,'salecreditinntekt','3000',NULL,'2004-04-16 15:27:36','','',0,0,'0000-00-00 00:00:00',0,0),(21,'salenotacashut','1900',NULL,'2004-05-13 08:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(22,'salenotacashreskontro','10000',NULL,'2004-03-23 10:07:51','','',0,0,'0000-00-00 00:00:00',0,0),(23,'salenotacashinntekt','3000',NULL,'2004-04-16 15:27:36','','',0,0,'0000-00-00 00:00:00',0,0),(24,'salenotacreditreskontro','10000',NULL,'2004-03-23 10:07:52','','',0,0,'0000-00-00 00:00:00',0,0),(25,'salenotacreditinntekt','3000',NULL,'2004-04-16 15:27:36','','',0,0,'0000-00-00 00:00:00',0,0),(26,'expectedflowTax','0.35',NULL,'2004-07-06 16:24:00','','',0,0,'0000-00-00 00:00:00',0,0),(27,'expectedflowPayedTaxTerm1','12000',NULL,'2004-07-06 16:24:00','','',0,0,'0000-00-00 00:00:00',0,0),(28,'expectedflowPayedTaxTerm2','12000',NULL,'2004-07-06 16:24:00','','',0,0,'0000-00-00 00:00:00',0,0),(29,'expectedflowPayedTaxTerm3','12000',NULL,'2004-07-06 16:24:00','','',0,0,'0000-00-00 00:00:00',0,0),(30,'expectedflowPayedTaxTerm4','12000',NULL,'2004-07-06 16:24:00','','',0,0,'0000-00-00 00:00:00',0,0),(31,'salarydeftodate','2008-02-28',NULL,'2008-06-19 09:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(32,'salarydeffromdate','2008-02-01',NULL,'2008-06-19 09:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(33,'salarydefvoucherdate','2008-02-01',NULL,'2008-06-19 09:23:22','','',0,0,'0000-00-00 00:00:00',0,0),(34,'kid','0','invoice','2008-10-21 08:23:35','','',0,0,'0000-00-00 00:00:00',0,0),(35,'username','phplodo','orgnumberlookup','2008-10-21 08:23:35','','',0,0,'0000-00-00 00:00:00',0,0),(36,'password','nX9GxGhNx2vD2M','orgnumberlookup','2008-10-21 08:23:35','','',0,0,'0000-00-00 00:00:00',0,0),(37,'status','approved','fakturabank','2008-10-21 08:23:35','','',0,0,'0000-00-00 00:00:00',0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `setup` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

