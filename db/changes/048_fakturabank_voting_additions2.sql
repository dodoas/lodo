ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `InvoiceNumber` varchar(255) DEFAULT NULL AFTER `Ref`;
ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `InvoiceSupplierIdentity` varchar(255) DEFAULT NULL AFTER `KID`;
ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `InvoiceSupplierIdentitySchemeID` varchar(255) DEFAULT NULL AFTER `InvoiceSupplierIdentity`;
ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `InvoiceCustomerIdentity` varchar(255) DEFAULT NULL AFTER `InvoiceSupplierIdentitySchemeID`;
ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `InvoiceCustomerIdentitySchemeID` varchar(255) DEFAULT NULL AFTER `InvoiceCustomerIdentity`;
ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `InvoiceType` varchar(40) DEFAULT NULL AFTER `InvoiceCustomerIdentitySchemeID`;
