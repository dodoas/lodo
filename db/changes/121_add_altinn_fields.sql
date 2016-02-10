-- Add missing altinn fields on employee
ALTER TABLE accountplan
ADD ShiftType varchar(50) DEFAULT 'ikkeSkift',
ADD Workmeasurement decimal(4,2)  DEFAULT 0,
ADD WorkTimeScheme varchar(50) DEFAULT '',
ADD TypeOfEmployment varchar(50) DEFAULT 'ordinaertArbeidsforhold',

ADD OccupationID bigint(20) DEFAULT 0,
ADD SubcompanyID bigint(20) DEFAULT 0,
ADD CreditDaysUpdatedAt date,
-- TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

ADD WorkPercentUpdatedAt date,
ADD inCurrentPositionSince date
;

UPDATE accountplan SET CreditDaysUpdatedAt=WorkStart, WorkPercentUpdatedAt=WorkStart, inCurrentPositionSince=WorkStart WHERE AccountPlanType LIKE '%employee%';

-- Add missing altinn fields on salary
ALTER TABLE salary
ADD ShiftType varchar(50),
ADD WorkTimeScheme varchar(50),
ADD TypeOfEmployment varchar(50),
ADD OccupationID bigint(20),
ADD SubcompanyID bigint(20),
ADD ActualPayDate date DEFAULT NULL
;

-- Add missing altinn fields on salaryconfline
ALTER TABLE salaryconfline
ADD SalaryDescription varchar(100)
;

-- Add missing altinn fields on salaryline
ALTER TABLE salaryline
ADD SalaryDescription varchar(100)
;

-- Add new field CalculationCodeForTax to company table
ALTER TABLE company
ADD CalculationCodeForTax varchar(60) DEFAULT '',
-- Link between company and Altinn
ADD AltinnSystemUsername varchar(10) DEFAULT '',
ADD AltinnSystemPassword varchar(60) DEFAULT ''
;

-- Add missing altinn fields on salaryconfline
ALTER TABLE person
-- there are only two valid optins AltinnPin and SMSPin
ADD AltinnAuthMethod varchar(100) NOT NULL DEFAULT 'SMSPin',
-- this es 'personnummer' same as social security number
ADD AltinnUserSSN varchar(100)
;

-- Set SMSPin to all existing persons
UPDATE person SET AltinnAuthMethod = 'SMSPin';

-- Set default WorkPercent and update all existing with blank or empty to 100.00
ALTER TABLE accountplantemplate CHANGE WorkPercent WorkPercent DECIMAL(16,2) NULL DEFAULT '100.00';
UPDATE accountplantemplate SET WorkPercent = 100.00 WHERE WorkPercent = 0.00 OR WorkPercent = '' OR WorkPercent IS NULL;


DROP TABLE IF EXISTS occupation;
-- Create new tables for Occupation, we will fetch data from ssb.no/a/yrke
CREATE TABLE IF NOT EXISTS occupation (
OccupationID int(11) NOT NULL AUTO_INCREMENT,
Active tinyint(4) NOT NULL DEFAULT '0',
YNr varchar(50) DEFAULT NULL,
LNr varchar(50) DEFAULT NULL,
Name varchar(255) NOT NULL DEFAULT '',
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (OccupationID)
);


-- Add table to store info from first report
DROP TABLE IF EXISTS altinnReport1;
-- Create new tables for AltinnReport1
CREATE TABLE IF NOT EXISTS altinnReport1 (
AltinnReport1ID int(11) NOT NULL AUTO_INCREMENT,
Folder varchar(50) DEFAULT NULL,
Period varchar(7) DEFAULT NULL,
ReceiptId int(16) NOT NULL,
ParentReceiptId int(16) DEFAULT NULL,
ReceiptText text NOT NULL,
ReceiptHistory text NOT NULL,
LastChanged datetime DEFAULT NULL,
ReceiptTypeName varchar(50) DEFAULT NULL,
ReceiptStatusCode varchar(50) DEFAULT NULL,
ExternalShipmentReference varchar(50) DEFAULT NULL,
OwnerPartyReference varchar(50) DEFAULT NULL,
PartyReference varchar(50) DEFAULT NULL,
-- This report is  ...
MeldingsId varchar(100) DEFAULT NULL,
-- This report is replacing ...
ErstatterMeldingsId varchar(100) DEFAULT NULL,
-- This report got replaced with ...
ReplacedByMeldindsID varchar(100) DEFAULT NULL,
PRIMARY KEY (AltinnReport1ID)
);

