UPDATE `salaryconfline`
SET `MandatoryTaxSubtraction` = 1
WHERE `SalaryDescription` LIKE  'elektroniskKommunikasjon'
AND `MandatoryTaxSubtraction` != 1
