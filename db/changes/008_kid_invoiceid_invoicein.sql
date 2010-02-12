alter table voucher change Reference KID varchar(40) not null; #This is critical but makes it easier
alter table invoiceout add CustomerAccountPlanID bigint;
update invoiceout set CustomerAccountPlanID = AccountPlanID;
alter table invoiceout add SupplierAccountPlanID bigint;
alter table invoiceout add ExternalID int not null;
alter table invoiceout add JournalID int not null;
alter table invoiceoutline change CreatedByPersonID InsertedByPersonID bigint not null;
alter table invoiceoutline change ChangedByPersonID UpdatedByPersonID bigint not null;
alter table invoiceoutline change CreatedDateTime InsertedDateTime datetime not null;
alter table voucher add InvoiceID varchar(40) not null;

alter table company add isAccountCustomer smallint;
alter table company add isAccountSupplier smallint;
alter table company add isAccountProspect smallint;
update company set isAccountCustomer=1;
update company set isAccountProspect=1;

alter table person add isAccountCustomer smallint;
alter table person add isAccountSupplier smallint;
alter table person add isAccountProspect smallint;
update person set isAccountCustomer=1;
update person set isAccountProspect=1;

alter table product add UnitCustPriceCurrencyID char(4) default 'NOK';
alter table session add ScreenSize char(1) default 'L'; #S = mobile, M=smartphone, L=Laptop/PC
update session set ScreenSize='L';
alter table arbeidsgiveravgift drop Percent62;

alter table accountline add UpdatedByPersonID int;
alter table accountline add InsertedByPersonID int;
alter table accountline add InsertedDateTime datetime;

alter table account add UpdatedByPersonID int;
alter table account add InsertedByPersonID int;
alter table account add InsertedDateTime datetime;

alter table person add key(Email);
alter table person add key(ExternalID);

insert into setup set Module='kid', Name='accountplanid', Value='0';
insert into setup set Module='kid', Name='invoiceid', Value='0';
insert into setup set Module='kid', Name='pad', Value='5';
insert into setup set Module='invoice', Name='outgoing', Value='lodo';

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='fakturabank', Template='listoutgoing', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='fakturabank', Template='listoutgoing', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='fakturabank', Template='listincoming', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='fakturabank', Template='listincoming', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='remittance', Template='listincoming', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='remittance', Template='listincoming', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='remittance', Template='listincoming', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='remittance', Template='listincoming', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

alter table invoiceout modify InvoiceID bigint not null auto_increment;
alter table invoiceoutline modify InvoiceID bigint;
alter table person modify PersonID bigint not null auto_increment;
alter table company modify CompanyID bigint not null auto_increment;

alter table accountplan add isAccountCustomer smallint;
alter table accountplan add isAccountSupplier smallint;
alter table accountplan add isAccountResult smallint;
alter table accountplan add isAccountBalance smallint;
alter table accountplan add isAccountEmployee smallint;

update accountplan set isAccountCustomer=1 where AccountPlanType='customer';
update accountplan set isAccountSupplier=1 where AccountPlanType='supplier';
update accountplan set isAccountResult  =1   where AccountPlanType='result';
update accountplan set isAccountBalance =1  where AccountPlanType='balance';
update accountplan set isAccountEmployee=1 where AccountPlanType='employee';

alter table voucher modify VoucherID bigint not null not null auto_increment;
alter table voucher modify JournalID bigint not null;
alter table voucher modify DepartmentID bigint not null;
alter table voucher modify ProjectID bigint not null;
alter table voucher modify InsertedByPersonID bigint not null;
alter table voucher modify AutomaticFromVoucherID bigint not null;
alter table voucher modify VatID bigint not null;
alter table voucher modify UpdatedByPersonID bigint not null;

alter table voucher modify AccountLineID bigint not null;
alter table voucher modify AutomaticBalanceID bigint not null;
alter table voucher modify AutoFromWeeklySale bigint not null;
alter table voucher modify AutomaticVatVoucherID bigint not null;

alter table accountline modify AccountLineID bigint not null auto_increment;
alter table accountline modify AccountID bigint not null;
alter table account modify AccountID bigint not null auto_increment;

