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
WorkPercentUpdatedAt date,
WorkMeasurement decimal(4,2) DEFAULT 0,
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (WorkRelationID)
);

-- Migrate existing information to new table for work relations
INSERT INTO workrelation(AccountPlanID, OccupationID, SubcompanyID, KommuneID, WorkStart, WorkStop, InCurrentPositionSince, WorkTimeScheme, ShiftType, TypeOfEmployment, WorkPercent, WorkMeasurement)
SELECT AccountPlanID, OccupationID, SubcompanyID, KommuneID, WorkStart, WorkStop, inCurrentPositionSince, WorkTimeScheme, ShiftType, TypeOfEmployment, WorkPercent, Workmeasurement
FROM accountplan
WHERE AccountPlanType = 'employee';

-- Add AltinnReport1WorkRelation table, to store links for employee's work relation included in report
DROP TABLE IF EXISTS altinnReport1WorkRelation;
CREATE TABLE IF NOT EXISTS altinnReport1WorkRelation (
AltinnReport1ID int(11) NOT NULL,
WorkRelationID int(11) NOT NULL,
PRIMARY KEY (AltinnReport1ID, WorkRelationID)
);

-- Migrate existing information from altinnReport1employee to
-- altinnReport1WorkRelation table linking the newly created working relation to
-- the employee it belongs to
INSERT INTO altinnReport1WorkRelation(AltinnReport1ID, WorkRelationID)
SELECT ar1e.AltinnReport1ID, wr.WorkRelationID
FROM altinnReport1employee ar1e JOIN accountplan a ON ar1e.AccountPlanID = a.AccountPlanID JOIN workrelation wr ON wr.AccountPlanID = a.AccountPlanID ORDER BY wr.WorkRelationID LIMIT 1;

-- Remove no longer used table altinnReport1employee
DROP TABLE IF EXISTS altinnReport1employee;
