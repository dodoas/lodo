alter table accountplan add IBAN varchar(255);
alter table bankvotingline add InvoiceNumber varchar(255);
alter table accountplan change ReskontroAccountPlanType ReskontroAccountPlanType varchar(15);
