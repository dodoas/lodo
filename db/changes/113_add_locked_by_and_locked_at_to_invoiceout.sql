ALTER TABLE invoiceout
ADD COLUMN UpdatedAt datetime DEFAULT NULL,
ADD COLUMN LockedAt datetime DEFAULT NULL,
ADD COLUMN LockedBy int(11) DEFAULT NULL;
UPDATE invoiceout
SET UpdatedByPersonID = CreatedByPersonID, UpdatedAt = TS;