-- Add table to store info from first report
DROP TABLE IF EXISTS altinnReport1salary;
-- Create new tables for altinnReport1salary

CREATE TABLE IF NOT EXISTS altinnReport1salary (
AltinnReport1ID int(11) NOT NULL,
SalaryId int(11) NOT NULL,
JournalID int(11) NOT NULL,
UpdatedAt timestamp NOT NULL,
Changed tinyint(4) DEFAULT 0 NOT NULL,
PRIMARY KEY (AltinnReport1ID, SalaryId)
);

-- Add table to store info from second report
DROP TABLE IF EXISTS altinnReport2;
-- Create new tables for altinnReport2
CREATE TABLE IF NOT EXISTS altinnReport2 (
AltinnReport2ID int(11) NOT NULL AUTO_INCREMENT,
req_ReceiptId varchar(50) DEFAULT NULL,

res_LastChanged datetime DEFAULT NULL,
res_ParentReceiptId int(16) DEFAULT NULL,
res_ReceiptHistory text NOT NULL,
res_ReceiptId int(16) NOT NULL,
res_ReceiptStatus varchar(50) DEFAULT NULL,
res_ReceiptTemplate varchar(50) DEFAULT NULL,
res_ReceiptText text NOT NULL,
res_ReceiptType varchar(50) DEFAULT NULL,
res_ExternalShipmentReference varchar(50) DEFAULT NULL,
res_OwnerPartyReference varchar(50) DEFAULT NULL,
res_WorkFlowReference varchar(50) DEFAULT NULL,
res_ReceiversReference varchar(50) DEFAULT NULL,
res_ArchiveReference varchar(50) DEFAULT NULL,
res_PartyReferenceA varchar(50) DEFAULT NULL,
res_PartyReferenceB varchar(50) DEFAULT NULL,

res_SubReceiptsLastChanged datetime DEFAULT NULL,
res_SubReceiptsParentReceiptId int(16) DEFAULT NULL,
res_SubReceiptsReceiptHistory text DEFAULT NULL,
res_SubReceiptsReceiptId int(16) DEFAULT NULL,
res_SubReceiptsReceiptStatus varchar(50) DEFAULT NULL,
res_SubReceiptsReceiptTemplate varchar(50) DEFAULT NULL,
res_SubReceiptsReceiptText varchar(255) DEFAULT NULL,
res_SubReceiptsReceiptType varchar(50) DEFAULT NULL,
res_SubReceiptsSendersReference varchar(50) DEFAULT NULL,

PRIMARY KEY (AltinnReport2ID)
);


-- Add table to store info from first report
DROP TABLE IF EXISTS altinnReport3;
-- Create new tables for altinnReport3
CREATE TABLE IF NOT EXISTS altinnReport3 (
AltinnReport3ID int(11) NOT NULL AUTO_INCREMENT,
res_Message varchar(255) DEFAULT NULL,
res_Status varchar(50) DEFAULT NULL,
res_ValidFrom datetime DEFAULT NULL,
res_ValidTo datetime DEFAULT NULL,
res_KodeNr int(5) DEFAULT NULL,
PRIMARY KEY (AltinnReport3ID)
);




