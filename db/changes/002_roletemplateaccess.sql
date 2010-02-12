#update accountplan set EnableReport1=1, EnableReport2=1,EnableReport3=1,EnableReport4=1,EnableReport5=1 where AccountPlanID >= 1000 and AccountPlanID <= 10000;
#alter table account add Sort int default '10';

#INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='bank', Template='tabsimple', AccessLevel=3;
#INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='setup', Template='edit', AccessLevel=3;

insert into roletemplate set Interface='lodo', Module='bank',  Template='tabsimple', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;
insert into roletemplate set Interface='lodo', Module='setup', Template='edit', 		AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;
