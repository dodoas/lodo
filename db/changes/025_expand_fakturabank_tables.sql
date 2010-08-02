-- Add new fakturabank fields to lodo's fakturabank data holding tables

ALTER TABLE `fakturabanktransaction` ADD COLUMN `InvoiceNumber` varchar(255) DEFAULT NULL AFTER `KID`;
ALTER TABLE `fakturabanktransaction` ADD COLUMN `IsSplit` tinyint(1) DEFAULT NULL AFTER `InvoiceNumber`;
ALTER TABLE `fakturabanktransaction` ADD COLUMN `ParentID` bigint(20) DEFAULT NULL AFTER `IsSplit`;
ALTER TABLE `fakturabanktransaction` ADD COLUMN `TransactionType` varchar(2) DEFAULT NULL AFTER `ParentID`;
ALTER TABLE `fakturabanktransaction` ADD COLUMN `CounterpartName` varchar(255) DEFAULT NULL AFTER `TransactionType`;