CREATE TABLE invoicein (
  ID bigint NOT NULL auto_increment,
  InvoiceID bigint NOT NULL,
  JournalID bigint NOT NULL,
  SupplierAccountPlanID bigint default NULL,
  CustomerAccountPlanID bigint default NULL,
  `TotalCustPrice` decimal(16,2) NOT NULL default '0.00',
  `TotalCostPrice` decimal(16,2) NOT NULL default '0.00',
  `Period` varchar(7) default NULL,
  `InvoiceDate` date NOT NULL default '0000-00-00',
  `DueDate` date NOT NULL default '0000-00-00',
  `DeliveryDate` date NOT NULL default '0000-00-00',
  `OrderDate` date NOT NULL default '0000-00-00',
  `RequiredDate` datetime default NULL,
  `PaymentDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `CurrencyID` char(4) default 'NOK',
  `KID` varchar(30) default NULL,
  `ContactPersonID` int(11) default '0',
  `SalePersonID` int(11) default '0',
  `ResponsiblePersonID` int(11) default '0',
  `Status` varchar(15) default NULL,
  `ProjectID` int(11) NOT NULL default '0',
  `ProjectNameInternal` varchar(255) default NULL,
  `CommentCustomer` text,
  `CommentInternal` text,
  `DeliveryCondition` varchar(255) NOT NULL default '',
  `PaymentCondition` varchar(255) NOT NULL default '',
  `EnableTaxFree` smallint(6) default NULL,
  `TotalVat` decimal(16,2) NOT NULL default '0.00',
  `RefCustomer` varchar(255) default '',
  `ProjectNameCustomer` varchar(255) NOT NULL default '',
  `RefInternal` varchar(255) default '',
  `IAddressID` int(11) default NULL,
  `IName` varchar(255) default NULL,
  `IAddress` varchar(255) default NULL,
  `IAddressNumber` varchar(10) default NULL,
  `IZipCode` varchar(4) default NULL,
  `ICity` varchar(255) default NULL,
  `ICountry` varchar(255) default NULL,
  `IPoBoxZipCode` varchar(255) default NULL,
  `IPoBoxZipCodeCity` varchar(255) default NULL,
  `IPoBox` varchar(255) NOT NULL default '',
  `IPoBoxCity` varchar(255) NOT NULL default '',
  `IEmail` varchar(50) default '',
  `DName` varchar(255) default NULL,
  `DAddress` varchar(255) default NULL,
  `DAddressNumber` varchar(10) default NULL,
  `DZipCode` varchar(4) default NULL,
  `DCity` varchar(255) default NULL,
  `DCountry` varchar(255) default NULL,
  `DAddressID` int(11) default NULL,
  `DPoBoxZipCode` varchar(255) default NULL,
  `DPoBoxZipCodeCity` varchar(255) default NULL,
  `DPoBox` varchar(255) default NULL,
  `DPoBoxCity` varchar(255) default NULL,
  `DEmail` varchar(50) default '',
  `BankAccount` varchar(15) default '',
  `Phone` varchar(20) default '',
  `DateShipped` date default NULL,
  `PersonID` int(11) NOT NULL default '0',
  `MailSendtDateTime` datetime default NULL,
  `CustomerViewedDateTime` datetime default NULL,
  `CustomerViewedPersonID` int(11) default NULL,
  `MailExpireDateTime` datetime default NULL,
  `IBAN` varchar(255) default NULL,
  `SessionID` varchar(40) default NULL,
  `Locked` smallint(6) default NULL,
  `FakturabankPersonID` int(11) default NULL,
  `FakturabankDateTime` datetime default NULL,
  `RemittanceDaySequence` int(11) NOT NULL default '0',
  `RemittanceDayRecordSequence` int(11) NOT NULL default '0',
  `RemittanceApprovedDate` datetime default NULL,
  `RemittanceApprovedPersonID` int(11) default NULL,
  `RemittanceSendtDate` datetime default NULL,
  `RemittanceSendtPersonID` int(11) default NULL,
  `RemittanceStatus` varchar(20) default 'recieved',
  `RemittanceAmount` varchar(20) default NULL,
  `RemittanceApprovedDateTime` datetime default NULL,
  `RemittanceSendtDateTime` datetime default NULL,
  `BatchID` int(11) default NULL,
  `Active` tinyint(4) NOT NULL default '0',
  `InsertedByPersonID` int(11) NOT NULL default '0',
  `InsertedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (ID),
  UNIQUE  (SupplierAccountPlanID, InvoiceID)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;

CREATE TABLE invoiceinline (
  LineID bigint(11) NOT NULL auto_increment,
  LineNum int(11) NOT NULL default '0',
  ID bigint(11) NOT NULL default '0',
  ProductID int(11) default NULL,
  ProductNumber varchar(255) default NULL,
  ProductName varchar(255) default NULL,
  Comment varchar(255) default NULL,
  UnitCostPrice decimal(16,5) default NULL,
  UnitCostPriceCurrencyID char(4) default 'NOK',
  UnitCustPrice decimal(16,5) default NULL,
  UnitCustPriceCurrencyID char(4) default 'NOK',
  QuantityOrdered decimal(16,5) default NULL,
  QuantityDelivered decimal(16,5) default NULL,
  Vat decimal(16,2) default '0.00',
  VatID int(11) default NULL,
  TaxFree smallint(6) default NULL,
  TaxAmount decimal(16,5) default NULL,
  Discount decimal(16,5) default NULL,
  ProjectID bigint default NULL,
  DepartmentID bigint default NULL,
  Active smallint(6) default '1',
  TS timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  InsertedByPersonID int(11) NOT NULL default '0',
  UpdatedByPersonID int(11) NOT NULL default '0',
  InsertedDateTime datetime default NULL,
  PRIMARY KEY  (LineID),
  KEY (ID)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;
