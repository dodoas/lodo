CREATE TABLE `salaryextra` (
`SalaryID` INT( 11 ) NOT NULL ,
`AccountName` VARCHAR( 255 ) NOT NULL ,
`Address` VARCHAR( 255 ) NOT NULL ,
`City` VARCHAR( 50 ) NOT NULL ,
`ZipCode` VARCHAR( 10 ) NOT NULL ,
`SocietyNumber` VARCHAR( 15 ) NOT NULL ,
`TabellTrekk` VARCHAR( 10 ) NOT NULL ,
`ProsentTrekk` VARCHAR( 10 ) NOT NULL ,
`MunicipalityPercent` FLOAT NOT NULL
) ENGINE = MYISAM ;
ALTER TABLE  `salaryextra` ADD PRIMARY KEY (  `SalaryID` ) ;
