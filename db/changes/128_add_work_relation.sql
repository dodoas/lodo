-- Create new table for Work relations
CREATE TABLE IF NOT EXISTS workrelation (
WorkRelationID int(11) NOT NULL AUTO_INCREMENT,
AccountPlanID int(11) NOT NULL,
OccupationID int(11) NOT NULL,
SubcompanyID int(11) NOT NULL,
KommuneID int(11) NOT NULL,
WorkStart date,
WorkStop date,
InCurrentPositionSince date,
WorkTimeScheme varchar(50) DEFAULT '',
ShiftType varchar(50) DEFAULT 'ikkeSkift',
TypeOfEmployment varchar(50) DEFAULT 'ordinaertArbeidsforhold',
WorkPercent decimal(16,2) DEFAULT 0,
WorkMeasurement decimal(4,2) DEFAULT 0,
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (WorkRelationID)
);

-- Migrate existing information to new table for work relations
INSERT INTO workrelation(AccountPlanID, OccupationID, SubcompanyID, KommuneID, WorkStart, WorkStop, InCurrentPositionSince, WorkTimeScheme, ShiftType, TypeOfEmployment, WorkPercent, WorkMeasurement)
SELECT AccountPlanID, OccupationID, SubcompanyID, KommuneID, WorkStart, WorkStop, inCurrentPositionSince, WorkTimeScheme, ShiftType, TypeOfEmployment, WorkPercent, Workmeasurement
FROM accountplan
WHERE AccountPlanType = 'employee';
