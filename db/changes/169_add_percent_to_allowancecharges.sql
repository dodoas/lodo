ALTER TABLE allowancecharge
ADD COLUMN PercentAmount DECIMAL(16,2) DEFAULT 0,
ADD COLUMN IsPercentage BOOLEAN DEFAULT FALSE;

ALTER TABLE invoiceallowancecharge
ADD COLUMN PercentAmount DECIMAL(16,2) DEFAULT 0,
ADD COLUMN IsPercentage BOOLEAN DEFAULT FALSE;

ALTER TABLE invoicelineallowancecharge
ADD COLUMN PercentAmount DECIMAL(16,2) DEFAULT 0,
ADD COLUMN IsPercentage BOOLEAN DEFAULT FALSE;

UPDATE invoiceallowancecharge iac
SET PercentAmount = iac.Amount * 100 / (SELECT sum(UnitCustPrice * QuantityDelivered + IFNULL((SELECT sum(IF(ChargeIndicator, Amount, -Amount)) FROM invoicelineallowancecharge WHERE InvoiceLineID = il.LineID AND AllowanceChargeType = 'line' AND InvoiceType = 'out'), 0)) FROM invoiceoutline il WHERE il.InvoiceID = iac.InvoiceID)
WHERE iac.InvoiceType = 'out';

UPDATE invoiceallowancecharge iac
SET PercentAmount = iac.Amount * 100 / (SELECT sum(UnitCustPrice * QuantityDelivered + IFNULL((SELECT sum(IF(ChargeIndicator, Amount, -Amount)) FROM invoicelineallowancecharge WHERE InvoiceLineID = il.LineID AND AllowanceChargeType = 'line' AND InvoiceType = 'in'), 0)) FROM invoiceinline il WHERE il.ID = iac.InvoiceID)
WHERE iac.InvoiceType = 'in';

UPDATE invoicelineallowancecharge ilac
SET PercentAmount = ilac.Amount * 100 / (SELECT UnitCustPrice * QuantityDelivered FROM invoiceoutline il WHERE il.LineID = ilac.InvoiceLineID)
WHERE ilac.AllowanceChargeType = 'line' AND ilac.InvoiceType = 'out';

UPDATE invoicelineallowancecharge ilac
SET PercentAmount = ilac.Amount * 100 / (SELECT UnitCustPrice * QuantityDelivered FROM invoiceinline il WHERE il.LineID = ilac.InvoiceLineID)
WHERE ilac.AllowanceChargeType = 'line' AND ilac.InvoiceType = 'in';

UPDATE invoicelineallowancecharge ilac
SET PercentAmount = ilac.Amount * 100 / (SELECT UnitCustPrice FROM invoiceoutline il WHERE il.LineID = ilac.InvoiceLineID)
WHERE ilac.AllowanceChargeType = 'price' AND ilac.InvoiceType = 'out';

UPDATE invoicelineallowancecharge ilac
SET PercentAmount = ilac.Amount * 100 / (SELECT UnitCustPrice FROM invoiceinline il WHERE il.LineID = ilac.InvoiceLineID)
WHERE ilac.AllowanceChargeType = 'price' AND ilac.InvoiceType = 'in';