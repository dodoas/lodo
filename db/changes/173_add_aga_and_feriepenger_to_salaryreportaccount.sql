ALTER TABLE salaryreportaccount
  ADD Feriepengeprosent decimal(16,2) NOT NULL DEFAULT 0.00,
  ADD AGAprosent decimal(16,2) NOT NULL DEFAULT 0.00;

ALTER TABLE accountplan
  ADD Feriepengeprosent decimal(16,2) NOT NULL DEFAULT 10.20;

ALTER TABLE accountplantemplate
  ADD Feriepengeprosent decimal(16,2) NOT NULL DEFAULT 10.20;
