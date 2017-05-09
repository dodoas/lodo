-- Find NO:EMAIL FakturabankSchemeID;
SET @emailid = (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:EMAIL');

-- Create FirmaIDs for Employee Accountplans which have OrgNumber, but no appropriate FirmaID
-- In case we have a NO:EMAIL FirmaID with different value from ap.Email, this will create a new additional one;
INSERT INTO accountplanscheme (AccountPlanID, FakturabankSchemeID, SchemeValue, CountryCode)
SELECT ap.AccountPlanID, @emailid, ap.Email, 'NO'
FROM accountplan ap
LEFT JOIN accountplanscheme aps ON
  ap.AccountPlanID = aps.AccountPlanID AND
  ap.Email = aps.SchemeValue AND
  aps.FakturabankSchemeID = @emailid
WHERE
  ap.Email != '' AND ap.Email is not null AND
  aps.AccountPlanSchemeID is null AND
  ap.AccountPlanType = 'employee';


UPDATE fakturabankscheme SET SchemeType = REPLACE(SchemeType, ':TELEPHONE', ':PHONE');

-- Find NO:PHONE FakturabankSchemeID;
SET @phoneid = (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:PHONE');

-- Create FirmaIDs for Employee Accountplans which have OrgNumber, but no appropriate FirmaID
-- In case we have a NO:PHONE FirmaID with different value from ap.Phone, this will create a new additional one;
INSERT INTO accountplanscheme (AccountPlanID, FakturabankSchemeID, SchemeValue, CountryCode)
SELECT ap.AccountPlanID, @phoneid, ap.Phone, 'NO'
FROM accountplan ap
LEFT JOIN accountplanscheme aps ON
  ap.AccountPlanID = aps.AccountPlanID AND
  ap.Phone = aps.SchemeValue AND
  aps.FakturabankSchemeID = @phoneid
WHERE
  ap.Phone != '' AND ap.Phone is not null AND
  aps.AccountPlanSchemeID is null AND
  ap.AccountPlanType = 'employee';

