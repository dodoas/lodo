-- Populate accountplanscheme table with firma id for all the companies that have NO:ORGNR set
-- excludes all of those that already have the firma id set for NO:ORGNR
-- This migration can be run multiple times, if needed
INSERT INTO accountplanscheme (AccountPlanID, FakturabankSchemeID, SchemeValue)
SELECT ap.AccountPlanID, (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:ORGNR' LIMIT 1) as FakturabankSchemeID, ap.OrgNumber as SchemeValue
FROM accountplan ap
WHERE ap.OrgNumber IS NOT NULL AND
      ap.OrgNumber <> '' AND
      ap.AccountPlanID NOT IN (
        SELECT ap.AccountPlanID
        FROM accountplan ap JOIN accountplanscheme aps ON ap.AccountPlanID = aps.AccountPlanID
        WHERE ap.OrgNumber IS NOT NULL AND ap.OrgNumber <> ''
      )
;
