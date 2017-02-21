UPDATE fakturabankinvoicein
SET SupplierPartyIndentificationSchemeID = 'NO:EMAIL'
WHERE SupplierPartyIndentificationSchemeID = 'FAKTURABANK:EMAIL';

UPDATE fakturabankinvoicein
SET CustomerPartyIndentificationSchemeID = 'NO:EMAIL'
WHERE CustomerPartyIndentificationSchemeID = 'FAKTURABANK:EMAIL';

UPDATE fakturabankinvoiceout
SET SupplierPartyIndentificationSchemeID = 'NO:EMAIL'
WHERE SupplierPartyIndentificationSchemeID = 'FAKTURABANK:EMAIL';

UPDATE fakturabankinvoiceout
SET CustomerPartyIndentificationSchemeID = 'NO:EMAIL'
WHERE CustomerPartyIndentificationSchemeID = 'FAKTURABANK:EMAIL';

UPDATE fakturabankscheme
SET SchemeType = 'NO:EMAIL'
WHERE SchemeType = 'FAKTURABANK:EMAIL';

UPDATE fakturabanktransactionrelation
SET InvoiceSupplierIdentitySchemeID = 'NO:EMAIL'
WHERE InvoiceSupplierIdentitySchemeID = 'FAKTURABANK:EMAIL';

UPDATE fakturabanktransactionrelation
SET InvoiceCustomerIdentitySchemeID = 'NO:EMAIL'
WHERE InvoiceCustomerIdentitySchemeID = 'FAKTURABANK:EMAIL';
