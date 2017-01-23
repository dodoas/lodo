ALTER TABLE invoiceinline ADD COLUMN TotalWithoutTax decimal(16,2), ADD COLUMN TotalWithTax decimal(16,2);

UPDATE invoiceinline SET TotalWithoutTax = UnitCustPrice * QuantityDelivered + IFNULL((SELECT SUM(IF(ChargeIndicator, Amount, -Amount)) FROM invoicelineallowancecharge WHERE AllowanceChargeType = 'line' AND InvoiceType = 'in' AND InvoiceLineID = invoiceinline.LineID), 0);
UPDATE invoiceinline SET TotalWithTax = TotalWithoutTax * (1 + Vat / 100);
UPDATE invoiceinline SET TaxAmount = TotalWithTax - TotalWithoutTax;
