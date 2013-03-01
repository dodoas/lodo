CREATE TABLE  `weeklysaletemplate` (
`WeeklySaleTemplateID` INT( 11 ) NOT NULL ,
`Year` INT( 5 ) NOT NULL ,
`WeekNo` INT( 2 ) NOT NULL ,
`LastDate` DATE NOT NULL ,
`Period` CHAR( 7 ) NOT NULL ,
`VoucherType` CHAR( 1 ) NOT NULL ,
`JournalID` INT( 11 ) NOT NULL ,
PRIMARY KEY (  `WeeklySaleTemplateID` )
) ENGINE = MYISAM ;
ALTER TABLE  `weeklysaletemplate` CHANGE  `WeeklySaleTemplateID`  `WeeklySaleTemplateID` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE  `weeklysaletemplate` ADD  `WeeklySaleConfID` INT NOT NULL;
ALTER TABLE  `weeklysaletemplate` ADD  `FirstDate` DATE NOT NULL AFTER  `WeekNo`;
ALTER TABLE  `weeklysaletemplate` ADD  `WeeklySaleID` INT( 11 ) NOT NULL AFTER  `WeeklySaleTemplateID`;
