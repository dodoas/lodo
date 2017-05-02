CREATE TABLE IF NOT EXISTS voucherreconciliation (
ID int(11) NOT NULL AUTO_INCREMENT,
CreatedAt datetime DEFAULT NULL,
CreatedBy int(11) DEFAULT NULL,
PRIMARY KEY (ID)
);

ALTER TABLE voucher
ADD COLUMN VoucherReconciliationID int(11) DEFAULT NULL,
ADD COLUMN MatchNumber VARCHAR(16) DEFAULT NULL;

-- Down migration
--
-- DROP TABLE voucherreconciliation;
-- ALTER TABLE voucher
-- DROP COLUMN VoucherReconciliationID,
-- DROP COLUMN MatchNumber;
