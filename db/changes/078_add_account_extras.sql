CREATE TABLE  `accountextras` (
`AccountExtrasID` INT( 11 ) NOT NULL ,
`AccountID` INT( 11 ) NOT NULL ,
`Period` CHAR( 7 ) NOT NULL ,
`BankEntryIn` DECIMAL( 10, 2 ) NOT NULL ,
`BankEntryOut` DECIMAL( 10, 2 ) NOT NULL ,
`BankLastIn` DECIMAL( 10, 2 ) NOT NULL ,
`BankLastOut` DECIMAL( 10, 2 ) NOT NULL
) ENGINE = MYISAM ;
ALTER TABLE  `accountextras` ADD PRIMARY KEY (  `AccountExtrasID` );
ALTER TABLE  `accountextras` CHANGE  `AccountExtrasID`  `AccountExtrasID` INT( 11 ) NOT NULL AUTO_INCREMENT;
