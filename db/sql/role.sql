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
-- Dumping data for table `role`
--


/*!40000 ALTER TABLE `role` DISABLE KEYS */;
LOCK TABLES `role` WRITE;
INSERT INTO `role` VALUES (1,'Intranett','intranett1','Vanlig medarbeider','2005-05-13 10:23:05',1),(2,'Extranett partner','extranett1','Partner','2005-05-13 10:23:05',1),(3,'Internett','internett1','Uinlogget internett aktivitet','2005-05-13 10:23:05',1),(4,'Extranett kunde','extranett1','Kundetilgang - prosjekt / faktura, etc','2005-05-13 10:23:05',1),(5,'Lodo','lodo','Regnskapssystem','2005-05-13 11:11:37',1),(6,'Shop','shop1','Simple WebShop','2005-05-13 10:23:05',1),(7,'Technical','technical','Tekniske instillinger','2005-05-13 14:29:23',1),(8,'Intranett - partner','intranett1','Tilgang for partnere i firma','2005-05-13 10:23:05',1),(9,'Kindergarten','kindergarten1',NULL,'2005-05-13 10:23:05',1),(10,'Uinnlogget','internett1','Tilgang f¿r innlogging','2005-05-13 10:23:05',1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

