-- Add field for municipality ID in company table.
ALTER TABLE company
ADD CompanyMunicipalityID INT(11);

-- Add municipality if not existing in the kommune table
REPLACE INTO kommune (KommuneNumber, KommuneName, Sone)
SELECT CompanyMunicipality, CompanyMunicipalityName, 1
FROM company
WHERE CompanyMunicipality IS NOT NULL OR CompanyMunicipalityName IS NOT NULL OR CompanyMunicipality != '' OR CompanyMunicipalityName != '';

-- Populate this field with values from kommune table, based on current CompanyMunicipality number in company table.
UPDATE company AS c INNER JOIN kommune AS k
ON k.KommuneNumber = c.CompanyMunicipality
SET c.CompanyMunicipalityID = k.KommuneID;

-- Remove CompanyMunicipality and CompanyMunicipalityName from company table. These are replaced now with ID field.
ALTER TABLE company
DROP CompanyMunicipality,
DROP CompanyMunicipalityName;