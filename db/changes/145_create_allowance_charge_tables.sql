ALTER TABLE vat
ADD Category varchar(5);

UPDATE vat SET Category = 'Z' WHERE VatID IN (10, 30, 32, 40, 60, 62);
UPDATE vat SET Category = 'S' WHERE VatID IN (11, 41);
UPDATE vat SET Category = 'H' WHERE VatID IN (12, 42);
UPDATE vat SET Category = 'AA' WHERE VatID IN (13, 43);

CREATE TABLE IF NOT EXISTS invoiceallowancecharge (
  InvoiceAllowanceChargeID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  AllowanceChargeID int(11) NOT NULL,
  InvoiceType varchar(10) NOT NULL DEFAULT 'out', -- 'out' or 'in'
  InvoiceID int(11) NOT NULL,
  ChargeIndicator tinyint NOT NULL DEFAULT 0,
  AllowanceChargeReason varchar(255),
  Amount decimal(16,2) NOT NULL,
  VatPercent decimal(16,2) NOT NULL,
  VatID int(11) NULL
);

CREATE TABLE IF NOT EXISTS invoicelineallowancecharge (
  InvoiceLineAllowanceChargeID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  AllowanceChargeType varchar(10) NOT NULL DEFAULT 'line', -- 'line' or 'price'
  InvoiceType varchar(10) NOT NULL DEFAULT 'out', -- 'out' or 'in'
  InvoiceLineID int(11) NOT NULL,
  ChargeIndicator tinyint NOT NULL DEFAULT 0,
  AllowanceChargeReason varchar(255),
  Amount decimal(16,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS allowancecharge (
  AllowanceChargeID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Active tinyint(4) NOT NULL DEFAULT 1,
  ChargeIndicator tinyint NOT NULL DEFAULT 0,
  InAccountPlanID int(11) NOT NULL,
  OutAccountPlanID int(11) NOT NULL,
  Reason varchar(255),
  Amount decimal(16,2) NOT NULL,
  InVatPercent decimal(16,2) NOT NULL,
  InVatID int(11) NULL,
  OutVatPercent decimal(16,2) NOT NULL,
  OutVatID int(11) NULL
);

-- Down migration code

-- ALTER TABLE vat
-- DROP Category;
--
-- DROP TABLE invoiceallowancecharge;
-- DROP TABLE invoicelineallowancecharge;
-- DROP TABLE allowancecharge;
