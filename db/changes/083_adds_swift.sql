CREATE TABLE  `accountplanswift` (
`AccountPlanID` INT NOT NULL ,
`Swift` CHAR( 64 ) NOT NULL ,
`SwiftAccount` CHAR( 128 ) NOT NULL ,
PRIMARY KEY (  `AccountPlanID` )
) ENGINE = MYISAM ;
