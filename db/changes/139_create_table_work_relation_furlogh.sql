CREATE TABLE IF NOT EXISTS `workrelationfurlough` (
  `FurloughID` int(11) NOT NULL AUTO_INCREMENT,
  `WorkRelationID` int(11) NOT NULL,
  `Text` varchar(50) NOT NULL,
  `Start` date DEFAULT NULL,
  `Stop` date DEFAULT NULL,
  `Percent` decimal(16,2) DEFAULT '0.00',
  `Description` varchar(50) NOT NULL,
  `TS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`FurloughID`)
);

CREATE TABLE IF NOT EXISTS `furloughtext` (
  `FurloughTextID` int(4) NOT NULL AUTO_INCREMENT,
  `Text` varchar(50) NOT NULL,
  PRIMARY KEY (`FurloughTextID`)
);
