UPDATE company SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE company SET VCountry = (select Code from country where LocalName = VCountry) where VCountry != '' and VCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = VCountry) IS NOT NULL;
UPDATE company SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE company change column ICountry ICountryCode varchar(2) NOT NULL default 'NO';
ALTER TABLE company change column DCountry DCountryCode varchar(2);
ALTER TABLE company change column VCountry VCountryCode varchar(2);


UPDATE accountplan SET Country = (select Code from country where LocalName = Country) where Country != '' and Country IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = Country) IS NOT NULL;
ALTER TABLE accountplan change column Country CountryCode varchar(2) NOT NULL default 'NO';


UPDATE installation SET VCountry = (select Code from country where LocalName = VCountry) where VCountry != '' and VCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = VCountry) IS NOT NULL;
ALTER TABLE installation change column VCountry VCountryCode varchar(2);


UPDATE invoiceconfig SET Country = (select Code from country where LocalName = Country) where Country != '' and Country IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = Country) IS NOT NULL;
ALTER TABLE invoiceconfig change column Country CountryCode varchar(2);


UPDATE invoicein SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE invoicein SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE invoicein change column ICountry ICountryCode varchar(2);
ALTER TABLE invoicein change column DCountry DCountryCode varchar(2);


UPDATE invoiceout SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE invoiceout SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE invoiceout change column ICountry ICountryCode varchar(2);
ALTER TABLE invoiceout change column DCountry DCountryCode varchar(2);


UPDATE language SET Country = (select Code from country where LocalName = Country) where Country != '' and Country IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = Country) IS NOT NULL;
ALTER TABLE language change column Country CountryCode varchar(2);

UPDATE orderpurchase SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE orderpurchase SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE orderpurchase change column DCountry DCountryCode varchar(2);
ALTER TABLE orderpurchase change column ICountry ICountryCode varchar(2);


UPDATE ordersubscription SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE ordersubscription SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE ordersubscription change column DCountry DCountryCode varchar(2);
ALTER TABLE ordersubscription change column ICountry ICountryCode varchar(2);


UPDATE person SET Country = (select Code from country where LocalName = Country) where Country != '' and Country IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = Country) IS NOT NULL;
UPDATE person SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE person SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE person change column Country CountryCode varchar(2);
ALTER TABLE person change column DCountry DCountryCode varchar(2);
ALTER TABLE person change column ICountry ICountryCode varchar(2);


UPDATE recurringout SET ICountry = (select Code from country where LocalName = ICountry) where ICountry != '' and ICountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = ICountry) IS NOT NULL;
UPDATE recurringout SET DCountry = (select Code from country where LocalName = DCountry) where DCountry != '' and DCountry IS NOT NULL AND (SELECT Code FROM country WHERE LocalName = DCountry) IS NOT NULL;
ALTER TABLE recurringout change column ICountry ICountryCode varchar(2);
ALTER TABLE recurringout change column DCountry DCountryCode varchar(2);
