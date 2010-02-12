alter table company add SWIFT varchar(255);

alter table installation add Version varchar(30);

alter table weeklysaleconf add VoucherType char(3) default 'O';
alter table weeklysale add VoucherType char(3) default 'O';
alter table weeklysale change CreatedDate InsertedDateTime datetime;
alter table weeklysale change ChangedByPersonID UpdatedByPersonID int;
alter table weeklysale change CreatedByPersonID InsertedByPersonID int;
alter table weeklysaleconf change CreatedByPersonID InsertedByPersonID int;
alter table weeklysaleconf add InsertedDateTime datetime;
alter table weeklysaleconf add UpdatedByPersonID int;
alter table accountplan add AccountLineFreeTextMatch varchar(255);

update weeklysale set VoucherType='K';
update weeklysaleconf set VoucherType='K';

alter table account add includeinsaldo smallint;

alter table company add VoucherWeeklysaleNumber bigint default '1000';
alter table company drop EnableAutoWeeklysaleSequence;
alter table company add EnableWeeklysaleNumberSequence smallint;

insert into confmenues (MenuName, MenuValue, MenuChoice, Active, LanguageID) values ('VoucherType', 'O', 'Ukeomsetning', 1, 'no');

alter table bankvotingperiod add unique(AccountID, Period);
alter table bankvotingperiod add Comment varchar(255);
alter table bankvotingperiod add Description varchar(255);
alter table bankvotingperiod add Locked smallint;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='bank', Template='accountperiodcomment', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='bank', Template='accountperiodcomment', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;


INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='report', Template='hovedbokvoucherprint', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='report', Template='hovedbokvoucherprint', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='report', Template='reskontrovoucherprint', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='report', Template='reskontrovoucherprint', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

CREATE TABLE `borettslagarsoppgjor` (
  `BorettslagarsoppgjorID` int(11) NOT NULL auto_increment,
  `BorettslagID` int(11) default NULL,
  `TS` timestamp NULL default NULL,
  `Arstall` int(11) default NULL,
  `ProsentInntekt` decimal(12,2) default NULL,
  `Inntekter` decimal(12,2) default NULL,
  `Utgifter` decimal(12,2) default NULL,
  `Ligningsverdi` decimal(12,2) default NULL,
  `Formue` decimal(12,2) default NULL,
  `Gjeld` decimal(12,2) default NULL,
  `Kostpris` decimal(12,2) default NULL,
  PRIMARY KEY  (`BorettslagarsoppgjorID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `weeklysalegroupconf` (
  `WeeklySaleGroupConfID` int(11) NOT NULL auto_increment,
  `WeeklySaleConfID` int(11) default NULL,
  `Group1Name` varchar(25) default NULL,
  `Group1Account` int(11) default NULL,
  `Group2Name` varchar(25) default NULL,
  `Group2Account` int(11) default NULL,
  `Group3Name` varchar(25) default NULL,
  `Group3Account` int(11) default NULL,
  `Group4Name` varchar(25) default NULL,
  `Group4Account` int(11) default NULL,
  `Group5Name` varchar(25) default NULL,
  `Group5Account` int(11) default NULL,
  `Group6Name` varchar(25) default NULL,
  `Group6Account` int(11) default NULL,
  `Group7Name` varchar(25) default NULL,
  `Group7Account` int(11) default NULL,
  `Group8Name` varchar(25) default NULL,
  `Group8Account` int(11) default NULL,
  `Group9Name` varchar(25) default NULL,
  `Group9Account` int(11) default NULL,
  `Group10Name` varchar(25) default NULL,
  `Group10Account` int(11) default NULL,
  `Group11Name` varchar(25) default NULL,
  `Group11Account` int(11) default NULL,
  `Group12Name` varchar(25) default NULL,
  `Group12Account` int(11) default NULL,
  `Group13Name` varchar(25) default NULL,
  `Group13Account` int(11) default NULL,
  `Group14Name` varchar(25) default NULL,
  `Group14Account` int(11) default NULL,
  `Group15Name` varchar(25) default NULL,
  `Group15Account` int(11) default NULL,
  `Group16Name` varchar(25) default NULL,
  `Group16Account` int(11) default NULL,
  `Group17Name` varchar(25) default NULL,
  `Group17Account` int(11) default NULL,
  `Group18Name` varchar(25) default NULL,
  `Group18Account` int(11) default NULL,
  `Group19Name` varchar(25) default NULL,
  `Group19Account` int(11) default NULL,
  `Group20Name` varchar(25) default NULL,
  `Group20Account` int(11) default NULL,
  `Type` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Group1ProjectID` int(11) default NULL,
  `Group2ProjectID` int(11) default NULL,
  `Group3ProjectID` int(11) default NULL,
  `Group4ProjectID` int(11) default NULL,
  `Group5ProjectID` int(11) default NULL,
  `Group6ProjectID` int(11) default NULL,
  `Group7ProjectID` int(11) default NULL,
  `Group8ProjectID` int(11) default NULL,
  `Group9ProjectID` int(11) default NULL,
  `Group10ProjectID` int(11) default NULL,
  `Group11ProjectID` int(11) default NULL,
  `Group12ProjectID` int(11) default NULL,
  `Group13ProjectID` int(11) default NULL,
  `Group14ProjectID` int(11) default NULL,
  `Group15ProjectID` int(11) default NULL,
  `Group16ProjectID` int(11) default NULL,
  `Group17ProjectID` int(11) default NULL,
  `Group18ProjectID` int(11) default NULL,
  `Group19ProjectID` int(11) default NULL,
  `Group20ProjectID` int(11) default NULL,
  `Group1DepartmentID` int(11) default NULL,
  `Group2DepartmentID` int(11) default NULL,
  `Group3DepartmentID` int(11) default NULL,
  `Group4DepartmentID` int(11) default NULL,
  `Group5DepartmentID` int(11) default NULL,
  `Group6DepartmentID` int(11) default NULL,
  `Group7DepartmentID` int(11) default NULL,
  `Group8DepartmentID` int(11) default NULL,
  `Group9DepartmentID` int(11) default NULL,
  `Group10DepartmentID` int(11) default NULL,
  `Group11DepartmentID` int(11) default NULL,
  `Group12DepartmentID` int(11) default NULL,
  `Group13DepartmentID` int(11) default NULL,
  `Group14DepartmentID` int(11) default NULL,
  `Group15DepartmentID` int(11) default NULL,
  `Group16DepartmentID` int(11) default NULL,
  `Group17DepartmentID` int(11) default NULL,
  `Group18DepartmentID` int(11) default NULL,
  `Group19DepartmentID` int(11) default NULL,
  `Group20DepartmentID` int(11) default NULL,
  PRIMARY KEY  (`WeeklySaleGroupConfID`),
  KEY `WeeklySaleConfID` (`WeeklySaleConfID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
