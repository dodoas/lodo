CREATE TABLE IF NOT EXISTS `workrelationfurlough` (
  `FurloughID` int(11) NOT NULL AUTO_INCREMENT,
  `WorkRelationID` int(11) NOT NULL,
  `FurloughText` varchar(50) NOT NULL,
  `FurloughStart` date DEFAULT NULL,
  `FurloughStop` date DEFAULT NULL,
  `FurloughPercent` decimal(16,2) DEFAULT '0.00',
  `FurloughDescription` varchar(50) NOT NULL,
  `TS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`FurloughID`)
);
