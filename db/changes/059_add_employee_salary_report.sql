CREATE TABLE  `salaryreport` (
`SalaryReportID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Date` DATE NOT NULL ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`Locked` BOOL NOT NULL ,
`LockedBy` INT( 11 ) NOT NULL
) ENGINE = MYISAM;


CREATE TABLE  `salaryreportentries` (
`SalaryReportEntryID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SalaryReportID` INT( 11 ) NOT NULL ,
`Code` VARCHAR( 7 ) NOT NULL ,
`Amount` FLOAT NOT NULL
) ENGINE = MYISAM;

CREATE TABLE  `salaryreportaccount` (
`SalaryReportAccountID` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`Year` YEAR NOT NULL ,
`Locked` BOOL NOT NULL ,
`LockedBy` INT( 11 ) NOT NULL ,
PRIMARY KEY (  `SalaryReportAccountID` )
) ENGINE = MYISAM;

CREATE TABLE  `salaryreportaccountentries` (
`SalaryReportAccountEntryID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SalaryReportAccountID` INT( 11 ) NOT NULL ,
`Code` VARCHAR( 7 ) NOT NULL ,
`Amount` FLOAT NOT NULL
) ENGINE = MYISAM;

