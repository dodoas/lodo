-- Add imported to invoicein
ALTER TABLE invoicein
ADD Imported int(1) DEFAULT 1;

UPDATE invoiceinline SET UnitCostPrice = 0 WHERE Active = 1;
