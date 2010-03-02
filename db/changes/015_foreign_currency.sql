CREATE TABLE `currency` (
  `CurrencyID` int(11) NOT NULL AUTO_INCREMENT,
  `CurrencyISO` varchar(3) NOT NULL DEFAULT '',
  `CurrencyName` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`CurrencyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `invoicein` ADD COLUMN `ForeignCurrencyID` CHAR(3);
ALTER TABLE `invoicein` ADD COLUMN `ForeignAmount` DECIMAL(16,2);
ALTER TABLE `invoicein` ADD COLUMN `ForeignConvRate` DECIMAL(16,4);

ALTER TABLE `invoiceinline` ADD COLUMN `ForeignCurrencyID` CHAR(3);
ALTER TABLE `invoiceinline` ADD COLUMN `ForeignAmount` DECIMAL(16,2);
ALTER TABLE `invoiceinline` ADD COLUMN `ForeignConvRate` DECIMAL(16,4);

ALTER TABLE `voucher` ADD COLUMN `ForeignCurrencyID` CHAR(3);
ALTER TABLE `voucher` ADD COLUMN `ForeignAmount` DECIMAL(16,2);
ALTER TABLE `voucher` ADD COLUMN `ForeignConvRate` DECIMAL(16,4);

ALTER TABLE `exchange` MODIFY COLUMN `Amount` DECIMAL(16,4);
