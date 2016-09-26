UPDATE accountplanscheme
SET FakturabankSchemeID = (
  SELECT MIN(FakturabankSchemeID)
  FROM fakturabankscheme
  WHERE SchemeType = (
    SELECT SchemeType
    FROM fakturabankscheme
    WHERE fakturabankscheme.FakturabankSchemeID = accountplanscheme.FakturabankSchemeID
  )
);

DELETE FROM fakturabankscheme WHERE FakturabankSchemeID IN (
  SELECT fbs2.FakturabankSchemeID
  FROM (SELECT * FROM fakturabankscheme) fbs1
  JOIN (SELECT * FROM fakturabankscheme) fbs2 ON (fbs1.SchemeType = fbs2.SchemeType AND fbs2.FakturabankSchemeID > fbs1.FakturabankSchemeID)
);