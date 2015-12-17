-- Add missing altinn fields on employee
ALTER TABLE accountplan
ADD ShiftType varchar(50) DEFAULT 'ikkeShift',
ADD Workmeasurement decimal(4,2)  DEFAULT 0,
ADD WorkTimeScheme varchar(50) DEFAULT '',
ADD TypeOfEmployment varchar(50) DEFAULT 'Ordinaer',

ADD OccupationCodeID bigint(20) DEFAULT 0,
ADD CreditDaysUpdatedAt timestamp,
-- TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

ADD WorkPercentUpdatedAt timestamp,
ADD inCurrentPositionSince date
;

-- Create new tables for Occupation, we will fetch data from ssb.no/a/yrke
CREATE TABLE IF NOT EXISTS occupation (
OccupationID int(11) NOT NULL AUTO_INCREMENT,
Active tinyint(4) NOT NULL DEFAULT '0',
YNr varchar(50) DEFAULT NULL,
LNr varchar(50) DEFAULT NULL,
Name varchar(255) NOT NULL DEFAULT '',
RemoteID int(6) DEFAULT NULL,
RemoteLastUpdatedAt datetime DEFAULT NULL,
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (OccupationID)
);


-- ALTER TABLE accountplan
-- DROP ShiftType,
-- DROP Workmeasurement,
-- DROP WorkTimeScheme,
-- DROP OccupationCodeID,
-- DROP CreditDaysUpdatedAt,
-- DROP WorkPercentUpdatedAt
-- DROP inCurentPositionSince
-- ;

-- Drop TABLE occupation;