-- Add table to store info from second report
DROP TABLE IF EXISTS altinnReport4;
-- Create new tables for altinnReport4
CREATE TABLE IF NOT EXISTS altinnReport4 (
AltinnReport4ID int(11) NOT NULL AUTO_INCREMENT,
Folder varchar(50) DEFAULT NULL,
req_CorrespondenceID varchar(50) DEFAULT NULL,

res_AllowForwarding tinyint(4) NOT NULL DEFAULT '0',
res_ArchiveReference varchar(50) DEFAULT NULL,
res_AuthenticatedUser varchar(255) DEFAULT NULL,
res_CaseID varchar(255) DEFAULT NULL,
res_ConfirmationDate datetime DEFAULT NULL,
res_CorrespondenceID int(16) DEFAULT NULL,
res_CorrespondenceName varchar(50) DEFAULT NULL,
res_CorrespondenceStatus varchar(50) DEFAULT NULL,
res_CorrespondenceSubject varchar(50) DEFAULT NULL,
res_CorrespondenceSummary varchar(50) DEFAULT NULL,
res_CorrespondenceTitle varchar(255) DEFAULT NULL,
res_CorrespondenceTxt varchar(255) DEFAULT NULL,
res_CustomMessageData varchar(255) DEFAULT NULL,
res_DateSent datetime DEFAULT NULL,
res_Description varchar(255) DEFAULT NULL,
res_DueDate datetime DEFAULT NULL,
res_ExternalSystemReference varchar(255) DEFAULT NULL,
res_Header varchar(255) DEFAULT NULL,
res_IsConfirmationNeeded tinyint(4) NOT NULL DEFAULT '0',
res_LanguageID varchar(50) DEFAULT NULL,
res_Reportee int(16) DEFAULT NULL,
res_SentBy varchar(50) DEFAULT NULL,
res_SentTo int(16) DEFAULT NULL,
res_UserID int(16) DEFAULT NULL,

res_AttachmentData text NOT NULL,
res_AttachmentFunctionTypeID varchar(255) NOT NULL,
res_AttachmentID int(16) NOT NULL,
res_AttachmentName varchar(255) DEFAULT NULL,
res_AttachmentTypeID varchar(255) DEFAULT NULL,
res_CreatedByUserID int(16) NOT NULL,
res_CreatedDateTime datetime NOT NULL,
res_DestinationType varchar(255) DEFAULT NULL,
res_FileName varchar(255) DEFAULT NULL,
res_IsAddedAfterFormFillin tinyint(4) NOT NULL DEFAULT '0',
res_IsAssociatedToFormSet tinyint(4) NOT NULL DEFAULT '0',
res_IsEncrypted tinyint(4) NOT NULL DEFAULT '0',
res_ReporteeElementID int(16) NOT NULL,
res_SendersReference varchar(255) DEFAULT NULL,

PRIMARY KEY (AltinnReport4ID)
);


-- Add table to store info from second report
DROP TABLE IF EXISTS altinnReport5;
-- Create new tables for altinnReport5
CREATE TABLE IF NOT EXISTS altinnReport5 (
AltinnReport5ID int(11) NOT NULL AUTO_INCREMENT,
req_CorrespondenceID varchar(50) DEFAULT NULL,

res_LastChanged datetime DEFAULT NULL,
res_ParentReceiptId int(16) DEFAULT NULL,
res_ReceiptHistory text NOT NULL,
res_ReceiptId int(16) NOT NULL,
res_ReceiptStatusCode varchar(50) DEFAULT NULL,
res_ReceiptTemplate varchar(50) DEFAULT NULL,
res_ReceiptText text NOT NULL,
res_ReceiptTypeName varchar(50) DEFAULT NULL,
res_ExternalShipmentReference varchar(50) DEFAULT NULL,
res_OwnerPartyReference varchar(50) DEFAULT NULL,

PRIMARY KEY (AltinnReport5ID)
);

DROP TABLE IF EXISTS subcompany;
-- Create new table for Subcompany
CREATE TABLE IF NOT EXISTS subcompany (
SubcompanyID int(11) NOT NULL AUTO_INCREMENT,
Name varchar(255) NOT NULL DEFAULT '',
OrgNumber varchar(20) NOT NULL DEFAULT '0',
TS timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (SubcompanyID)
);


-- -- ALTER TABLE accountplan
-- -- DROP ShiftType,
-- -- DROP Workmeasurement,
-- -- DROP WorkTimeScheme,
-- -- DROP OccupationID,
-- -- DROP CreditDaysUpdatedAt,
-- -- DROP WorkPercentUpdatedAt
-- -- DROP inCurrentPositionSince
-- -- ;
-- --
-- -- ALTER TABLE salary
-- -- DROP ActualPayDate
-- -- ;
