alter table setup add CompanyID int default '0';
alter table setup add PersonID int default '0';

update accountplan set EnableReport1=1, EnableReport2=1,EnableReport3=1,EnableReport4=1,EnableReport5=1 where AccountPlanID >= 1000 and AccountPlanID <= 10000;
alter table account add Sort int default '10';

alter table accountplan add ReskontroAccountPlanType char(9);
alter table accountplan add AccountPlanType char(9);

update accountplan set AccountPlanType='balance', ReskontroAccountPlanType='customer' where AccountPlanID=1500;
update accountplan set AccountPlanType='balance', ReskontroAccountPlanType='supplier' where AccountPlanID=2400;
update accountplan set AccountPlanType='balance', ReskontroAccountPlanType='employee' where AccountPlanID=2930;

update accountplan set AccountPlanType='balance'   where AccountPlanID >= 1000 and AccountPlanID < 3000;
update accountplan set AccountPlanType='result'    where AccountPlanID >= 3000 and AccountPlanID < 10000;
update accountplan set AccountPlanType='employee'  where AccountPlanID < 1000;
update accountplan set AccountPlanType='customer'  where AccountPlanID >= 10000 and AccountPlanID < 50000;
update accountplan set AccountPlanType='supplier'  where AccountPlanID >= 50000;

alter table confmenues add sort int;
delete from confmenues where MenuName='AccountPlanType';
insert into confmenues set MenuName='AccountPlanType', MenuValue='balance' , MenuChoice='Balanse', Active=1, LanguageID='no', Sort=1;
insert into confmenues set MenuName='AccountPlanType', MenuValue='result'  , MenuChoice='Resultat', Active=1, LanguageID='no', Sort=2;
insert into confmenues set MenuName='AccountPlanType', MenuValue='customer', MenuChoice='Kunde', Active=1, LanguageID='no', Sort=3;
insert into confmenues set MenuName='AccountPlanType', MenuValue='supplier', MenuChoice='Leverand&oslash;r', Active=1, LanguageID='no', Sort=4;
insert into confmenues set MenuName='AccountPlanType', MenuValue='employee', MenuChoice='Ansatt', Active=1, LanguageID='no', Sort=5;

alter table accountplan add UpdatedByPersonID int;
alter table accountplan change CreatedDate InsertedDateTime datetime;
alter table accountplan change CreatedByPersonID InsertedByPersonID int;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='accountplan', Template='employee', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='accountplan', Template='employee', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='bank', Template='tabsimple', AccessLevel=3;
INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='setup', Template='edit', AccessLevel=3;

alter table company add PostAccount varchar(255);
alter table company add VATDuty smallint;
