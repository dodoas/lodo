-- Add AltinnFieldsUpdatedBy and AltinnFieldsUpdatedAt to salary table
ALTER TABLE salary
ADD AltinnFieldsUpdatedBy INT(11) NOT NULL,
ADD AltinnFieldsUpdatedAt timestamp NOT NULL;
