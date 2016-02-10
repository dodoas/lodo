-- Add send to altinn to on salaryconfline
ALTER TABLE salaryconfline
ADD SendToAltinn int(1) DEFAULT 1;

UPDATE salaryconfline set SendToAltinn = 1;
UPDATE salaryconfline set SendToAltinn = 0 where SalaryText in ('Påleggstrekk', 'Diverse trekk', 'Forskudd');

-- Add send to altinn to on salaryline
ALTER TABLE salaryline
ADD SendToAltinn int(1) DEFAULT 1;

UPDATE salaryline set SendToAltinn = 1;
UPDATE salaryline set SendToAltinn = 0 where SalaryText in ('Påleggstrekk', 'Diverse trekk', 'Forskudd');
