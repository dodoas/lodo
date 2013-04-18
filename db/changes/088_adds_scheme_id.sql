CREATE TABLE `fakturabankscheme` (
`FakturabankSchemeID` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`FakturabankRemoteSchemeID` INT( 11 ) NOT NULL ,
`SchemeType` CHAR( 64 ) NOT NULL ,
PRIMARY KEY (  `FakturabankSchemeID` )
) ENGINE = MYISAM ;

CREATE TABLE `accountplanscheme` (
`AccountPlanSchemeID` INT( 11 ) NOT NULL AUTO_INCREMENT,
`AccountPlanID` INT( 11 ) NOT NULL,
`FakturabankSchemeID` INT( 11 ) NOT NULL,
`SchemeValue` CHAR( 128 ) NOT NULL ,
PRIMARY KEY (  `AccountPlanSchemeID` )
) ENGINE = MYISAM;
