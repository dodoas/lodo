ALTER TABLE `salary` ADD COLUMN `FakturabankID` int(11) AFTER `Comment`;
ALTER TABLE `salary` ADD COLUMN `FakturabankPersonID` int(11) AFTER `FakturaBankID`;
ALTER TABLE `salary` ADD COLUMN `FakturabankDateTime` datetime AFTER `FakturabankPersonID`;

ALTER TABLE `accountplan` ADD COLUMN `FirstName` varchar(50) AFTER `AccountName`;
ALTER TABLE `accountplan` ADD COLUMN `LastName` varchar(50) AFTER `FirstName`;
