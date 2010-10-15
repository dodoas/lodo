ALTER TABLE  `weeklysale` ADD  `Bank1Amount` DECIMAL( 16, 2 ) NOT NULL ,
ADD  `Bank2Amount` DECIMAL( 16, 2 ) NOT NULL ,
ADD  `Bank3Amount` DECIMAL( 16, 2 ) NOT NULL ,
ADD  `Bank1Explanation` VARCHAR( 255 ) NOT NULL ,
ADD  `Bank2Explanation` VARCHAR( 255 ) NOT NULL ,
ADD  `Bank3Explanation` VARCHAR( 255 ) NOT NULL ,
ADD  `Bank1Date` DATE NOT NULL ,
ADD  `Bank2Date` DATE NOT NULL ,
ADD  `Bank3Date` DATE NOT NULL ,
ADD  `PrivateDate` DATE NOT NULL ;


ALTER TABLE  `weeklysaleconf` ADD  `StartDate` DATE NOT NULL ,
ADD  `EndDate` DATE NOT NULL ;
