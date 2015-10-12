ALTER TABLE salary
ADD COLUMN UpdatedAt timestamp NOT NULL,
ADD COLUMN UpdatedBy int(11) NOT NULL,
MODIFY COLUMN LockedDate timestamp;
UPDATE salary
SET UpdatedBy = CreatedByPersonID, UpdatedAt = TS;
