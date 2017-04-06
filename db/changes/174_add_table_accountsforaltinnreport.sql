CREATE TABLE IF NOT EXISTS accountsforaltinnreport(
  ID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Active tinyint(4) NOT NULL DEFAULT 1,
  AccountPlanID int(11) NOT NULL
);

ALTER TABLE salaryreportaccount
ADD COLUMN SalaryJournalID INT(11) NULL DEFAULT NULL;
