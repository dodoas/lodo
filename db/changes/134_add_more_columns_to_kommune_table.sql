-- Add more columns to kommune table and change Sone column to string since it can be for example 1a
ALTER TABLE kommune
ADD County varchar(255) DEFAULT '',
ADD BankAccountNumber varchar(255) DEFAULT '',
ADD OrgNumber varchar(20) DEFAULT '',
ADD OrgName varchar(255) DEFAULT '',
ADD OrganisationForm varchar(255) DEFAULT '',
ADD Comments text DEFAULT '',
MODIFY Sone varchar(10);

-- Alter AGA zone code to string
ALTER TABLE arbeidsgiveravgift
MODIFY Code varchar(10) NOT NULL;

-- Update so only the first (lowest id) of the kommunes with the same number are used,
-- then we can delete the duplicates
UPDATE accountplan ap
SET ap.KommuneID = (
  SELECT MIN(KommuneID)
  FROM kommune
  WHERE KommuneNumber IN (
    SELECT KommuneNumber
    FROM kommune
    WHERE KommuneID = ap.KommuneID
  )
);

UPDATE accountplantemplate apt
SET apt.KommuneID = (
  SELECT MIN(KommuneID)
  FROM kommune
  WHERE KommuneNumber IN (
    SELECT KommuneNumber
    FROM kommune
    WHERE KommuneID = apt.KommuneID
  )
);

UPDATE company c
SET c.CompanyMunicipalityID = (
  SELECT MIN(KommuneID)
  FROM kommune
  WHERE KommuneNumber IN (
    SELECT KommuneNumber
    FROM kommune
    WHERE KommuneID = c.CompanyMunicipalityID
  )
);

UPDATE salary s
SET s.KommuneID = (
  SELECT MIN(KommuneID)
  FROM kommune
  WHERE KommuneNumber IN (
    SELECT KommuneNumber
    FROM kommune
    WHERE KommuneID = s.KommuneID
  )
);

-- Remove duplicate kommune entries
-- Uses a subselect trick in order to have the
-- same tabe we delete from in the where query
DELETE FROM kommune WHERE KommuneID IN (
  SELECT k1.KommuneID
  FROM (
    SELECT * FROM kommune
  ) k1 JOIN (
    SELECT * FROM kommune
  ) k2 ON (k1.KommuneNumber = k2.KommuneNumber AND k1.KommuneID > k2.KommuneID)
);

