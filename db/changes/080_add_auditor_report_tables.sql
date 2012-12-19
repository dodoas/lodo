CREATE TABLE  `auditorreport` (
`AuditorReportID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`PeriodYear` CHAR( 4 ) NOT NULL ,
UNIQUE (`PeriodYear`)
) ENGINE = MYISAM ;

CREATE TABLE  `auditorreportline` (
`AuditorReportLineID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`AuditorReportID` INT( 11 ) NOT NULL ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`AuditAmount` DECIMAL( 10, 2 ) NOT NULL ,
`TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
UNIQUE (`AuditorReportID`,`AccountPlanID`)
) ENGINE = MYISAM ;
