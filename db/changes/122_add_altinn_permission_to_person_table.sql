-- Add altinn permission fields to person
ALTER TABLE person
ADD AltinnSalaryAccess int(1) DEFAULT 0;
