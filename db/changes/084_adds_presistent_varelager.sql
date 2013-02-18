ALTER TABLE  `varelagerline` ADD  `Department` VARCHAR( 128 ) NOT NULL ,
ADD  `Project` VARCHAR( 128 ) NOT NULL ,
ADD  `Shelf` VARCHAR( 128 ) NOT NULL ,
ADD  `UnitSize` FLOAT NOT NULL ,
ADD  `BulkSize` INT NOT NULL ,
ADD  `RealProductNumber` VARCHAR( 128 ) NOT NULL COMMENT  'ProductNr has been used for ProductID'
