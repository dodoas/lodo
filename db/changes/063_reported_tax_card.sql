CREATE TABLE  `reportedtaxcard` (
`ReportedTaxCardID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`Year` INT( 4 ) NOT NULL ,
`Date` DATE NOT NULL
) ENGINE = MYISAM;
