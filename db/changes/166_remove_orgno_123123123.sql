UPDATE accountplan SET OrgNumber = '' WHERE OrgNumber = '123123123';
UPDATE accountplan SET VatNumber = '' WHERE VatNumber = '123123123';
DELETE FROM accountplanscheme WHERE SchemeValue = '123123123' AND FakturabankSchemeID IN ((SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:ORGNR'), (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:VAT'), 0, NULL);

UPDATE accountline SET ReskontroOrgNumber = '' WHERE ReskontroOrgNumber = '123123123';

UPDATE fakturabanktransactionrelation SET AccountPlanOrgNumber = NULL WHERE AccountPlanOrgNumber = 123123123;

UPDATE fakturabankinvoiceout SET CustomerPartyIndentification = '' WHERE CustomerPartyIndentification = '123123123';
UPDATE invoiceout SET IOrgNo = '' WHERE IOrgNo = '123123123';
UPDATE recurringout SET IOrgNo = '' WHERE IOrgNo = '123123123';
