ALTER TABLE `invoiceout` ADD COLUMN `Locked` smallint(6) AFTER FakturaBankID;
ALTER TABLE `invoiceout` ADD COLUMN `FakturabankPersonID` int(11) AFTER FakturaBankID;
ALTER TABLE `invoiceout` ADD COLUMN `FakturabankDateTime` datetime AFTER FakturabankPersonID;
