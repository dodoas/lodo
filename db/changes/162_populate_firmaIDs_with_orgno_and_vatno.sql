-- Find NO:ORGNR FakturabankSchemeID;
SET @orgnrid = (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:ORGNR');

-- Create FirmaIDs for Accountplans which have OrgNumber, but no appropriate FirmaID
-- In case we have a NO:ORGNR FirmaID with different value from ap.OrgNumber, this will create a new additional one;
INSERT INTO accountplanscheme (AccountPlanID, FakturabankSchemeID, SchemeValue, CountryCode)
SELECT ap.AccountPlanID, @orgnrid, ap.OrgNumber, 'NO'
FROM accountplan ap 
LEFT JOIN accountplanscheme aps ON
  ap.AccountPlanID = aps.AccountPlanID AND
  ap.OrgNumber = aps.SchemeValue AND
  aps.FakturabankSchemeID = @orgnrid
WHERE
  ap.OrgNumber != '' AND ap.OrgNumber is not null AND
  aps.AccountPlanSchemeID is null;

-- Update accountplans without OrgNumber which have FirmaID with NO:ORGNR;
UPDATE accountplan ap
INNER JOIN accountplanscheme aps ON
  ap.AccountPlanID = aps.AccountPlanID AND
  aps.FakturabankSchemeID = @orgnrid
SET ap.OrgNumber = aps.SchemeValue
WHERE
  ap.OrgNumber = '' OR ap.OrgNumber is null;



-- Same thing for VatNumber;

-- Find NO:VAT FakturabankSchemeID;
SET @vatid = (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:VAT');

-- Create FirmaIDs for Accountplans which have VatNumber, but no appropriate FirmaID
-- In case we have a NO:VAT FirmaID with different value from ap.VatNumber, this will create a new additional one;
INSERT INTO accountplanscheme (AccountPlanID, FakturabankSchemeID, SchemeValue, CountryCode)
SELECT ap.AccountPlanID, @vatid, ap.VatNumber, 'NO'
FROM accountplan ap
LEFT JOIN accountplanscheme aps ON
  ap.AccountPlanID = aps.AccountPlanID AND
  ap.VatNumber = aps.SchemeValue AND
  aps.FakturabankSchemeID = @vatid
WHERE
  ap.VatNumber != '' AND ap.VatNumber is not null AND
  aps.AccountPlanSchemeID is null;

-- Update accountplans without VatNumber which have FirmaID with NO:VAT
UPDATE accountplan ap
INNER JOIN accountplanscheme aps ON
  ap.AccountPlanID = aps.AccountPlanID AND
  aps.FakturabankSchemeID = @vatid
SET ap.VatNumber = aps.SchemeValue
WHERE
  ap.VatNumber = '' OR ap.VatNumber is null;