ALTER TABLE accountplanscheme
ADD CountryCode varchar(20) DEFAULT '';

UPDATE accountplanscheme, fakturabankscheme
SET accountplanscheme.CountryCode = IF(SUBSTRING_INDEX(SchemeType, ':', 1) = 'FAKTURABANK', '', SUBSTRING_INDEX(SchemeType, ':', 1))
WHERE accountplanscheme.FakturabankSchemeID = fakturabankscheme.FakturabankSchemeID
