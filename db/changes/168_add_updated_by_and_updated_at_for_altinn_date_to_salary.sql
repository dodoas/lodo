ALTER TABLE salary
ADD COLUMN ActualPayDateUpdatedAt timestamp NOT NULL,
ADD COLUMN ActualPayDateUpdatedBy int(11) NOT NULL;

UPDATE salary
SET ActualPayDateUpdatedBy = UpdatedBy, ActualPayDateUpdatedAt = UpdatedAt
WHERE ActualPayDate IS NOT NULL AND ActualPayDate != '0000-00-00';
