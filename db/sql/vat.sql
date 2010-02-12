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
-- Dumping data for table `vat`
--


/*!40000 ALTER TABLE `vat` DISABLE KEYS */;
LOCK TABLES `vat` WRITE;
INSERT INTO `vat` VALUES (0,10,2700,1,1,'1990-01-01','9999-12-31','2006-06-04 14:57:37','sale',11,1),(25,11,2701,1,0,'2005-01-01','9999-12-31','2005-05-19 14:52:33','sale',12,2),(8,13,2703,1,0,'2006-01-01','9999-12-31','2005-12-28 11:15:08','sale',14,3),(11,12,2702,1,0,'2005-01-01','2005-12-31','2005-12-28 11:12:39','sale',13,4),(0,14,0,1,0,'2005-01-01','9999-12-31','2007-02-09 08:22:45','sale',15,5),(0,15,0,1,0,'2005-01-01','9999-12-31','2007-02-09 08:22:55','sale',16,6),(0,16,0,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:03','sale',17,7),(0,17,0,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:06','sale',18,8),(0,18,0,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:10','sale',19,9),(0,19,0,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:14','sale',20,10),(0,40,2710,1,1,'1990-01-01','9999-12-31','2006-06-04 14:57:37','buy',1,11),(25,41,2711,1,0,'2005-01-01','9999-12-31','2005-05-19 14:52:33','buy',2,12),(8,43,2713,1,0,'2006-01-01','9999-12-31','2005-12-28 11:15:08','buy',3,14),(0,44,2712,1,0,'2005-01-01','9999-12-31','2007-02-09 08:22:45','buy',5,15),(0,45,2715,1,0,'2005-01-01','9999-12-31','2007-02-09 08:22:55','buy',6,16),(0,46,2716,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:03','buy',7,17),(0,47,2717,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:06','buy',8,18),(0,48,2718,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:10','buy',9,19),(0,49,2719,1,0,'2005-01-01','9999-12-31','2007-02-09 08:23:14','buy',10,20),(0,60,2700,1,0,'1990-01-01','9999-12-31','2005-11-27 09:38:10','buy',22,21),(NULL,30,0,1,0,'1990-01-01','9999-12-31','2005-11-27 09:38:10','sale',21,22),(24,11,2701,1,0,'1990-01-01','2004-12-31','2005-12-27 23:54:32','sale',26,23),(12,12,2702,1,0,'1990-01-01','2004-12-31','2005-12-27 23:54:38','sale',27,24),(6,13,2703,1,0,'1990-01-01','2004-12-31','2005-12-27 23:54:46','sale',28,25),(24,41,2711,1,0,'1990-01-01','2004-12-31','2005-12-27 23:54:32','buy',23,26),(12,42,2712,1,0,'1990-01-01','2004-12-31','2005-12-27 23:54:38','buy',24,27),(6,43,2713,1,0,'1990-01-01','2004-12-31','2005-12-27 23:54:46','buy',25,28),(11,42,2712,1,0,'2005-01-01','2005-12-31','2005-12-28 11:12:39','buy',4,13),(NULL,32,NULL,1,0,'1990-01-01','9999-12-31','2005-12-28 00:04:46','sale',30,29),(NULL,62,NULL,1,0,'1990-01-01','9999-12-31','2005-12-28 00:04:46','buy',29,30),(14,12,2702,1,0,'2007-01-01','9999-12-31','2006-12-26 19:13:01','sale',32,31),(14,42,2712,1,0,'2007-01-01','9999-12-31','2006-12-26 19:13:01','buy',31,32),(7,13,2703,1,0,'2005-01-01','2005-12-31','2005-12-28 11:14:34','sale',34,33),(7,43,2713,1,0,'2005-01-01','2005-12-31','2006-01-04 08:58:08','buy',33,34),(13,12,2702,1,0,'2006-01-01','2006-12-31','2006-12-26 16:36:11','sale',36,35),(13,42,2712,1,0,'2006-01-01','2006-12-31','2007-01-03 10:13:42','buy',35,36);
UNLOCK TABLES;
/*!40000 ALTER TABLE `vat` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

