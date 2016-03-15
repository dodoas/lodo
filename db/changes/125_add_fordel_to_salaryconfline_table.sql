-- Add send to altinn to on salaryconfline
ALTER TABLE salaryconfline
ADD Fordel varchar(50) DEFAULT '',
ADD MandatoryTaxSubtraction smallint(1) DEFAULT 0;
UPDATE salaryconfline set MandatoryTaxSubtraction = 0;

-- Add send to altinn to on salaryline
ALTER TABLE salaryline
ADD Fordel varchar(50) DEFAULT '',
ADD MandatoryTaxSubtraction smallint(1) DEFAULT 0;
UPDATE salaryconfline set MandatoryTaxSubtraction = 0;
