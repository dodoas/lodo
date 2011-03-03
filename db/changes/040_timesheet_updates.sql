ALTER TABLE `timesheets` ADD `Locked` BOOL NOT NULL ;
ALTER TABLE `timesheets` ADD `Diet` INT( 20 ) NOT NULL ;
ALTER TABLE `timesheets` ADD `Accommodation` INT( 20 ) NOT NULL ;

ALTER TABLE `timesheets` ADD `TravelRoute` VARCHAR( 255 ) NOT NULL ,
ADD `TravelDesc` VARCHAR( 255 ) NOT NULL ,
ADD `TravelDistance` INT( 20 ) NOT NULL ;

CREATE TABLE `timesheetdiet` (
`DietID` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Name` VARCHAR( 128 ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `timesheetaccommodation` (
`AccommodationID` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Name` VARCHAR( 128 ) NOT NULL
) ENGINE = MYISAM ;

