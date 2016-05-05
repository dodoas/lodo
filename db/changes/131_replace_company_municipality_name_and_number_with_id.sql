-- Add field for municipality ID in company table.
ALTER TABLE company
ADD CompanyMunicipalityID INT(11);

-- Populate this field with values from kommune table, based on current CompanyMunicipality number in company table.
UPDATE company AS c INNER JOIN kommune AS k
ON k.KommuneNumber = c.CompanyMunicipality
SET c.CompanyMunicipalityID = k.KommuneID;

-- Remove CompanyMunicipality and CompanyMunicipalityName from company table. These are replaced now with ID field.
ALTER TABLE company
DROP CompanyMunicipality,
DROP CompanyMunicipalityName;