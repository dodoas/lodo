-- Create new table for Work relations
DROP TABLE IF EXISTS workrelation;
CREATE TABLE IF NOT EXISTS workrelation (
WorkRelationID int(11) NOT NULL AUTO_INCREMENT,
AccountPlanID int(11) NOT NULL,
OccupationID int(11) NOT NULL,
SubcompanyID int(11) NOT NULL,
WorkStart date,
WorkStop date,
InCurrentPositionSince date,
WorkTimeScheme varchar(50) DEFAULT '',
ShiftType varchar(50) DEFAULT 'ikkeSkift',
TypeOfEmployment varchar(50) DEFAULT 'ordinaertArbeidsforhold',
WorkPercent decimal(16,2) DEFAULT 0,
WorkPercentUpdatedAt date,
WorkMeasurement decimal(4,2) DEFAULT 0,
SalaryDateChangedAt date,
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (WorkRelationID)
);

-- Migrate existing information to new table for work relations
INSERT INTO workrelation(AccountPlanID, OccupationID, SubcompanyID, WorkStart, WorkStop, InCurrentPositionSince, WorkTimeScheme, ShiftType, TypeOfEmployment, WorkPercent, WorkPercentUpdatedAt, WorkMeasurement, SalaryDateChangedAt)
SELECT AccountPlanID, OccupationID, SubcompanyID, WorkStart, WorkStop, inCurrentPositionSince, WorkTimeScheme, ShiftType, TypeOfEmployment, WorkPercent, WorkPercentUpdatedAt, Workmeasurement, CreditDaysUpdatedAt
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
FROM altinnReport1employee ar1e JOIN accountplan a ON ar1e.AccountPlanID = a.AccountPlanID JOIN workrelation wr ON wr.AccountPlanID = a.AccountPlanID ORDER BY wr.WorkRelationID;

-- Add work relartion foreign key to salary table
ALTER TABLE salary
ADD WorkRelationID int(11),
ADD WorkStart date,
ADD WorkStop date,
ADD InCurrentPositionSince date,
ADD WorkPercent decimal(16,2),
ADD WorkPercentUpdatedAt date,
ADD WorkMeasurement decimal(4,2),
ADD SalaryDateChangedAt date;

-- Populate the newly created WorkRelationID field based on employee for the salary
UPDATE salary s
INNER JOIN accountplan ap ON s.AccountPlanID = ap.AccountPlanID INNER JOIN workrelation wr ON wr.AccountPlanID = ap.AccountPlanID
SET s.WorkRelationID = wr.WorkRelationID;
