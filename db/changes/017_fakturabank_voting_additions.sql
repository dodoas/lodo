ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `TransactionAmount` decimal(16,5) DEFAULT NULL;
ALTER TABLE `fakturabanktransactionrelation` ADD COLUMN `TransactionCurrency` varchar(4) DEFAULT NULL;
