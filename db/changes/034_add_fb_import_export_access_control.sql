ALTER TABLE `person` ADD COLUMN `FakturabankExportInvoiceAccess` int(1) DEFAULT '0' NOT NULL AFTER isAccountProspect;
ALTER TABLE `person` ADD COLUMN `FakturabankImportInvoiceAccess` int(1) DEFAULT '0' NOT NULL AFTER isAccountProspect;
ALTER TABLE `person` ADD COLUMN `FakturabankImportBankTransactionAccess` int(1) DEFAULT '0' NOT NULL AFTER isAccountProspect;
