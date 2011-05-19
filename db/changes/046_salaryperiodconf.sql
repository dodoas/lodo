CREATE TABLE `salaryperiodconf` (
`SalaryperiodconfID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Year` INT( 4 ) NOT NULL ,
`Name` VARCHAR( 64 ) NOT NULL ,
`Period` CHAR( 7 ) NOT NULL ,
`Voucherdate` CHAR( 10 ) NOT NULL ,
`Fromdate` DATE NOT NULL ,
`Todate` DATE NOT NULL ,
`Active` BOOL NOT NULL
) ENGINE = MYISAM;

CREATE TABLE `salaryperiodentries` (
`SalaryperiodconfID` INT( 11 ) NOT NULL,
`JournalID` INT( 11 ) NOT NULL ,
`SalaryID` INT( 11 ) NOT NULL ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`Processed` BOOL NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `salaryperiodentries` ADD INDEX ( `SalaryperiodconfID` );
ALTER TABLE `salaryconfline` ADD `Active` BOOL NOT NULL DEFAULT '1';
ALTER TABLE `salaryinfo` ADD `SalaryperiodconfID` INT( 11 ) NOT NULL ;
ALTER TABLE `salary` ADD `InternComment` TEXT NOT NULL ;
